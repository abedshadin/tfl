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

// --- CORRECTED CHECK ---
// 4. Check if decryption failed or ID is invalid
if ($vendor_id === false || $vendor_id <= 0) { 
    header("Location: index.php"); 
    exit();
}

// Fetch vendor details
$stmt_vendor = $conn->prepare("SELECT name FROM vendors WHERE id = ?");
if ($stmt_vendor === false) { die("Error preparing vendor statement: " . $conn->error); } 
$stmt_vendor->bind_param("i", $vendor_id);
$stmt_vendor->execute();
$vendor_result = $stmt_vendor->get_result(); 
$vendor = $vendor_result->fetch_assoc();
// Check if vendor was actually found
if (!$vendor) { 
     header("Location: index.php"); 
     exit();
}
$stmt_vendor->close(); 

// Fetch all invoices for this vendor
$stmt_invoices = $conn->prepare("SELECT id, pi_number, pi_date, lc_number FROM proforma_invoices WHERE vendor_id = ? ORDER BY pi_date DESC");
if ($stmt_invoices === false) { die("Error preparing invoice statement: " . $conn->error); } 
$stmt_invoices->bind_param("i", $vendor_id);
$stmt_invoices->execute();
$invoices_result = $stmt_invoices->get_result(); 
$invoices = $invoices_result->fetch_all(MYSQLI_ASSOC);
$stmt_invoices->close(); 

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices for <?= htmlspecialchars($vendor['name']) ?></title>
     <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; border: none; background-color: #28a745; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .btn-edit { background-color: #007bff; }
        .btn-print { background-color: #6c757d; }
        a.back-link { color: #007bff; text-decoration: none; display: inline-block; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f1f3f5; }
        .actions a { margin-right: 5px; padding: 6px 10px; font-size: 0.9em; } 
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Vendor List</a>
        <h1>Invoices for: <?= htmlspecialchars($vendor['name']) ?></h1>
        <a href="create_invoice.php?vendor_id=<?= encryptId($vendor_id) ?>" class="btn">+ Create New Invoice</a>

        <hr style="margin: 25px 0;">

        <h2>Existing Invoices</h2>
        <?php if (count($invoices) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>PI Number</th>
                        <th>PI Date</th>
                        <th>L/C Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?= htmlspecialchars($invoice['pi_number']) ?: 'N/A' ?></td>
                        <td><?= htmlspecialchars($invoice['pi_date']) ?: 'N/A' ?></td>
                        <td><?= htmlspecialchars($invoice['lc_number']) ?: 'N/A' ?></td>
                        <td class="actions">
                             <a href="edit_invoice.php?id=<?= encryptId($invoice['id']) ?>" class="btn btn-edit">Edit</a>
                           <a href="print_invoice.php?id=<?= encryptId($invoice['id']) ?>" target="_blank" class="btn btn-print">Bank Print</a>
                     
                             <a href="print_invoice2.php?id=<?= encryptId($invoice['id']) ?>" target="_blank" class="btn btn-print">C& F Print</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No invoices found for this vendor.</p>
        <?php endif; ?>
    </div>
</body>
</html>