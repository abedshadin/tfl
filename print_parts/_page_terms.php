<div class="page page-break"> 
    <header class="page-header"><img src="header.jpg" alt="Header" /></header>
    <main class="page-content" role="main">
      <div class="doc-title">Other Terms &amp; Conditions</div>
      <ol>
        <li>
          <?php if (!empty($invoice['advising_bank_name'])): ?>
            Please arrange to through L/C to
            <strong><?= htmlspecialchars($invoice['advising_bank_name']) ?>, SWIFT CODE: <?= htmlspecialchars($invoice['advising_bank_swift']) ?>, A/C NO. <?= htmlspecialchars($invoice['advising_bank_ac_no']) ?>;</strong>
            For Payment to
            <strong><?= htmlspecialchars($invoice['beneficiary_bank']) ?>, SWIFT CODE: <?= htmlspecialchars($invoice['beneficiary_swift']) ?>, <?= htmlspecialchars($invoice['vendor_name']) ?>, A/C NO. <?= htmlspecialchars($invoice['beneficiary_ac_no']) ?>.</strong>
          <?php else: ?>
            Please open irrevocable L/C through 
            <strong><?= htmlspecialchars($invoice['beneficiary_bank']) ?>, SWIFT Code: <?= htmlspecialchars($invoice['beneficiary_swift']) ?>.</strong>
          <?php endif; ?>
        </li>
        <li>All Packets / Cartons must show <strong>Date of Manufacture &amp; Expiry</strong>.</li>
        <li>L/C number and date must appear in all shipping documents.</li>
        
        <li>L/C number and date, &amp; H.S. Code no. <?= htmlspecialchars($hs_code_string) ?> must appear in the Invoice.</li>
        
        <li>
          Description of Goods:
          <?php foreach ($products_on_invoice as $product): ?>
             <?php 
                $is_unit_none = isset($product['unit']) && $product['unit'] === 'None'; 
                if (!$is_unit_none): 
            ?>
                <strong><?= htmlspecialchars($product['quantity'] ?? '') ?> <?= htmlspecialchars($product['unit'] ?? '') ?> of <?= htmlspecialchars($product['description'] ?? '') ?></strong> at the rate of <?= htmlspecialchars($invoice['default_currency'] ?? '') ?> <?= number_format($product['unit_price'] ?? 0, 2) ?>/<?= htmlspecialchars($product['unit'] ?? '') ?>;
                
            <?php else: ?>
                <strong><?= htmlspecialchars($product['description'] ?? '') ?></strong>;
            <?php endif; ?>
          <?php endforeach; ?>
          Freight <?= htmlspecialchars($invoice['default_currency'] ?? '') ?> <?= number_format($invoice['freight_cost'] ?? 0, 2) ?>; Total Amount <?= htmlspecialchars($invoice['default_currency'] ?? '') ?> <?= number_format($grand_total, 2) ?> as per Proforma Invoice No. <?= htmlspecialchars($invoice['pi_number'] ?? 'N/A') ?> Dated <?= htmlspecialchars($pi_date_formatted) ?>.
        </li>
        <li>Certificate of Origin issued by Chamber of Commerce.</li>
        <li>
          The acceptable highest level of radioactivity has been determined to
          <strong>50 BQ/KG/CS-137</strong> for imported
          <strong><?= htmlspecialchars($product_names_string) ?></strong>.
          The radioactivity testing report from the component authority must be sent along with the shipping documents. The level of radioactivity in <strong>CS-137/KG</strong>
          should be mentioned quantitatively in the test report.
        </li>
         <li>
           The Certificates mentioning that the Goods Exported are <strong>“Fit for Human Consumption”</strong>, <strong>“Not Harmful to Human Health”</strong>, <strong>“Free From Harmful Substances”</strong> and <strong>“Free From All Harmful Germs”</strong> to be issued by the concerned authority of the Government of the Exporting Country should be send separately with the shipping documents.
         </li>
        <li>Importer’s Name: Transcom Foods Limited, Address: SE (F) 5, Gulshan Avenue, Gulshan, Dhaka-1212, Bangladesh and E-TIN No. 892580838781, must be clearly mentioned / printed in the packets/cartons.</li>
        <li>E-TIN No. 892580838781, BIN No. 000002132-0101 must appear in the invoice and packing list.</li>
        <li>The beneficiary must send the shipment advice to Reliance Insurance Ltd. at their E-mail ID: <a href="mailto:info@reliance.com.bd">info@reliance.com.bd</a>.</li>
        <?php if (!empty($invoice['lc_tolerance_enabled'])): ?>
            <li>L/C allows <?= htmlspecialchars($invoice['lc_tolerance_percentage'] ?? 10) ?>% tolerance in amount &amp; qty.</li>
        <?php endif; ?>
      </ol><br><br>
      <div class="sig-row">
        <div class="sig">Authorized Signature</div>
        <div class="sig">Authorized Signature</div>
      </div>
    </main>
    <footer class="page-footer"><img src="footer.jpg" alt="Footer" /></footer>
</div>