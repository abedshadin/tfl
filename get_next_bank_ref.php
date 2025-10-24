<?php
// Set content type to JSON *before* any output
header('Content-Type: application/json');

// Helper function to send a JSON error and exit
function send_json_error($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// 1. IP Blocker FIRST
include 'ip_blocker.php';
// 2. Authentication Check SECOND
ob_start();
include 'auth_check.php';
$auth_output = ob_get_clean();
if (!empty($auth_output)) {
    send_json_error("Authentication failed. Your session may have expired. Please log in again.");
}

// 3. Database Connection and Functions THIRD
include 'db_connect.php'; 

$bank_id = isset($_GET['bank_id']) ? (int)$_GET['bank_id'] : 0;
$prefix = '';
$new_ref_num = 1; // Default to 1
$current_year = date("Y");

if ($bank_id <= 0) {
    send_json_error("Invalid Bank ID.");
}

if (!$conn || !$conn->ping()) {
    include 'db_connect.php'; 
     if ($conn->connect_error) {
         send_json_error("Database connection failed.");
     }
}

// 1. Get the bank's prefix
$stmt_bank = $conn->prepare("SELECT ref_prefix FROM banks WHERE id = ?");
if ($stmt_bank === false) {
    send_json_error("DB Error (prepare bank): " . $conn->error);
}
$stmt_bank->bind_param("i", $bank_id);
if (!$stmt_bank->execute()) {
    send_json_error("DB Error (execute bank): " . $stmt_bank->error);
}
$bank_result = $stmt_bank->get_result();

if ($bank = $bank_result->fetch_assoc()) {
    $prefix = $bank['ref_prefix'] ?? '';
    if (empty($prefix)) {
         send_json_error("No reference prefix is defined for this bank.");
    }
} else {
    send_json_error("Bank not found in database.");
}
$stmt_bank->close();

// 2. Construct the year-based prefix (e.g., "TFL/SCM/BBL/2025/")
$year_prefix = $prefix . $current_year . '/';

// 3. Find the highest existing number for this prefix and year
$sql_find_max = "SELECT reference_no 
                 FROM proforma_invoices 
                 WHERE reference_no LIKE ? 
                 ORDER BY CAST(SUBSTRING_INDEX(reference_no, '/', -1) AS UNSIGNED) DESC 
                 LIMIT 1";

$stmt_max = $conn->prepare($sql_find_max);
if ($stmt_max === false) {
     send_json_error("DB Error (prepare max ref): " . $conn->error);
}
$like_prefix = $year_prefix . '%'; // Search for "TFL/SCM/BBL/2025/%"
$stmt_max->bind_param("s", $like_prefix);
if (!$stmt_max->execute()) {
    send_json_error("DB Error (execute max ref): " . $stmt_max->error);
}
$max_result = $stmt_max->get_result();

if ($last_invoice = $max_result->fetch_assoc()) {
    $last_ref = $last_invoice['reference_no'];
    $last_num_str = substr($last_ref, strrpos($last_ref, '/') + 1);
    
    if (is_numeric($last_num_str)) {
        $new_ref_num = (int)$last_num_str + 1;
    }
}
$stmt_max->close();
$conn->close();

// 4. Return the new reference parts as JSON
echo json_encode(['success' => true, 'ref_prefix' => $year_prefix, 'ref_suffix' => $new_ref_num]);
exit();
?>