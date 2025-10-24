<?php
// This file is included by edit_invoice.php
// It assumes $conn, $invoice_id, and $encrypted_invoice_id are already defined.

$message = ''; // Initialize message variables
$error_message = '';
$needs_fetch = false; // Initialize fetch flag

// Handle form submission for updating data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Note: $invoice_id is already correctly decrypted and validated
    
    if ($invoice_id === false || $invoice_id <= 0) {
         die("Critical Error: Invoice ID became invalid before processing POST data.");
    }

    if (!$conn || !$conn->ping()) {
        include 'db_connect.php'; 
         if ($conn->connect_error) {
             die("Connection failed before transaction: " . $conn->connect_error);
         }
    }

    $conn->begin_transaction();
    try {
        // Update Vendor Details
        $stmt_vendor = $conn->prepare("UPDATE vendors SET beneficiary_bank = ?, beneficiary_swift = ?, beneficiary_ac_no = ?, advising_bank_name = ?, advising_bank_swift = ?, advising_bank_ac_no = ? WHERE id = ?");
        if($stmt_vendor === false) { throw new Exception("Prepare failed (vendor): " . $conn->error); }
        $stmt_vendor->bind_param("ssssssi", $_POST['beneficiary_bank'], $_POST['beneficiary_swift'], $_POST['beneficiary_ac_no'], $_POST['advising_bank_name'], $_POST['advising_bank_swift'], $_POST['advising_bank_ac_no'], $_POST['vendor_id']);
        if(!$stmt_vendor->execute()) { throw new Exception("Execute failed (vendor): " . $stmt_vendor->error); }
        $stmt_vendor->close();

        // Get POST data for invoice details
        $lc_tolerance_enabled = isset($_POST['lc_tolerance_enabled']) ? 1 : 0;
        $lc_tolerance_percentage = (int)($_POST['lc_tolerance_percentage'] ?? 10);
        $cnf_agent_id = !empty($_POST['cnf_agent_id']) ? (int)$_POST['cnf_agent_id'] : null;
        
        // --- UPDATED: Combine BOTH Ref Nos ---
        $reference_no = ($_POST['bank_ref_prefix'] ?? '') . ($_POST['bank_ref_suffix'] ?? '');
        $cnf_reference_no = ($_POST['cnf_ref_prefix'] ?? '') . ($_POST['cnf_ref_suffix'] ?? '');
        
        $subject_line = $_POST['subject_line'] ?? null;
        $amount_in_words = $_POST['amount_in_words'] ?? null;
        $lc_date = !empty($_POST['lc_date']) ? $_POST['lc_date'] : null; 
        $commercial_invoice_no = $_POST['commercial_invoice_no'] ?? null;
        $commercial_invoice_date = !empty($_POST['commercial_invoice_date']) ? $_POST['commercial_invoice_date'] : null;
        $bl_number = $_POST['bl_number'] ?? null;
        $bl_date = !empty($_POST['bl_date']) ? $_POST['bl_date'] : null;
        $document_status = $_POST['document_status'] ?? 'Original'; 

        // Ensure bank_id is read from POST (so it will be updated in DB)
        $bank_id = isset($_POST['bank_id']) && $_POST['bank_id'] !== '' ? (int)$_POST['bank_id'] : 0;
        
        // Get POST data for checklist checkboxes
        $chk_bill_of_exchange = isset($_POST['chk_bill_of_exchange']) ? 1 : 0;
        $chk_packing_list = isset($_POST['chk_packing_list']) ? 1 : 0;
        $chk_coo = isset($_POST['chk_coo']) ? 1 : 0;
        $chk_health_cert = isset($_POST['chk_health_cert']) ? 1 : 0;
        $chk_radioactivity_cert = isset($_POST['chk_radioactivity_cert']) ? 1 : 0;
        $chk_lc_copy = isset($_POST['chk_lc_copy']) ? 1 : 0;
        $chk_pi_copy = isset($_POST['chk_pi_copy']) ? 1 : 0;
        $chk_insurance_cert = isset($_POST['chk_insurance_cert']) ? 1 : 0;
        $chk_form_ga = isset($_POST['chk_form_ga']) ? 1 : 0;
        $od_enabled = isset($_POST['od_enabled']) ? 1 : 0; // Renamed
        $chk_others_cert_text = $_POST['chk_others_cert_text'] ?? null; // Renamed
        $chk_noc = isset($_POST['chk_noc']) ? 1 : 0;
        $chk_undertaking = isset($_POST['chk_undertaking']) ? 1 : 0;
        $chk_declaration = isset($_POST['chk_declaration']) ? 1 : 0;
        $chk_lca_cad = isset($_POST['chk_lca_cad']) ? 1 : 0;

        // Update Invoice Details (REMOVED lcaf_number = ?,)
        $sql_update_invoice = "UPDATE proforma_invoices SET
            pi_number = ?, pi_date = ?, lc_number = ?, freight_cost = ?,
            lc_tolerance_enabled = ?, lc_tolerance_percentage = ?, cnf_agent_id = ?, bank_id = ?,
            reference_no = ?, cnf_reference_no = ?, subject_line = ?, amount_in_words = ?, lc_date = ?, commercial_invoice_no = ?, commercial_invoice_date = ?,
            bl_number = ?, bl_date = ?,
            chk_bill_of_exchange = ?, chk_packing_list = ?, chk_coo = ?, chk_health_cert = ?,
            chk_radioactivity_cert = ?, chk_lc_copy = ?, chk_pi_copy = ?, chk_insurance_cert = ?,
            chk_form_ga = ?, od_enabled = ?, chk_others_cert_text = ?, chk_noc = ?, chk_undertaking = ?,
            chk_declaration = ?, chk_lca_cad = ?,
            document_status = ? 
            WHERE id = ?"; // Total 32 placeholders
        $stmt_invoice = $conn->prepare($sql_update_invoice);
        if(!$stmt_invoice) throw new Exception("Prepare failed (invoice): " . $conn->error);

        // --- FIXED: exact 34-type string to match 34 parameters ---
        $stmt_invoice->bind_param(
            "sssdiiiisssssssssiiiiiiiiiisiiiisi",
            $_POST['pi_number'],
            $_POST['pi_date'],
            $_POST['lc_number'],
            $_POST['freight_cost'],
            $lc_tolerance_enabled,
            $lc_tolerance_percentage,
            $cnf_agent_id,
            $bank_id,
            $reference_no,
            $cnf_reference_no,
            $subject_line,
            $amount_in_words,
            $lc_date,
            $commercial_invoice_no,
            $commercial_invoice_date,
            $bl_number,
            $bl_date,
            $chk_bill_of_exchange,
            $chk_packing_list,
            $chk_coo,
            $chk_health_cert,
            $chk_radioactivity_cert,
            $chk_lc_copy,
            $chk_pi_copy,
            $chk_insurance_cert,
            $chk_form_ga,
            $od_enabled,
            $chk_others_cert_text,
            $chk_noc,
            $chk_undertaking,
            $chk_declaration,
            $chk_lca_cad,
            $document_status,
            $invoice_id
        );

        // --- END CORRECTION ---

         if(!$stmt_invoice->execute()) { throw new Exception("Execute failed (invoice): " . $stmt_invoice->error); }
        $stmt_invoice->close();
        
        // --- Product Insert/Update Logic (remains the same) ---
        $stmt_delete = $conn->prepare("DELETE FROM proforma_products WHERE invoice_id = ?");
        if($stmt_delete === false) { throw new Exception("Prepare failed (delete products): " . $conn->error); }
        $stmt_delete->bind_param("i", $invoice_id);
        if(!$stmt_delete->execute()) { throw new Exception("Execute failed (delete products): " . $stmt_delete->error); }
        $stmt_delete->close();

        if (isset($_POST['product_description']) && is_array($_POST['product_description'])) {
            $stmt_insert_invoice_product = $conn->prepare("INSERT INTO proforma_products (invoice_id, description, quantity, unit_price, unit, net_weight, hs_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if($stmt_insert_invoice_product === false) { throw new Exception("Prepare failed (insert product): " . $conn->error); }
            $stmt_upsert_vendor_product = $conn->prepare("INSERT INTO vendor_products (vendor_id, description, default_unit_price, default_unit, default_net_weight, default_hs_code) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE default_unit_price = VALUES(default_unit_price), default_unit = VALUES(default_unit), default_net_weight = VALUES(default_net_weight), default_hs_code = VALUES(default_hs_code)");
            if($stmt_upsert_vendor_product === false) { throw new Exception("Prepare failed (upsert vendor product): " . $conn->error); }

            foreach ($_POST['product_description'] as $key => $desc) {
                if (!empty($desc) && isset($_POST['product_price'][$key], $_POST['product_unit'][$key], $_POST['product_hs_code'][$key])) {
                    $unit = $_POST['product_unit'][$key];
                    $qty = ($unit === 'None' || !isset($_POST['product_qty'][$key]) || $_POST['product_qty'][$key] === '') ? null : (int)$_POST['product_qty'][$key];
                    if ($qty === null && $unit !== 'None') {
                         throw new Exception("Quantity is required for product '{$desc}' when Unit is not 'None'.");
                    }
                    $qty_to_save = ($qty === null) ? 0 : $qty;

                    $price = (float)$_POST['product_price'][$key];
                    $net_weight = (float)($_POST['product_net_weight'][$key] ?? 0); 
                    $hs_Code = $_POST['product_hs_code'][$key];
                    $vendor_id_hidden = (int)$_POST['vendor_id'];

                    // Corrected types: invoice_id (i), description (s), quantity (i), unit_price (d), unit (s), net_weight (d), hs_code (s)
                    $stmt_insert_invoice_product->bind_param("isidsds", $invoice_id, $desc, $qty_to_save, $price, $unit, $net_weight, $hs_Code);
                     if(!$stmt_insert_invoice_product->execute()) {
                         throw new Exception("Execute failed (insert product loop): " . $stmt_insert_invoice_product->error . " | Invoice ID: " . $invoice_id);
                     }
                    // Corrected types for upsert: vendor_id (i), description (s), default_unit_price (d), default_unit (s), default_net_weight (d), default_hs_code (s)
                    $stmt_upsert_vendor_product->bind_param("isdsds", $vendor_id_hidden, $desc, $price, $unit, $net_weight, $hs_Code); 
                     if(!$stmt_upsert_vendor_product->execute()) { throw new Exception("Execute failed (upsert vendor product loop): " . $stmt_upsert_vendor_product->error); }
                }
            }
            $stmt_insert_invoice_product->close();
            $stmt_upsert_vendor_product->close();
        }
        // --- End Product Logic ---

        $conn->commit();
        $message = "Invoice and Vendor details updated successfully!";
        $needs_fetch = true; 

    } catch (Exception $exception) {
        $conn->rollback();
        error_log("Database Error in edit_invoice.php: " . $exception->getMessage());
        $error_message = "Error updating record: " . htmlspecialchars($exception->getMessage());
        $needs_fetch = true; 
    }
} else {
    $needs_fetch = true;
}

