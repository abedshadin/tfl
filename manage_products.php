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
// 4. Check if decryption failed OR ID is invalid *before* doing anything else
if ($vendor_id === false || $vendor_id <= 0) { 
    // Log error or handle appropriately
    error_log("Manage Products Error: Decryption failed or invalid vendor ID. Encrypted: " . $encrypted_vendor_id); 
    // Redirect back to index as the vendor context is lost
    header("Location: index.php"); 
    exit();
}
// --- END CORRECTION ---

// Fetch vendor name for the title (using the validated $vendor_id)
$vendor_name = 'Unknown Vendor'; // Default
$stmt_vendor_name = $conn->prepare("SELECT name FROM vendors WHERE id = ?");
if ($stmt_vendor_name) {
    $stmt_vendor_name->bind_param("i", $vendor_id);
    if ($stmt_vendor_name->execute()) {
        $result = $stmt_vendor_name->get_result();
        if ($row = $result->fetch_assoc()) {
            $vendor_name = $row['name'];
        } else {
             // Vendor ID was valid format but doesn't exist in DB
             error_log("Manage Products Error: Vendor not found for ID: " . $vendor_id);
             header("Location: index.php"); 
             exit();
        }
    }
    $stmt_vendor_name->close();
} else {
     error_log("Prepare failed (vendor name): " . $conn->error); // Log prepare error
     // Handle error appropriately, maybe redirect or show generic error
     die("Error fetching vendor details."); 
}


