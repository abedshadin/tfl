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

// Decrypt the vendor_id from the URL
$encrypted_vendor_id = $_GET['vendor_id'] ?? '';
$vendor_id = decryptId($encrypted_vendor_id);

// Check if decryption failed OR ID is invalid
if ($vendor_id === false || $vendor_id <= 0) { 
    // Redirect back to index if vendor ID is invalid/missing
    error_log("Create Invoice Error: Invalid or missing vendor_id. Encrypted: " . $encrypted_vendor_id); // Log error
    header("Location: index.php"); 
    exit();
}

// Create a new blank proforma invoice record for the specified vendor
$pi_date = date('Y-m-d'); // Default to today's date

$stmt = $conn->prepare("INSERT INTO proforma_invoices (vendor_id, pi_date) VALUES (?, ?)");
if ($stmt === false) {
    die("Error preparing insert statement: " . $conn->error);
}
$stmt->bind_param("is", $vendor_id, $pi_date);

if ($stmt->execute()) {
    // Get the ID of the newly created invoice
    $new_invoice_id = $conn->insert_id;
    $stmt->close();
    $conn->close();

    // --- CRITICAL FIX: Encrypt the new ID before redirecting ---
    $encrypted_new_id = encryptId($new_invoice_id);

    if ($encrypted_new_id === false) {
        // Handle potential encryption error
        die("Error: Could not encrypt the new invoice ID."); 
    }

    // Redirect the user to the edit page with the *encrypted* new invoice ID
    header("Location: edit_invoice.php?id=" . urlencode($encrypted_new_id));
    exit();

} else {
    // Handle insertion error
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    die("Error creating new invoice: " . $error);
}
?>