<details name="my-accordion"> 
    <summary>Vendor & Bank Details</summary>
    <div class="panel-content">
        <h3>Beneficiary Bank</h3>
        <div class="form-group"><label>Bank Name</label><input type="text" name="beneficiary_bank" value="<?= htmlspecialchars($invoice['beneficiary_bank'] ?? '') ?>"></div>
        <div class="form-group"><label>SWIFT Code</label><input type="text" name="beneficiary_swift" value="<?= htmlspecialchars($invoice['beneficiary_swift'] ?? '') ?>"></div>
        <div class="form-group"><label>Account No</label><input type="text" name="beneficiary_ac_no" value="<?= htmlspecialchars($invoice['beneficiary_ac_no'] ?? '') ?>"></div>
        <h3>Advising Bank</h3>
        <p class="field-note">Leave empty for one-bank clause.</p>
        <div class="form-group"><label>Bank Name</label><input type="text" name="advising_bank_name" value="<?= htmlspecialchars($invoice['advising_bank_name'] ?? '') ?>"></div>
        <div class="form-group"><label>SWIFT Code</label><input type="text" name="advising_bank_swift" value="<?= htmlspecialchars($invoice['advising_bank_swift'] ?? '') ?>"></div>
        <div class="form-group"><label>Account No</label><input type="text" name="advising_bank_ac_no" value="<?= htmlspecialchars($invoice['advising_bank_ac_no'] ?? '') ?>"></div>
    </div>
</details>