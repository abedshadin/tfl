<?php 
// 1. IP Blocker FIRST
include 'ip_blocker.php'; 
?>
<?php 
// 2. Authentication Check SECOND
include 'auth_check.php'; 
?>
<?php
// 3. Database Connection and Functions THIRD (needed for encryptId)
include 'db_connect.php'; 

// Fetch all vendors from the database
$result = $conn->query("SELECT * FROM vendors ORDER BY name ASC");
$vendors = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Management</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; }
        #searchInput { width: 100%; padding: 12px 15px; font-size: 16px; border: 1px solid #ced4da; border-radius: 5px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 12px 20px; border: none; background-color: #28a745; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; white-space: nowrap; }
        .btn-edit { background-color: #ffc107; color: #212529; }
        .btn-products { background-color: #17a2b8; }
        .btn-invoices { background-color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f1f3f5; }
        .actions a { margin-right: 8px; text-decoration: none; padding: 6px 10px; border-radius: 4px; color: white; font-size: 0.9em;}
        .logout-link { margin-top: 20px; text-align: center; } /* Style for logout link */
    </style>
</head>
<body>
    <div class="container">
        <h1>Vendor Management</h1>
        <div class="toolbar">
            <input type="text" id="searchInput" placeholder="🔍 Search for a vendor...">
            <a href="edit_vendor.php" class="btn">+ Add New Vendor</a> </div>

            <div class="logout-link"> <a href="search_letters.php">Search Letters</a>
            
        <table id="vendorTable">
            <thead>
                <tr>
                    <th>Vendor Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vendors) > 0): ?>
                    <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><?= htmlspecialchars($vendor['name']) ?></td>
                        <td class="actions">
                            <a href="vendor_invoices.php?vendor_id=<?= encryptId($vendor['id']) ?>" class="btn-invoices">Invoices</a>
                         <a href="edit_vendor.php?id=<?= encryptId($vendor['id']) ?>" class="btn-edit">Edit Vendor</a>  <a href="manage_products.php?vendor_id=<?= encryptId($vendor['id']) ?>" class="btn-products">Products</a>
                        </td>       
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2">No vendors found. Please add one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="logout-link"> <a href="logout.php">Logout</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const vendorRows = document.querySelectorAll('#vendorTable tbody tr');

            searchInput.addEventListener('keyup', function() {
                const filterValue = searchInput.value.toUpperCase();

                vendorRows.forEach(function(row) {
                    // Make sure row is not the "No vendors found" message row
                    if (row.querySelectorAll('td').length > 1) { 
                        const vendorNameCell = row.querySelector('td:first-child');
                        if (vendorNameCell) {
                            const vendorName = vendorNameCell.textContent || vendorNameCell.innerText;
                            if (vendorName.toUpperCase().indexOf(filterValue) > -1) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>