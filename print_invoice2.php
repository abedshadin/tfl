<?php
// Includes: ip_blocker, auth_check, db_connect
include 'ip_blocker.php';
include 'auth_check.php';
include 'db_connect.php';

// Decrypt and validate ID
$encrypted_invoice_id = $_GET['id'] ?? '';
$invoice_id = decryptId($encrypted_invoice_id);
if ($invoice_id === false || $invoice_id <= 0) {
    error_log("Print Invoice 2 Error: Decryption failed or invalid ID. Encrypted: " . $encrypted_invoice_id);
    die("Invalid or missing Invoice ID provided in the URL.");
}

// Fetch Invoice, Vendor, and C&F Agent Data using LEFT JOIN
$sql_invoice = "SELECT
                    pi.*,
                    v.*,
                    v.name as vendor_name,
                    cnf.name as cnf_agent_name_from_db,
                    cnf.address1 as cnf_agent_address1_from_db,
                    cnf.address2 as cnf_agent_address2_from_db,
                    cnf.attn_person as cnf_agent_attn_from_db
                FROM proforma_invoices pi
                JOIN vendors v ON pi.vendor_id = v.id
                LEFT JOIN cnf_agents cnf ON pi.cnf_agent_id = cnf.id
                WHERE pi.id = ?";
$stmt = $conn->prepare($sql_invoice);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("i", $invoice_id);
if(!$stmt->execute()) die("Error executing statement: " . $stmt->error);
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();
$stmt->close();

if (!$invoice) die("Invoice not found for the specified ID.");

// --- ADDED: Fetch Products ---
$products_on_invoice = [];
$stmt_products = $conn->prepare("SELECT * FROM proforma_products WHERE invoice_id = ?");
if ($stmt_products) {
    $stmt_products->bind_param("i", $invoice_id);
    if($stmt_products->execute()){
        $products_on_invoice = $stmt_products->get_result()->fetch_all(MYSQLI_ASSOC);
    } else { error_log("Print Invoice 2 Error: Execute failed (product fetch): " . $stmt_products->error); }
    $stmt_products->close();
} else { error_log("Print Invoice 2 Error: Prepare failed (product fetch): " . $conn->error); }
// --- END ADDED ---

// Close DB connection
if ($conn->ping()) $conn->close();

// Calculations needed for the included pages
$current_date_formatted = date("F j, Y");
$lc_date_letter = 'N/A';
if (!empty($invoice['lc_date']) && ($lc_timestamp = strtotime($invoice['lc_date'])) !== false) {
    $lc_date_letter = date("d.m.Y", $lc_timestamp);
}
$pi_date_letter = 'N/A';
if (!empty($invoice['pi_date']) && ($pi_timestamp = strtotime($invoice['pi_date'])) !== false) {
    $pi_date_letter = date("d.m.Y", $pi_timestamp);
}
$commercial_invoice_date_letter = 'N/A';
if (!empty($invoice['commercial_invoice_date']) && ($ci_timestamp = strtotime($invoice['commercial_invoice_date'])) !== false) {
    $commercial_invoice_date_letter = date("d.m.Y", $ci_timestamp);
}
$bl_date_letter = 'N/A';
if (!empty($invoice['bl_date']) && ($bl_timestamp = strtotime($invoice['bl_date'])) !== false) {
    $bl_date_letter = date("d.m.Y", $bl_timestamp);
}

// --- ADDED: Calculate Product Names String ---
$product_names_string = 'N/A'; // Default
if (!empty($products_on_invoice)) {
    $product_descriptions = array_filter(array_column($products_on_invoice, 'description'));
    if (count($product_descriptions) == 1) {
        $product_names_string = $product_descriptions[0];
    } elseif (count($product_descriptions) == 2) {
        $product_names_string = implode(' and ', $product_descriptions);
    } elseif (count($product_descriptions) > 2) {
        $last_product = array_pop($product_descriptions);
        $product_names_string = implode(', ', $product_descriptions) . ', and ' . $last_product;
    }
     elseif (empty($product_descriptions)) {
         $product_names_string = 'Goods'; // Fallback if descriptions were empty
     }
} else {
     $product_names_string = 'Goods'; // Fallback if no products found
}
// --- END ADDED ---

// Helper function for checklist status
function getStatus($flag) {
    return !empty($flag) && $flag == 1 ? 'Enclosed' : 'N/A';
}
function getStatus_OtherCert($enabled, $text) {
    if (!empty($enabled) && $enabled == 1 && !empty($text)) {
        return 'Other Certificate: <b>' . htmlspecialchars($text) . '</b>';
    } elseif (!empty($enabled) && $enabled == 1) {
        return 'Other Certificate: <b>Enclosed</b>';
    } else {
        return 'Other Certificate: <b>N/A</b>';
    }
}


// --- Include the HTML View ---
include 'print_invoice2_view.php';
?>