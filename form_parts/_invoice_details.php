<h2 style="margin-top: 20px;">Invoice Details</h2>

<div class="form-group">
    <label for="bank_id">Bank (for L/C Application)</label>
    <select id="bank_id" name="bank_id" onchange="fetchSuggestedBankRef()"> <option value="">-- Select Bank --</option>
        <?php 
            $selected_bank_id = $invoice['bank_id'] ?? null; 
            if (!empty($banks)) { 
                foreach ($banks as $bank):
        ?>
            <option value="<?= $bank['id'] ?>" <?= ($selected_bank_id == $bank['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($bank['bank_name']) ?>
            </option>
        <?php 
                endforeach; 
            }
        ?>
    </select>
</div>

<div class="form-group">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <label for="bank_ref_suffix" style="margin-bottom: 0;">Bank Reference No</label>
        <button type="button" class="btn-suggest" onclick="fetchSuggestedBankRef()">Suggest New Ref</button>
    </div>
    <div style="display: flex; align-items: center; gap: 5px;">
        <input type="text" id="bank_ref_prefix" name="bank_ref_prefix" value="" placeholder="Prefix" readonly style="background-color: #eee; flex: 2;">
        <input type="text" id="bank_ref_suffix" name="bank_ref_suffix" value="" placeholder="Suffix (e.g., 101)" style="flex: 1;">
    </div>
    <p class="field-note">Select a Bank, then click "Suggest New Ref".</p>
    <input type="hidden" id="reference_no_full" value="<?= htmlspecialchars($invoice['reference_no'] ?? '') ?>">
</div>

<div class="form-group"> <label for="subject_line">Subject Line (for Bank Letter)</label>
    <input type="text" id="subject_line" name="subject_line" value="<?= htmlspecialchars($invoice['subject_line'] ?? 'Opening L/C for Import of Kitchen Equipment for International Chain Restaurant') ?>">
</div>

<div class="form-group">
    <label for="amount_in_words">Amount in Words (for Bank Letter)</label>
    <textarea id="amount_in_words" name="amount_in_words" rows="2"><?= htmlspecialchars($invoice['amount_in_words'] ?? '') ?></textarea>
    <button type="button" class="btn-suggest" style="margin-top: 5px;" onclick="calculateAmountInWords()">Suggest from Total</button>
</div>

<div class="form-group"><label>Currency</label><div class="currency-display"><?= htmlspecialchars($invoice['default_currency'] ?? 'N/A') ?> (Set by vendor)</div></div>
<div class="form-row">
    <div class="form-group"><label>PI No.</label><input type="text" id="pi_number" name="pi_number" value="<?= htmlspecialchars($invoice['pi_number'] ?? '') ?>"></div>
    <div class="form-group"><label>PI Date</label><input type="date" id="pi_date" name="pi_date" value="<?= htmlspecialchars($invoice['pi_date'] ?? '') ?>"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>L/C No.</label><input type="text" id="lc_number" name="lc_number" value="<?= htmlspecialchars($invoice['lc_number'] ?? '') ?>"></div>
    <div class="form-group"><label>L/C Date</label><input type="date" id="lc_date" name="lc_date" value="<?= htmlspecialchars($invoice['lc_date'] ?? '') ?>"></div>
 </div>
 
<div class="form-group"><label>Freight Cost</label><input type="number" step="0.01" id="freight_cost" name="freight_cost" value="<?= htmlspecialchars($invoice['freight_cost'] ?? '') ?>"></div>
 <div class="form-row">
     <div class="form-group"><label>Comm. Invoice No.</label><input type="text" name="commercial_invoice_no" value="<?= htmlspecialchars($invoice['commercial_invoice_no'] ?? '') ?>"></div>
    <div class="form-group"><label>Comm. Invoice Date</label><input type="date" name="commercial_invoice_date" value="<?= htmlspecialchars($invoice['commercial_invoice_date'] ?? '') ?>"></div>
 </div>
 <div class="form-row">
    <div class="form-group"><label>B/L No.</label><input type="text" name="bl_number" value="<?= htmlspecialchars($invoice['bl_number'] ?? '') ?>"></div>
    <div class="form-group"><label>B/L Date</label><input type="date" name="bl_date" value="<?= htmlspecialchars($invoice['bl_date'] ?? '') ?>"></div>
</div>