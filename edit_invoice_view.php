<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Invoice Details</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        h3 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px;}
        .form-group, .form-group-inline { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 3px; font-family: sans-serif; }
        textarea { min-height: 60px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group-inline { display: flex; align-items: center; gap: 10px; }
        .form-group-inline label { margin-bottom: 0; font-weight: normal; }
        .form-group-inline input[type="checkbox"] { width: auto; margin-right: 5px; flex-shrink: 0;}
        
        /* Product row updated to 8 columns, adjusted fractions */
        .product-row { display: grid; grid-template-columns: 3fr 0.8fr 1fr 1fr 1.2fr 1.2fr 1fr auto; gap: 10px; margin-bottom: 10px; align-items: center; }
        
        .btn { padding: 10px 15px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-manual { background-color: #ffc107; color: #212529;}
        .btn-remove { background-color: #dc3545; }
        .btn-suggest { font-size: 11px; padding: 4px 8px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-suggest:hover { background-color: #5a6268; }
        .btn-calculate { background-color: #fd7e14; }
        .product-controls { display: flex; gap: 10px; align-items: flex-end; border: 1px solid #eee; padding: 15px; border-radius: 5px; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px; }
        .currency-display { font-size: 1.1em; font-weight: bold; color: #28a745; padding: 8px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 3px; }
        .field-note { font-size: 0.9em; color: #6c757d; margin-top: 4px; }
        .checklist-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 5px 20px; }
        .checklist-grid .form-group-inline { margin-bottom: 5px; }
        details { border: 1px solid #eee; border-radius: 4px; margin-bottom: 10px; }
        details summary { font-size: 1.2em; font-weight: bold; cursor: pointer; padding: 12px 15px; background-color: #f9f9f9; }
        details[open] summary { background-color: #f1f1f1; border-bottom: 1px solid #eee; }
        details div.panel-content { padding: 15px; }
        #cnf-calc-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        #cnf-calc-table th, #cnf-calc-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        #cnf-calc-table th { background-color: #f1f1f1; }
        #cnf-calc-table tfoot td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Invoice (ID: <?= htmlspecialchars($invoice_id) ?>) for <?= htmlspecialchars($invoice['vendor_name'] ?? 'N/A') ?></h1>

        <?php if (!empty($message)): ?><div class="success-msg"><?= $message ?></div><?php endif; ?>
        <?php if (!empty($error_message)): ?><div class="error-msg"><?= $error_message ?></div><?php endif; ?>

        <a href="print_invoice.php?id=<?= urlencode(encryptId($invoice_id)) ?>" target="_blank" class="btn">🖨️ Print Bank Copy</a>
        <a href="print_invoice2.php?id=<?= urlencode(encryptId($invoice_id)) ?>" target="_blank" class="btn" style="background-color:#17a2b8;">🖨️ Print C&F Copy</a>
        <a href="vendor_invoices.php?vendor_id=<?= urlencode(encryptId($invoice['vendor_id'] ?? 0)) ?>" class="btn" style="background-color:#6c757d;">&larr; Back to Invoices</a>
        <hr style="margin: 20px 0;">

        <form action="edit_invoice.php?id=<?= urlencode($encrypted_invoice_id) ?>" method="POST">
            <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($invoice['vendor_id'] ?? 0) ?>">
            
            <?php include 'form_parts/_vendor_details.php'; ?>
            <?php include 'form_parts/_invoice_details.php'; ?>
            <?php include 'form_parts/_auth_letter_details.php'; ?>
            <?php include 'form_parts/_checklist.php'; ?>
            <?php include 'form_parts/_other_clauses.php'; ?>
            <?php include 'form_parts/_products.php'; ?>

            <hr style="margin: 20px 0;">
            <button type="submit" class="btn">Save Changes</button>
        </form>

        <div style="margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0;">C&F Calculation (per kg)</h2>
                <button type="button" class="btn btn-calculate" onclick="calculateCAndF()">Calculate C&F Prices</button>
            </div>
            <table id="cnf-calc-table">
                <thead>
                    <tr><th>Description</th><th>FOB/kg</th><th>Freight/kg</th><th>C&F/kg</th></tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" style="text-align: center;">Click "Calculate" to see results.</td></tr>
                </tbody>
                <tfoot>
                    <tr><td colspan="3">Total Net Weight (kg)</td><td id="total-weight-cell">0.00</td></tr>
                    <tr><td colspan="3">Freight Cost per kg</td><td id="freight-per-kg-cell">0.00</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <script>
        // --- Data from PHP for JS ---
        const allBanks = <?= json_encode($banks ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?>;
        const allCnfAgents = <?= json_encode($cnf_agents ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?>;
        const vendorProducts = <?= json_encode($vendor_products ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?>;
        
        // --- On Page Load ---
        document.addEventListener('DOMContentLoaded', function() {
            // L/C Tolerance Checkbox script
            const toleranceCheckbox = document.getElementById('lc_tolerance_enabled');
            const percentageGroup = document.getElementById('tolerance-percentage-group');
            if(toleranceCheckbox) {
                toleranceCheckbox.addEventListener('change', function() {
                    if(percentageGroup) {
                        percentageGroup.style.display = this.checked ? 'block' : 'none';
                    }
                });

  }

// Product row scripts
            document.querySelectorAll('#product-list select[name="product_unit[]"]').forEach(select => {
                select.addEventListener('change', () => toggleQtyRequired(select));
            });
            
            // Calculate all total weights on load
            document.querySelectorAll('#product-list .product-row').forEach(row => {
                updateTotalWeight(row.querySelector('input[name="product_qty[]"]')); // Trigger calculation
            });

            // Populate ref fields on load
            splitBankReferenceNo(); 
            splitCnfReferenceNo(); 
        });
        
        // --- Split Bank Ref No Function ---
        function splitBankReferenceNo() {
            const refFullInput = document.getElementById('reference_no_full');
            const refPrefixInput = document.getElementById('bank_ref_prefix');
            const refSuffixInput = document.getElementById('bank_ref_suffix');
            
            if (refFullInput && refPrefixInput && refSuffixInput && refFullInput.value) {
                const fullRef = refFullInput.value;
                const lastSlash = fullRef.lastIndexOf('/');
                if (lastSlash !== -1 && lastSlash < fullRef.length - 1) {
                    refPrefixInput.value = fullRef.substring(0, lastSlash + 1);
                    refSuffixInput.value = fullRef.substring(lastSlash + 1);
                } else {
                    refSuffixInput.value = fullRef;
                }
            } else if (refPrefixInput) {
                // Pre-fill prefix if bank is already selected
                fetchSuggestedBankRef(false); 
            }
        }
        function toggleOtherCert(checkbox) {
            const odGroup = document.getElementById('odgroup');
            if (odGroup) {
                odGroup.style.display = checkbox.checked ? 'block' : 'none';
            }
        }
        // --- Split C&F Ref No Function ---
        function splitCnfReferenceNo() {
            const refFullInput = document.getElementById('cnf_reference_no_full');
            const refPrefixInput = document.getElementById('cnf_ref_prefix');
            const refSuffixInput = document.getElementById('cnf_ref_suffix');
            
            if (refFullInput && refPrefixInput && refSuffixInput && refFullInput.value) {
                const fullRef = refFullInput.value;
                const lastSlash = fullRef.lastIndexOf('/');
                if (lastSlash !== -1 && lastSlash < fullRef.length - 1) {
                    refPrefixInput.value = fullRef.substring(0, lastSlash + 1);
                    refSuffixInput.value = fullRef.substring(lastSlash + 1);
                } else {
                    refSuffixInput.value = fullRef;
                }
            } else if (refPrefixInput) {
                // Pre-fill prefix if agent is already selected
                fetchSuggestedCnfRef(false);
            }
        }

        // --- UPDATED: Function to fetch Bank Reference No ---
        function fetchSuggestedBankRef(showAlert = true) {
            const bankSelect = document.getElementById('bank_id');
            const refPrefixInput = document.getElementById('bank_ref_prefix');
            const refSuffixInput = document.getElementById('bank_ref_suffix');
            
            if (!bankSelect || !refPrefixInput || !refSuffixInput) { return; }
            const selectedBankId = bankSelect.value;
            if (!selectedBankId) {
                if (showAlert) alert("Please select a Bank first.");
                return;
            }
            
            if (showAlert) refSuffixInput.value = "Loading...";

            fetch(`get_next_bank_ref.php?bank_id=${selectedBankId}`)
                .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                .then(data => {
                    if (data.success && data.ref_prefix && data.ref_suffix) {
                        refPrefixInput.value = data.ref_prefix;
                        refSuffixInput.value = data.ref_suffix;
                        if(showAlert) {
                            refSuffixInput.focus();
                            refSuffixInput.setSelectionRange(data.ref_suffix.length, data.ref_suffix.length);
                        }
                    } else {
                        if (showAlert) alert(data.message || "Could not generate reference number.");
                        else if (!refSuffixInput.value) refPrefixInput.value = ''; // Clear prefix if just loading
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    if (showAlert) alert("An error occurred while fetching the bank reference number.");
                });
        }
        
        // --- NEW: Function to fetch C&F Reference No ---
        function fetchSuggestedCnfRef(showAlert = true) {
            const agentSelect = document.getElementById('cnf_agent_id');
            const refPrefixInput = document.getElementById('cnf_ref_prefix');
            const refSuffixInput = document.getElementById('cnf_ref_suffix');
            
            if (!agentSelect || !refPrefixInput || !refSuffixInput) { return; }
            const selectedAgentId = agentSelect.value;
            if (!selectedAgentId) {
                if (showAlert) alert("Please select a C&F Agent first.");
                return;
            }
            
            if (showAlert) refSuffixInput.value = "Loading...";

            fetch(`get_next_cnf_ref.php?cnf_agent_id=${selectedAgentId}`)
                .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                .then(data => {
                    if (data.success && data.ref_prefix && data.ref_suffix) {
                        refPrefixInput.value = data.ref_prefix;
                        refSuffixInput.value = data.ref_suffix;
                        if(showAlert) {
                            refSuffixInput.focus();
                            refSuffixInput.setSelectionRange(data.ref_suffix.length, data.ref_suffix.length);
                        }
                    } else {
                        if (showAlert) alert(data.message || "Could not generate reference number.");
                         else if (!refSuffixInput.value) refPrefixInput.value = ''; // Clear prefix if just loading
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    if (showAlert) alert("An error occurred while fetching the C&F reference number.");
                });
        }
        
      // --- Amount in Words Calculation ---
        function numberToWords(numStr) {
            // ... (full numberToWords function) ...
            const a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
            const b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];
            function inWords(num) {
                if ((num = num.toString()).length > 9) return 'overflow'; 
                let n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
                if (!n) return ''; var str = '';
                str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
                str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
                str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
                str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
                str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) : '';
                return str.trim();
            }
            let parts = numStr.split('.');
            let wholeNum = parts[0];
            let fraction = parts[1] || '00';
            let words = inWords(wholeNum);
            if (words === '') words = 'Zero';
            if (fraction !== '00') {
                words += ' and ' + fraction + '/100';
            }
            return words.charAt(0).toUpperCase() + words.slice(1);
        }
        function calculateAmountInWords() {
            let total = 0;
            const productRows = document.querySelectorAll('#product-list .product-row');
            productRows.forEach(row => {
                const qty = parseFloat(row.querySelector('input[name="product_qty[]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name="product_price[]"]').value) || 0;
                total += qty * price;
            });
            const freight = parseFloat(document.getElementById('freight_cost').value) || 0;
            const grandTotal = (total + freight).toFixed(2);
            const wordsField = document.getElementById('amount_in_words');
            if (wordsField) {
                wordsField.value = numberToWords(grandTotal);
            }
        }
        // --- UPDATED: Total Weight Calculation ---
        function updateTotalWeight(inputElement) {
            const productRow = inputElement.closest('.product-row');
            if (!productRow) return;
            
            const qtyInput = productRow.querySelector('input[name="product_qty[]"]');
            const netWtInput = productRow.querySelector('input[name="product_net_weight[]"]');
            const totalWtInput = productRow.querySelector('input[name="product_total_weight[]"]');
            
            if (qtyInput && netWtInput && totalWtInput) {
                const qty = parseFloat(qtyInput.value) || 0;
                const netWt = parseFloat(netWtInput.value) || 0;
                totalWtInput.value = (qty * netWt).toFixed(2);
            }
        }
        
        // --- NEW: Net Weight Calculation ---
        function updateNetWeight(inputElement) {
            const productRow = inputElement.closest('.product-row');
            if (!productRow) return;
            
            const qtyInput = productRow.querySelector('input[name="product_qty[]"]');
            const netWtInput = productRow.querySelector('input[name="product_net_weight[]"]');
            const totalWtInput = productRow.querySelector('input[name="product_total_weight[]"]');
            
            if (qtyInput && netWtInput && totalWtInput) {
                const qty = parseFloat(qtyInput.value) || 0;
                const totalWt = parseFloat(totalWtInput.value) || 0;
                
                if (qty > 0) {
                    netWtInput.value = (totalWt / qty).toFixed(2); // Calculate Net Wt
                } else {
                    // Avoid division by zero, maybe clear net wt
                    netWtInput.value = '0.00';
                }
            }
        }
// --- Product row "None" unit script ---
        function toggleQtyRequired(unitSelectElement) {
            const productRow = unitSelectElement.closest('.product-row');
            if (productRow) {
                const qtyInput = productRow.querySelector('input[name="product_qty[]"]');
                if (qtyInput) {
                    if (unitSelectElement.value === 'None') {
                        qtyInput.removeAttribute('required');
                    } else {
                        qtyInput.setAttribute('required', 'required');
                    }
                }
            }
            updateTotalWeight(unitSelectElement);
        }

        // Add Product Row script (UPDATED)
        function addProductRow(productData = null) {
            const list = document.getElementById('product-list');
            if(!list) return;
            const newRow = document.createElement('div');
            newRow.className = 'product-row';

            const desc = productData ? productData.description : '';
            const price = productData ? productData.default_unit_price : '';
            const unit = productData ? productData.default_unit : 'Case';
            const net_weight = productData ? productData.default_net_weight : '0.00'; 
            const hsCode = productData ? productData.default_hs_code : '';
            const isQtyRequired = (unit !== 'None');
            
            // Calculate initial total weight for new row
            const initialTotalWt = (1 * parseFloat(net_weight)).toFixed(2);

            newRow.innerHTML = `
                <input type="text" name="product_description[]" placeholder="Description" value="${desc}" required>
                <input type="number" name="product_qty[]" placeholder="Qty" value="1" ${isQtyRequired ? 'required' : ''} oninput="updateTotalWeight(this)">
                <input type="number" step="0.01" name="product_price[]" placeholder="Unit Price (FOB)" value="${price}" required>
                <select name="product_unit[]" onchange="toggleQtyRequired(this)">
                    <option value="None" ${unit === 'None' ? 'selected' : ''}>None</option>
                    <option value="Case" ${unit === 'Case' ? 'selected' : ''}>/ Case</option>
                    <option value="Carton" ${unit === 'Carton' ? 'selected' : ''}>/ Carton</option>
                </select>
                <input type="number" step="0.01" name="product_net_weight[]" placeholder="Net Wt (kg)/unit" value="${net_weight}" required oninput="updateTotalWeight(this)">
                <input type="number" step="0.01" name="product_total_weight[]" class="product-total-weight" placeholder="Total Wt" value="${initialTotalWt}" oninput="updateNetWeight(this)">
                <input type="text" name="product_hs_code[]" placeholder="HS Code" value="${hsCode}" required>
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
            `;
            list.appendChild(newRow);
            
            // Add listeners to the new row's inputs
            const newQtyInput = newRow.querySelector('input[name="product_qty[]"]');
            const newNetWtInput = newRow.querySelector('input[name="product_net_weight[]"]');
            const newTotalWtInput = newRow.querySelector('input[name="product_total_weight[]"]');
            const newSelect = newRow.querySelector('select[name="product_unit[]"]');
            
            if(newQtyInput) newQtyInput.addEventListener('input', () => updateTotalWeight(newQtyInput));
            if(newNetWtInput) newNetWtInput.addEventListener('input', () => updateTotalWeight(newNetWtInput));
            if(newTotalWtInput) newTotalWtInput.addEventListener('input', () => updateNetWeight(newTotalWtInput));
            if(newSelect) newSelect.addEventListener('change', () => toggleQtyRequired(newSelect));
        }
        function addSelectedProduct() {
            const select = document.getElementById('vendor-product-select');
            if(!select) return;
            const selectedId = parseInt(select.value, 10);
            if (!selectedId) { alert('Please select a product from the list.'); return; }
            const productToAdd = vendorProducts.find(p => p.id === selectedId);
            if (productToAdd) { addProductRow(productToAdd); }
        }
// Add event listeners to initially loaded rows
        document.querySelectorAll('#product-list .product-row').forEach(row => {
            const qtyInput = row.querySelector('input[name="product_qty[]"]');
            const netWtInput = row.querySelector('input[name="product_net_weight[]"]');
            const totalWtInput = row.querySelector('input[name="product_total_weight[]"]');
            const unitSelect = row.querySelector('select[name="product_unit[]"]');

            if(qtyInput) qtyInput.addEventListener('input', () => updateTotalWeight(qtyInput));
            if(netWtInput) netWtInput.addEventListener('input', () => updateTotalWeight(netWtInput));
            if(totalWtInput) totalWtInput.addEventListener('input', () => updateNetWeight(totalWtInput));
            if(unitSelect) unitSelect.addEventListener('change', () => toggleQtyRequired(unitSelect));
        });
        
  // --- UPDATED C&F CALCULATION FUNCTION ---
        function calculateCAndF() {
            const freightInput = document.querySelector('input[name="freight_cost"]');
            const totalFreight = parseFloat(freightInput.value) || 0;
            
            const productRows = document.querySelectorAll('#product-list .product-row');
            const calcTableBody = document.querySelector('#cnf-calc-table tbody');
            const totalWeightCell = document.getElementById('total-weight-cell');
            const freightPerKgCell = document.getElementById('freight-per-kg-cell');
            
            if (productRows.length === 0) {
                calcTableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: red;">Please add products first.</td></tr>';
                return;
            }

            let totalNetWeight = 0;
            const productsData = [];

            // First pass: get all data and calculate total weight
            productRows.forEach(row => {
                const qty = parseFloat(row.querySelector('input[name="product_qty[]"]').value) || 0;
                const unitPriceFOB = parseFloat(row.querySelector('input[name="product_price[]"]').value) || 0; // This is FOB per unit
                const netWeightPerUnit = parseFloat(row.querySelector('input[name="product_net_weight[]"]').value) || 0;
                const description = row.querySelector('input[name="product_description[]"]').value;



                if (qty > 0 && netWeightPerUnit > 0) {
                    totalNetWeight += qty * netWeightPerUnit;
                }
                productsData.push({ description, unitPriceFOB, netWeightPerUnit });
            });

            if (totalNetWeight <= 0) {
                calcTableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: red;">Total Net Weight is 0. Cannot calculate. Please check quantities and net weights.</td></tr>';
                totalWeightCell.textContent = '0.00';
                freightPerKgCell.textContent = '0.00';
                return;
            }

            // This is (Total Freight / Total Net Weight)
            const freightPerKg = totalFreight / totalNetWeight;

            // Second pass: calculate and display
            calcTableBody.innerHTML = ''; // Clear the table
            productsData.forEach(prod => {
                let fobPerKg = 0;
                // This is (Single Item FOB / Single Item Net Weight)
                if (prod.netWeightPerUnit > 0) {
                    fobPerKg = prod.unitPriceFOB / prod.netWeightPerUnit;
                }
                
                // This is C&F per kg = (FOB/kg) + (Freight/kg)
                const cnfPerKg = fobPerKg + freightPerKg;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${prod.description}</td>
                    <td>${fobPerKg.toFixed(4)}</td>
                    <td>${freightPerKg.toFixed(4)}</td>
                    <td>${cnfPerKg.toFixed(4)}</td>
                `;
                calcTableBody.appendChild(newRow);
            });
            
            // Update footer cells
            totalWeightCell.textContent = totalNetWeight.toFixed(2);
            freightPerKgCell.textContent = freightPerKg.toFixed(4); // Show more precision
        }
    </script>
</body>
</html>