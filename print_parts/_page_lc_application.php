<div class="page"> 
    <header class="page-header"><img src="header.jpg" alt="Header" /></header>
    <main class="page-content bank-letter-body" role="main">
    
        <p class="ref-no">Ref: <?= htmlspecialchars($invoice['reference_no'] ?? 'TFL/SCM/BANK/YEAR/XXX') ?></p>
        
        <p class="date"><?= $current_date_formatted ?></p>

        <div class="address-block">
            <p><b><?= htmlspecialchars($invoice['bank_name'] ?? 'BANK NAME') ?></b><br>
            <?= htmlspecialchars($invoice['address1'] ?? 'Address Line 1') ?><br>
            <?= htmlspecialchars($invoice['address2'] ?? 'Address Line 2') ?><br>
            <?= htmlspecialchars($invoice['address3'] ?? 'Address Line 3') ?></p>
        </div>

        <u><p class="attn"><strong>Attn: Trade Service (Import)</strong></p></u>

        <u><b><p class="subject">
            Sub: <?= htmlspecialchars($invoice['subject_line'] ?? 'Opening L/C for Import') ?>
        </p></b></u>

        <p>Dear Sir,</p>

        <p style="text-align: justify;">
            We are enclosing L/C application form and other related papers duly filled in, stamped and 
            signed by us for opening L/C worth <strong><?= htmlspecialchars($invoice['default_currency'] ?? 'US$') ?> <?= number_format($grand_total, 2) ?> 
            (<?= htmlspecialchars($total_in_words) ?>)</strong> 
            only favoring <strong><?= htmlspecialchars($invoice['vendor_name'] ?? 'VENDOR NAME') ?></strong>, 
            <strong><?= nl2br(htmlspecialchars($invoice['vendor_address'] ?? 'VENDOR ADDRESS')) ?></strong>.
        </p>

        <p>
            Please register L/C & request to debit our current account no. 
            <strong><?= htmlspecialchars($invoice['bank_acc_no'] ?? 'ACCOUNT_NUMBER_NOT_FOUND') ?></strong> 
            maintained with you for your margin and charges.
        </p>

        <div class="closing">
            <p>Thanking You,</p>
            <p>Yours Faithfully,<br>
            For <strong>TRANSCOM FOODS LIMITED</strong></p>
        </div>

        <div class="sig-row" style="margin-top: 4em;"> <div class="sig" style="text-align: left;">Authorized Signature</div>
            <div class="sig" style="text-align: right;">Authorized Signature</div>
        </div>
        
    </main>
    <footer class="page-footer"><img src="footer.jpg" alt="Footer" /></footer>
</div>