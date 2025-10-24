<h2 style="margin-top: 20px;">Products</h2>
<div class="product-controls">
    <div style="flex:3;"><label>Select a standard product to add</label><select id="vendor-product-select"><option value="">-- Choose --</option><?php foreach($vendor_products as $vp): ?><option value="<?= $vp['id'] ?>"><?= htmlspecialchars($vp['description']) ?></option><?php endforeach; ?></select></div>
    <button type="button" class="btn btn-add" onclick="addSelectedProduct()">Add Selected</button>
    <button type="button" class="btn btn-manual" onclick="addProductRow()">Add Manual</button>
</div>
<div id="product-list" style="margin-top:20px;">
   <?php foreach ($products_on_invoice as $index => $product): ?>
    <?php
        // Calculate initial total weight for this product
        $qty = $product['quantity'] ?? 0;
        $net_wt = $product['net_weight'] ?? 0;
        $total_wt = (float)$qty * (float)$net_wt;
    ?>
    <div class="product-row">
        <div>
            <label for="product_description_<?= $index ?>">Description</label>
            <input type="text" id="product_description_<?= $index ?>" name="product_description[]" placeholder="Description" value="<?= htmlspecialchars($product['description']) ?>" required>
        </div>
        
        <div>
            <label for="product_qty_<?= $index ?>">Qty</label>
            <input type="number" id="product_qty_<?= $index ?>" name="product_qty[]" placeholder="Qty" value="<?= htmlspecialchars($product['quantity'] ?? '') ?>" <?= (isset($product['unit']) && $product['unit'] !== 'None') ? 'required' : '' ?> oninput="updateTotalWeight(this)">
        </div>
        
        <div>
            <label for="product_price_<?= $index ?>">Unit Price</label>
            <input type="number" step="0.01" id="product_price_<?= $index ?>" name="product_price[]" placeholder="Unit Price (FOB)" value="<?= htmlspecialchars($product['unit_price']) ?>" required>
        </div>
        
        <div>
            <label for="product_unit_<?= $index ?>">Unit</label>
            <select id="product_unit_<?= $index ?>" name="product_unit[]" onchange="toggleQtyRequired(this)">
                <option value="None" <?= (isset($product['unit']) && $product['unit'] == 'None') ? 'selected' : '' ?>>None</option>
                <option value="Case" <?= (!isset($product['unit']) || $product['unit'] == 'Case' || empty($product['unit'])) ? 'selected' : '' ?>>/ Case</option>
                <option value="Carton" <?= (isset($product['unit']) && $product['unit'] == 'Carton') ? 'selected' : '' ?>>/ Carton</option>
            </select>
        </div>
        
        <div>
            <label for="product_net_weight_<?= $index ?>">Net Wt/unit</label>
            <input type="number" step="0.01" id="product_net_weight_<?= $index ?>" name="product_net_weight[]" placeholder="Net Wt (kg)/unit" value="<?= htmlspecialchars($product['net_weight'] ?? '') ?>" required oninput="updateTotalWeight(this)">
        </div>
        
        <div>
            <label for="product_total_weight_<?= $index ?>">Total Wt</label>
            <input type="number" step="0.01" id="product_total_weight_<?= $index ?>" name="product_total_weight[]" class="product-total-weight" placeholder="Total Wt" value="<?= number_format($total_wt, 2, '.', '') ?>" oninput="updateNetWeight(this)">
        </div>
        
        <div>
            <label for="product_hs_code_<?= $index ?>">HS Code</label>
            <input type="text" id="product_hs_code_<?= $index ?>" name="product_hs_code[]" placeholder="HS Code" value="<?= htmlspecialchars($product['hs_code']) ?>" required>
        </div>
        
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
    </div>
    <?php endforeach; ?>
</div>