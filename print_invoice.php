<?php
// 1. IP Blocker FIRST
include 'ip_blocker.php';
?>
<?php
// 2. Authentication Check SECOND
include 'auth_check.php';
?>
<?php
// 3. Database Connection and Functions THIRD
include 'db_connect.php'; // Includes the decryptId function

// --- Base Number to Words Function ---
function numberToWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) { return false; }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        trigger_error('numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
        return false;
    }

    if ($number < 0) { return $negative . numberToWords(abs($number)); }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) { $string .= $hyphen . $dictionary[$units]; }
            break;
        case $number < 1000:
            $hundreds  = (int)($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) { $string .= $conjunction . numberToWords($remainder); }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    // Keep the original optional-decimal behavior as fallback
    if (null !== $fraction && is_numeric($fraction)) {
        if (strlen($fraction) != 2) {
             $string .= $decimal;
             $words = array();
             foreach (str_split((string)$fraction) as $num_char) {
                 $words[] = $dictionary[$num_char];
             }
             $string .= implode(' ', $words);
        } else {
             // For currency we won't rely on this path; we handle cents separately.
             if ((int)$fraction > 0) { $string .= $conjunction . $fraction . '/100'; }
        }
    }

    // Capitalize first letter of each word (we'll fix 'And' to 'and' in the currency wrapper)
    $string = ucwords($string);
    return $string;
}
// --- END Number to Words ---

// --- Currency wrapper for USD (NEW) ---
function currencyWordsUSD($amount) {
    // Normalize to 2 decimals
    $norm = number_format((float)$amount, 2, '.', '');
    list($dollars, $cents) = explode('.', $norm);

    // Words for dollars only
    $words = numberToWords((int)$dollars); // e.g., "Zero", "Eighty-Nine Thousand ..."
    // Ensure 'and' stays lower-case (numberToWords uses ucwords)
    $words = preg_replace('/\bAnd\b/u', 'and', $words);

    // Cents suffix
    // MODIFIED: Only add " Only" if cents are 0.
    $suffix = ((int)$cents > 0) ? " and {$cents}/100" : " Only";
    
    // MODIFIED: Handle the "Zero" case specifically
    if ($words === 'Zero' && (int)$cents === 0) {
        return "US Dollar Zero Only";
    }

    return "US Dollar {$words}{$suffix}";
}
// --- END Currency wrapper ---


// --- DECRYPTION AND CHECK ---
$encrypted_invoice_id = $_GET['id'] ?? '';
$invoice_id = decryptId($encrypted_invoice_id);

if ($invoice_id === false || $invoice_id <= 0) { 
    error_log("Print Invoice Error: Decryption failed or invalid ID. Encrypted: " . $encrypted_invoice_id);
    die("Invalid or missing Invoice ID provided in the URL."); 
}

// --- Fetch all data (NOW JOINING BANKS) ---
$sql_invoice = "SELECT pi.*, v.*, v.name as vendor_name, 
                       b.bank_name, b.address1, b.address2, b.address3, b.bank_acc_no 
                FROM proforma_invoices pi 
                JOIN vendors v ON pi.vendor_id = v.id
                LEFT JOIN banks b ON pi.bank_id = b.id
                WHERE pi.id = ?";
$stmt = $conn->prepare($sql_invoice);

if ($stmt === false) {
    error_log("Print Invoice Error: Prepare failed (invoice fetch): " . $conn->error);
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $invoice_id);
if(!$stmt->execute()){
    error_log("Print Invoice Error: Execute failed (invoice fetch): " . $stmt->error);
    die("Error executing statement.");
}
$result = $stmt->get_result(); 
$invoice = $result->fetch_assoc();
$stmt->close(); 

if (!$invoice) {
    die("Invoice not found for the specified ID."); 
}

$products_on_invoice = []; 
$stmt_products = $conn->prepare("SELECT * FROM proforma_products WHERE invoice_id = ?");
if ($stmt_products === false) {
    error_log("Print Invoice Error: Prepare failed (product fetch): " . $conn->error);
} else {
    $stmt_products->bind_param("i", $invoice_id);
    if($stmt_products->execute()){
        $products_on_invoice = $stmt_products->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
         error_log("Print Invoice Error: Execute failed (product fetch): " . $stmt_products->error);
    }
    $stmt_products->close(); 
}

if ($conn->ping()) {
    $conn->close();
}


// --- Robust Calculations ---
$hs_codes = [];
if (!empty($products_on_invoice)) {
    $hs_codes = array_unique(array_column($products_on_invoice, 'hs_code'));
}
$hs_code_string = !empty($hs_codes) ? implode(', ', $hs_codes) : 'N/A';

$total_goods_value = 0;
if (!empty($products_on_invoice)) {
    $total_goods_value = array_sum(array_map(function($p) {
        return (float)($p['quantity'] ?? 0) * (float)($p['unit_price'] ?? 0);
    }, $products_on_invoice));
}
$freight_cost = (float)($invoice['freight_cost'] ?? 0);
$grand_total = (float)$total_goods_value + $freight_cost;

// --- Totals for display ---
$grand_total_formatted = number_format($grand_total, 2, '.', ''); 
// --- UPDATED: Use new currencyWordsUSD function ---
$total_in_words = $invoice['amount_in_words'] ?? '';
if (empty($total_in_words)) {
    // Calculate using the new function if database is empty
    $total_in_words = currencyWordsUSD($grand_total_formatted);
}
// --- END UPDATED ---

$pi_date_formatted = 'N/A'; 
if (!empty($invoice['pi_date'])) {
    $pi_timestamp = strtotime($invoice['pi_date']);
    if ($pi_timestamp !== false) {
        $pi_date_formatted = date("d.m.Y", $pi_timestamp);
    }
}

$current_date_formatted = date("F j, Y"); // For letter

$product_names_string = 'N/A'; 
if (!empty($products_on_invoice)) {
    $product_descriptions = array_values(array_filter(array_column($products_on_invoice, 'description')));
    if (count($product_descriptions) === 1) {
        $product_names_string = $product_descriptions[0];
    } elseif (count($product_descriptions) === 2) {
        $product_names_string = implode(' and ', $product_descriptions);
    } elseif (count($product_descriptions) > 2) {
        $last_product = array_pop($product_descriptions);
        $product_names_string = implode(', ', $product_descriptions) . ', and ' . $last_product;
    } else {
        $product_names_string = 'Goods';
    }
} else {
    $product_names_string = 'Goods';
}

// --- End Calculations ---

// Include the HTML view
include 'print_invoice_view.php';
?>