// --- Data Fetching Logic ---
if ($needs_fetch) {
    if (!$conn || !$conn->ping()) { include 'db_connect.php'; if ($conn->connect_error) die("Connection failed: " . $conn->connect_error); }
    
    $sql_display = "SELECT pi.*, v.*, v.name as vendor_name FROM proforma_invoices pi JOIN vendors v ON pi.vendor_id = v.id WHERE pi.id = ?";
    $stmt_display = $conn->prepare($sql_display);
    if(!$stmt_display) die("Error preparing display statement: " . $conn->error);
    $stmt_display->bind_param("i", $invoice_id);
    $stmt_display->execute();
    $invoice = $stmt_display->get_result()->fetch_assoc();
    $stmt_display->close();
    if (!$invoice) die("Invoice not found for the specified ID.");

    // Fetch products
    $stmt_prods = $conn->prepare("SELECT * FROM proforma_products WHERE invoice_id = ?");
    if(!$stmt_prods) die("Error preparing product display statement: " . $conn->error);
    $stmt_prods->bind_param("i", $invoice_id); $stmt_prods->execute(); $products_on_invoice = $stmt_prods->get_result()->fetch_all(MYSQLI_ASSOC); $stmt_prods->close();

    // Fetch vendor products
    $stmt_vendor_prods = $conn->prepare("SELECT id, description, default_unit_price, default_unit, default_net_weight, default_hs_code FROM vendor_products WHERE vendor_id = ? ORDER BY description ASC");
    if(!$stmt_vendor_prods) die("Error preparing vendor product statement: " . $conn->error);
    $current_vendor_id_for_products = $invoice['vendor_id'] ?? 0;
    $stmt_vendor_prods->bind_param("i", $current_vendor_id_for_products); $stmt_vendor_prods->execute(); $vendor_products = $stmt_vendor_prods->get_result()->fetch_all(MYSQLI_ASSOC); $stmt_vendor_prods->close();

    // Fetch C&F Agents list
    $cnf_agents_result = $conn->query("SELECT id, name, ref_prefix FROM cnf_agents ORDER BY name ASC");
    $cnf_agents = $cnf_agents_result ? $cnf_agents_result->fetch_all(MYSQLI_ASSOC) : [];
    
    // Fetch Banks list
    $banks_result = $conn->query("SELECT id, bank_name, ref_prefix, bank_acc_no FROM banks ORDER BY bank_name ASC");
    $banks = $banks_result ? $banks_result->fetch_all(MYSQLI_ASSOC) : [];
}

// Close connection at the very end
if ($conn && $conn->ping()) $conn->close();
?>