// Handle POST requests for add/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Note: $vendor_id is already validated from the GET request handling above
     if ($vendor_id === false || $vendor_id <= 0) {
         die("Critical Error: Vendor ID became invalid before processing POST."); // Safety check
    }

    $action = $_POST['action'] ?? '';
    $stmt = null; // Initialize statement variable
    
    try {
        if ($action === 'delete') {
            $product_id_to_delete = (int)($_POST['product_id'] ?? 0);
            if ($product_id_to_delete > 0) {
                $stmt = $conn->prepare("DELETE FROM vendor_products WHERE id = ? AND vendor_id = ?");
                if (!$stmt) throw new Exception("Prepare failed (delete): " . $conn->error);
                $stmt->bind_param("ii", $product_id_to_delete, $vendor_id);
            }
        } elseif ($action === 'save') {
            $product_id = (int)($_POST['product_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['default_unit_price'] ?? 0);
            $unit = $_POST['default_unit'] ?? 'Case';
            $hs_code = trim($_POST['default_hs_code'] ?? '');

            if (!empty($description)) { // Basic validation
                if ($product_id > 0) { // Update
                    $stmt = $conn->prepare("UPDATE vendor_products SET description = ?, default_unit_price = ?, default_unit = ?, default_hs_code = ? WHERE id = ? AND vendor_id = ?");
                     if (!$stmt) throw new Exception("Prepare failed (update): " . $conn->error);
                    $stmt->bind_param("sdssii", $description, $price, $unit, $hs_code, $product_id, $vendor_id);
                } else { // Insert
                    $stmt = $conn->prepare("INSERT INTO vendor_products (vendor_id, description, default_unit_price, default_unit, default_hs_code) VALUES (?, ?, ?, ?, ?)");
                     if (!$stmt) throw new Exception("Prepare failed (insert): " . $conn->error);
                    // Use the validated $vendor_id here
                    $stmt->bind_param("isdss", $vendor_id, $description, $price, $unit, $hs_code); 
                }
            } else {
                 throw new Exception("Product description cannot be empty."); // Validation error
            }
        }

        // Execute if a statement was prepared
        if ($stmt) {
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
        } else if ($action === 'save' || $action === 'delete') {
             // If action was save/delete but no statement means invalid input (e.g., empty description or bad product_id)
             // Error might have been thrown already, or handle silently / add specific user feedback
        }

        // --- CORRECTED REDIRECT ---
        // Redirect using the original *encrypted* vendor ID
        header("Location: manage_products.php?vendor_id=" . urlencode($encrypted_vendor_id));
        exit();

    } catch (Exception $e) {
         error_log("Error in manage_products POST: " . $e->getMessage()); // Log detailed error
         $error_message = "Error saving product: " . htmlspecialchars($e->getMessage()); // Show error on page
         if ($stmt) $stmt->close(); // Ensure statement is closed on error
    }
} // End POST handling

// Fetch all products for this vendor (using validated $vendor_id)
// Use prepared statement for fetching products as well
$products = []; // Initialize
$stmt_fetch_prods = $conn->prepare("SELECT * FROM vendor_products WHERE vendor_id = ? ORDER BY description ASC");
if ($stmt_fetch_prods) {
    $stmt_fetch_prods->bind_param("i", $vendor_id);
    if ($stmt_fetch_prods->execute()) {
        $products = $stmt_fetch_prods->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        error_log("Error executing fetch products: " . $stmt_fetch_prods->error);
    }
    $stmt_fetch_prods->close();
} else {
    error_log("Prepare failed (fetch products): " . $conn->error);
}

// Close connection at the end
if ($conn->ping()) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products for <?= htmlspecialchars($vendor_name) ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .btn { padding: 10px 15px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .btn-edit { background-color: #ffc107; color: #212529; font-size: 0.9em; padding: 5px 10px; }
        .btn-delete { background-color: #dc3545; font-size: 0.9em; padding: 5px 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f1f3f5; }
        .form-section { margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .form-grid { display: grid; grid-template-columns: 3fr 1fr 1fr 1fr; gap: 15px; align-items: flex-end;}
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 3px; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px; } 
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">&larr; Back to Vendor List</a>
        <h1>Manage Products</h1>
        <h2>For: <?= htmlspecialchars($vendor_name) ?></h2>

        <?php if (isset($error_message)): ?>
            <p class="error-msg"><?= $error_message ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Unit</th>
                    <th>HS Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['default_unit_price']) ?></td>
                        <td><?= htmlspecialchars($product['default_unit']) ?></td>
                        <td><?= htmlspecialchars($product['default_hs_code']) ?></td>
                        <td>
                            <button class="btn-edit" onclick='editProduct(<?= json_encode($product, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?>)'>Edit</button>
                            <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No products found for this vendor. Add one below.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="form-section">
            <h2 id="form-title">Add New Product</h2>
            <form id="product-form" action="manage_products.php?vendor_id=<?= urlencode($encrypted_vendor_id) ?>" method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="product_id" id="product_id" value="">
                <div class="form-grid">
                    <div>
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" required>
                    </div>
                    <div>
                        <label for="default_unit_price">Price</label>
                        <input type="number" step="0.01" id="default_unit_price" name="default_unit_price" required>
                    </div>
                    <div>
                        <label for="default_unit">Unit</label>
                        <select id="default_unit" name="default_unit">
                            <option value="Case">Case</option>
                            <option value="Carton">Carton</option>
                        </select>
                    </div>
                    <div>
                        <label for="default_hs_code">HS Code</label>
                        <input type="text" id="default_hs_code" name="default_hs_code">
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <button type="submit" class="btn">Save Product</button>
                    <button type="button" class="btn" style="background-color:#6c757d;" onclick="resetForm()">Clear Form</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const formTitle = document.getElementById('form-title');
        const productForm = document.getElementById('product-form');
        const productIdField = document.getElementById('product_id');
        const descriptionField = document.getElementById('description');
        const priceField = document.getElementById('default_unit_price');
        const unitField = document.getElementById('default_unit');
        const hsCodeField = document.getElementById('default_hs_code');

        function editProduct(product) {
            formTitle.textContent = 'Edit Product';
            productIdField.value = product.id;
            descriptionField.value = product.description;
            priceField.value = product.default_unit_price;
            unitField.value = product.default_unit;
            hsCodeField.value = product.default_hs_code;
            // Scroll form into view smoothly
            productForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function resetForm() {
            formTitle.textContent = 'Add New Product';
            productForm.reset();
            productIdField.value = ''; // Ensure hidden ID is cleared
        }
    </script>
</body>
</html>