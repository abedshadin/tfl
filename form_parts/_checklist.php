<details name="my-accordion"> <summary>Document Checklist Status (for C&F Copy)</summary>
    <div class="panel-content"> <p class="field-note">Check items that should be marked as "Enclosed".</p>
        <div class="checklist-grid">
             <div class="form-group-inline"><input type="checkbox" id="chk_noc" name="chk_noc" value="1" <?= !empty($invoice['chk_noc']) ? 'checked' : '' ?>><label for="chk_noc">NOC</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_bill_of_exchange" name="chk_bill_of_exchange" value="1" <?= !empty($invoice['chk_bill_of_exchange']) ? 'checked' : '' ?>><label for="chk_bill_of_exchange">Bill of Exchanges</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_packing_list" name="chk_packing_list" value="1" <?= !empty($invoice['chk_packing_list']) ? 'checked' : '' ?>><label for="chk_packing_list">Packing List</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_coo" name="chk_coo" value="1" <?= !empty($invoice['chk_coo']) ? 'checked' : '' ?>><label for="chk_coo">Certificate of Origin</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_health_cert" name="chk_health_cert" value="1" <?= !empty($invoice['chk_health_cert']) ? 'checked' : '' ?>><label for="chk_health_cert">Health Certificate</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_radioactivity_cert" name="chk_radioactivity_cert" value="1" <?= !empty($invoice['chk_radioactivity_cert']) ? 'checked' : '' ?>><label for="chk_radioactivity_cert">Radioactivity Cert.</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_undertaking" name="chk_undertaking" value="1" <?= !empty($invoice['chk_undertaking']) ? 'checked' : '' ?>><label for="chk_undertaking">Undertaking</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_declaration" name="chk_declaration" value="1" <?= !empty($invoice['chk_declaration']) ? 'checked' : '' ?>><label for="chk_declaration">Declaration of Shipments</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_lc_copy" name="chk_lc_copy" value="1" <?= !empty($invoice['chk_lc_copy']) ? 'checked' : '' ?>><label for="chk_lc_copy">LC Copy</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_lca_cad" name="chk_lca_cad" value="1" <?= !empty($invoice['chk_lca_cad']) ? 'checked' : '' ?>><label for="chk_lca_cad">LCA / CAD Copy</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_pi_copy" name="chk_pi_copy" value="1" <?= !empty($invoice['chk_pi_copy']) ? 'checked' : '' ?>><label for="chk_pi_copy">Proforma Invoice</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_insurance_cert" name="chk_insurance_cert" value="1" <?= !empty($invoice['chk_insurance_cert']) ? 'checked' : '' ?>><label for="chk_insurance_cert">Insurance Certificate</label></div>
             <div class="form-group-inline"><input type="checkbox" id="chk_form_ga" name="chk_form_ga" value="1" <?= !empty($invoice['chk_form_ga']) ? 'checked' : '' ?>><label for="chk_form_ga">Form-GA</label></div>
             
             <div class="form-group-inline">
                <input type="checkbox" id="od_enabled" name="od_enabled" value="1" <?= !empty($invoice['od_enabled']) ? 'checked' : '' ?> onchange="toggleOtherCert(this)">
                <label for="od_enabled">Others Certificate</label>
             </div>
        </div>
        <div class="form-group" id="odgroup" style="display: <?= !empty($invoice['od_enabled']) ? 'block' : 'none' ?>; margin-left: 25px; margin-top: 5px; margin-bottom: 10px;">
            <label for="chk_others_cert_text" style="font-weight: normal; font-style: italic;">Specify Certificate Name:</label>
            <input type="text" id="chk_others_cert_text" name="chk_others_cert_text" value="<?= htmlspecialchars($invoice['chk_others_cert_text'] ?? 'Phytosanitary Certificate') ?>" style="width: 90%;">
        </div>
        </div>
</details>