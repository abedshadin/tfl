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
include 'db_connect.php';

// --- DECRYPTION AND CHECK ---
$encrypted_invoice_id = $_GET['id'] ?? '';
$invoice_id = decryptId($encrypted_invoice_id);

if ($invoice_id === false || $invoice_id <= 0) {
    error_log("Edit Invoice Error: Decryption failed or invalid ID received. Encrypted ID: " . $encrypted_invoice_id);
    die("Invalid or missing Invoice ID provided in the URL.");
}
// --- END DECRYPTION ---

// 4. Handle all POST logic and Data Fetching
// This script will define $message, $error_message, $invoice, $products_on_invoice, $vendor_products, $cnf_agents
include 'edit_invoice_logic.php';

// 5. Render the HTML View
// This script uses all the variables defined above
include 'edit_invoice_view.php';
?>