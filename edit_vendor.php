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

// Decrypt the ID from the URL if it exists
$encrypted_vendor_id = $_GET['id'] ?? ''; // Expecting 'id' based on index.php link
$vendor_id = 0; // Initialize vendor_id
$is_editing = false;

if (!empty($encrypted_vendor_id)) {
    $vendor_id = decryptId($encrypted_vendor_id);
    if ($vendor_id === false || $vendor_id <= 0) {
        die("Invalid Vendor ID provided."); // Stop if decryption fails or ID is invalid
    }
    $is_editing = true;
}

$page_title = $is_editing ? "Edit Vendor" : "Add New Vendor";
$vendor = []; // Initialize vendor array

// Fetch existing vendor data if editing
if ($is_editing) {
    $stmt = $conn->prepare("SELECT * FROM vendors WHERE id = ?");
    if ($stmt === false) { die("Error preparing statement: " . $conn->error); }
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
    if (!$vendor) {
        die("Vendor not found."); // Stop if vendor with decrypted ID doesn't exist
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data
    $name = $_POST['name'] ?? '';
    $vendor_address = $_POST['vendor_address'] ?? null; // NEW: Get vendor address
    $default_currency = $_POST['default_currency'] ?? 'US$';
    $beneficiary_bank = $_POST['beneficiary_bank'] ?? '';
    $beneficiary_swift = $_POST['beneficiary_swift'] ?? '';
    $beneficiary_ac_no = $_POST['beneficiary_ac_no'] ?? '';
    $advising_bank_name = $_POST['advising_bank_name'] ?? '';
    $advising_bank_swift = $_POST['advising_bank_swift'] ?? '';
    $advising_bank_ac_no = $_POST['advising_bank_ac_no'] ?? '';
    
    // Determine the vendor ID to use (needed for update)
    $current_vendor_id = 0;
    if (isset($_GET['id'])) { // Check if 'id' is in the URL (means editing)
       $encrypted_id_from_url = $_GET['id'];
       $decrypted_id = decryptId($encrypted_id_from_url);
       if ($decrypted_id !== false && $decrypted_id > 0) {
          $current_vendor_id = $decrypted_id;
          $is_editing_post = true; // Flag for query type
       } else {
          die("Invalid Vendor ID in form action.");
       }
    } else {
        $is_editing_post = false; // Flag for query type
    }

    if ($is_editing_post) {
        // UPDATE existing vendor
        $stmt = $conn->prepare("UPDATE vendors SET name = ?, vendor_address = ?, default_currency = ?, beneficiary_bank = ?, beneficiary_swift = ?, beneficiary_ac_no = ?, advising_bank_name = ?, advising_bank_swift = ?, advising_bank_ac_no = ? WHERE id = ?");
        if ($stmt === false) { die("Error preparing update statement: " . $conn->error); }
        // Type string updated from 'ssssssssi' to 'sssssssssi' (10 vars)
        $stmt->bind_param("sssssssssi", $name, $vendor_address, $default_currency, $beneficiary_bank, $beneficiary_swift, $beneficiary_ac_no, $advising_bank_name, $advising_bank_swift, $advising_bank_ac_no, $current_vendor_id);
    } else {
        // INSERT new vendor
        $stmt = $conn->prepare("INSERT INTO vendors (name, vendor_address, default_currency, beneficiary_bank, beneficiary_swift, beneficiary_ac_no, advising_bank_name, advising_bank_swift, advising_bank_ac_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) { die("Error preparing insert statement: " . $conn->error); }
        // Type string updated from 'ssssssss' to 'sssssssss' (9 vars)
        $stmt->bind_param("sssssssss", $name, $vendor_address, $default_currency, $beneficiary_bank, $beneficiary_swift, $beneficiary_ac_no, $advising_bank_name, $advising_bank_swift, $advising_bank_ac_no);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: index.php"); // Redirect on success
        exit();
    } else {
        $error_message = "Error saving vendor: " . $stmt->error;
        $stmt->close();
    }
}

// Close connection if not closed already
if ($conn->ping()) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 700px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h3 { color: #333; }
        h3 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        /* Added textarea styles */
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 3px; font-family: sans-serif; }
        textarea { min-height: 80px; resize: vertical; }
        .btn { padding: 12px 20px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .field-note { font-size: 0.9em; color: #6c757d; margin-top: 4px; }
        .error-msg { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">&larr; Back to Vendor List</a>
        <h1><?= $page_title ?></h1>
        <?php if (isset($error_message)): ?>
            <p class="error-msg"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form action="edit_vendor.php<?= $is_editing ? '?id='.urlencode($encrypted_vendor_id) : '' ?>" method="POST">
            <h3>Vendor Information</h3>
            <div class="form-group">
                <label for="name">Vendor Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($vendor['name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="vendor_address">Vendor Address</label>
                <textarea id="vendor_address" name="vendor_address"><?= htmlspecialchars($vendor['vendor_address'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="default_currency">Default Currency</label>
                <select id="default_currency" name="default_currency">
                    <option value="US$" <?= ($vendor['default_currency'] ?? 'US$') == 'US$' ? 'selected' : '' ?>>US Dollar (US$)</option>
                    <option value="EUR" <?= ($vendor['default_currency'] ?? '') == 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                </select>
            </div>

            <h3>Beneficiary Bank (For Payment To)</h3>
            <div class="form-group">
                <label>Bank Name</label>
                <input type="text" name="beneficiary_bank" value="<?= htmlspecialchars($vendor['beneficiary_bank'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>SWIFT Code</label>
                <input type="text" name="beneficiary_swift" value="<?= htmlspecialchars($vendor['beneficiary_swift'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Account No</label>
                <input type="text" name="beneficiary_ac_no" value="<?= htmlspecialchars($vendor['beneficiary_ac_no'] ?? '') ?>">
            </div>

            <h3>Advising Bank (Through L/C)</h3>
            <p class="field-note">Leave empty if not applicable.</p>
            <div class="form-group">
                <label>Bank Name</label>
                <input type="text" name="advising_bank_name" value="<?= htmlspecialchars($vendor['advising_bank_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>SWIFT Code</label>
                <input type="text" name="advising_bank_swift" value="<?= htmlspecialchars($vendor['advising_bank_swift'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Account No</label>
                <input type="text" name="advising_bank_ac_no" value="<?= htmlspecialchars($invoice['advising_bank_ac_no'] ?? '') ?>">
            </div>

            <hr style="margin: 20px 0;">
            <button type="submit" class="btn">Save Vendor</button>
        </form>
    </div>
</body>
</html>