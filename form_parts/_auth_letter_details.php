<h3>Authorization Letter Details</h3>
<div class="form-row">
    <div class="form-group">
        <label for="cnf_agent_id">C&F Agent</label>
        <select id="cnf_agent_id" name="cnf_agent_id">
            <option value="">-- Select --</option>
            <?php $selected_agent_id = $invoice['cnf_agent_id'] ?? null; if (!empty($cnf_agents)) { foreach ($cnf_agents as $agent):?>
            <option value="<?= $agent['id'] ?>" <?= ($selected_agent_id == $agent['id']) ? 'selected' : '' ?>><?= htmlspecialchars($agent['name']) ?></option>
            <?php endforeach; } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="document_status">Document Status (for C&F Copy)</label>
        <select id="document_status" name="document_status">
            <option value="Original" <?= (($invoice['document_status'] ?? 'Original') == 'Original') ? 'selected' : '' ?>>Original</option>
            <option value="Copy" <?= (($invoice['document_status'] ?? '') == 'Copy') ? 'selected' : '' ?>>Copy</option>
        </select>
    </div>
</div>

<div class="form-group">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <label for="cnf_ref_suffix" style="margin-bottom: 0;">C&F Reference No</label>
        <button type="button" class="btn-suggest" onclick="fetchSuggestedCnfRef()">Suggest New C&F Ref</button>
    </div>
    <div style="display: flex; align-items: center; gap: 5px;">
        <input type="text" id="cnf_ref_prefix" name="cnf_ref_prefix" value="" placeholder="Prefix" readonly style="background-color: #eee; flex: 2;">
        <input type="text" id="cnf_ref_suffix" name="cnf_ref_suffix" value="" placeholder="Suffix (e.g., 101)" style="flex: 1;">
    </div>
    <p class="field-note">Select a C&F Agent, then click "Suggest New C&F Ref".</p>
    <input type="hidden" id="cnf_reference_no_full" value="<?= htmlspecialchars($invoice['cnf_reference_no'] ?? '') ?>">
</div>