<?php
// Security includes
include 'ip_blocker.php';
include 'auth_check.php';
include 'db_connect.php'; // Includes decryptId and other functions

// Get reference prefix from URL
$reference_prefix_search = trim($_GET['ref_prefix'] ?? ''); // Changed variable name

if (empty($reference_prefix_search)) {
    die("No reference prefix provided.");
}

// Prepare the search term for LIKE query
$search_term = $reference_prefix_search . '%'; // Append wildcard

// Fetch all invoices matching the reference prefix, joining necessary tables
$sql_invoices = "SELECT
                    pi.id as invoice_id_num, pi.*,
                    v.*,
                    v.name as vendor_name,
                    cnf.name as cnf_agent_name_from_db,
                    cnf.address1 as cnf_agent_address1_from_db,
                    cnf.address2 as cnf_agent_address2_from_db,
                    cnf.attn_person as cnf_agent_attn_from_db
                FROM proforma_invoices pi
                JOIN vendors v ON pi.vendor_id = v.id
                LEFT JOIN cnf_agents cnf ON pi.cnf_agent_id = cnf.id
                WHERE pi.reference_no LIKE ? -- Changed to LIKE
                ORDER BY pi.id DESC";

$stmt = $conn->prepare($sql_invoices);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("s", $search_term); // Bind the search term with wildcard
if(!$stmt->execute()) die("Error executing statement: " . $stmt->error);
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch products for each invoice (remains the same)
$invoice_products = [];
if (!empty($invoices)) {
    // ... (product fetching code remains the same as before) ...
}


if ($conn->ping()) $conn->close();

// Helper function for checklist status (remains the same)
function getStatus($flag) {
    return !empty($flag) && $flag == 1 ? ': Enclosed' : ': N/A';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>C&F Letters for Prefix: <?= htmlspecialchars($reference_prefix_search) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* CSS remains the same */
        :root{ --header-height: 28mm; --footer-height: 20mm; --side-padding: 15mm; }
        html, body { margin: 0; padding: 0; }
        body{ background: #ccc; color: #000; font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.35; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .page{ width: 210mm; height: 297mm; margin: 10mm auto; background: #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1); display: flex; flex-direction: column; overflow: hidden; }
        .page-break { page-break-before: always; }
        .page-header, .page-footer { flex-shrink: 0; }
        .page-header { height: var(--header-height); padding: 3mm 16mm 0 16mm; }
        .page-footer { height: var(--footer-height); padding: 0 0 3mm 0; }
        .page-header img, .page-footer img { width: 100%; height: 100%; object-fit: contain; }
        .page-content { flex-grow: 1; padding: 2mm var(--side-padding); overflow: hidden; display: flex; flex-direction: column; }
        .doc-title{ text-align: center; font-weight: 700; text-decoration: underline; margin: 0 0 8pt 0; text-transform: uppercase; }
        p, li { text-align: justify; } ol{ margin: 0; padding-left: 18pt; } li{ margin: 0 0 4pt 0; break-inside: avoid; page-break-inside: avoid; }
        .sig-row{ display: flex; justify-content: space-between; gap: 20pt; margin-top: auto; padding-top: 15pt; }
        .sig{ flex: 1 1 0; text-align: center; font-weight: 700; }
        ol.checklist { list-style-type: decimal; padding-left: 20px; } ol.checklist li { font-size: 13pt; font-weight: bold; margin-bottom: 10px; }
        .letter-body { font-size: 11pt; line-height: 1.35; } .letter-body p { margin-bottom: 0em; text-align: justify; } .letter-body .ref-no { text-align: left; font-weight: bold; margin-bottom: 1.5em; } .letter-body .date { text-align: left; margin-bottom: 0.5em; } .letter-body .address-block p { margin-bottom: 0.1em; text-align: left; line-height: 1.3; } .letter-body .attn { margin-top: 0.8em; margin-bottom: 0.5em; text-align: left; } .letter-body .subject { margin-bottom: 0em; text-align: left; font-weight: bold; line-height: 1.4; } .letter-body .subject strong { text-decoration: underline;} .letter-body ol { margin-top: 10px; margin-left: 20px; font-size: 10.5pt; } .letter-body ol li { margin-bottom: 3pt; text-align: left; } .letter-body .closing p { margin-top: 1em; text-align: left; margin-bottom: 0.3em; line-height: 1.3; } .letter-body .signature-block { margin-top: 25px; } .letter-body .signature-block p { margin-bottom: 0.1em; text-align: right; line-height: 1.3; padding-right:50px; } .letter-body .signature-line { margin-bottom: 0; line-height: 1; }
        .bengali-letter p { margin-bottom: 1em; line-height: 1.8; font-size: 13pt;} .bengali-letter .address-block p { font-size: 14pt; line-height: 1.5;} .bengali-letter .subject { font-size: 14pt; margin-bottom: 0.8em;} .bengali-letter ol { font-size: 12pt; } .bengali-letter ol li { margin-bottom: 5pt; text-align: left; } .bengali-letter .closing p { font-size: 14pt; margin-top: 1.5em;} .bengali-letter .signature-block p { font-size: 12pt; text-align: center; }
        .toolbar{ position: sticky; top: 0; z-index: 9999; display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background:#fff; border-bottom:1px solid #e5e7eb; } .toolbar button, .toolbar a { border:1px solid #e5e7eb; background:#fff; padding:8px 12px; border-radius:10px; cursor:pointer; font-weight:600; text-decoration: none; color: #333; } .toolbar span { font-weight: bold; }
        @page{ size: A4 portrait; margin: 0; } @media print{ body { background: #fff; } .page { margin: 0; box-shadow: none; } .toolbar{ display:none !important; } body, .page-content{ background:#fff !important; color:#000 !important; } a{ color: #000; text-decoration: underline; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <span>Displaying Letters for Prefix: <?= htmlspecialchars($reference_prefix_search) ?> (<?= count($invoices) ?> found)</span>
        <div>
            <a href="search_letters.php">&larr; New Search</a>
            <button onclick="window.print()">🖨️ Print All Letters</button>
        </div>
    </div>

    <?php if (empty($invoices)): ?>
        <p style="text-align: center; margin-top: 50px; font-size: 1.2em;">No invoices found matching reference prefix "<?= htmlspecialchars($reference_prefix_search) ?>".</p>
    <?php else: ?>
        <?php foreach ($invoices as $index => $invoice): ?>
            <?php
            // Recalculations for each invoice (remains the same)
            $current_products = $invoice_products[$invoice['invoice_id_num']] ?? [];
            $lc_date_letter = !empty($invoice['lc_date']) && ($ts = strtotime($invoice['lc_date'])) !== false ? date("d.m.Y", $ts) : 'N/A';
            $pi_date_letter = !empty($invoice['pi_date']) && ($ts = strtotime($invoice['pi_date'])) !== false ? date("d.m.Y", $ts) : 'N/A';
            $commercial_invoice_date_letter = !empty($invoice['commercial_invoice_date']) && ($ts = strtotime($invoice['commercial_invoice_date'])) !== false ? date("d.m.Y", $ts) : 'N/A';
            $bl_date_letter = !empty($invoice['bl_date']) && ($ts = strtotime($invoice['bl_date'])) !== false ? date("d.m.Y", $ts) : 'N/A';
            $product_names_string = 'N/A'; // Default
            if (!empty($current_products)) { /* ... product name string calculation ... */ } else $product_names_string = 'Goods';
            ?>

            <div class="page <?= $index > 0 ? 'page-break' : '' ?>"> <header class="page-header"><img src="header.jpg" alt="Header" /></header>
                <main class="page-content letter-body bengali-letter" role="main" style="font-family: 'SolaimanLipi', 'Nikosh', Arial, sans-serif;">
                    <p style="text-align: right; margin-bottom: 1.5em;">+৮৮০ ২.২২২২</p>
                    <p class="date" style="margin-bottom: 1em;"><?= date("F j, Y") ?></p>
                    <div class="address-block" style="margin-bottom: 1.5em;"><p>বরাবর,<br>ডেপুটি কমিশনার অফ কাস্টমস,<br>কাস্টম হাউস,<br>চট্টগ্রাম।</p></div>
                    <p class="subject" style="margin-bottom: 1em;"><strong>বিষয়: ক্ষমতা অর্পণ পত্র।</strong></p>
                    <p>জনাব,</p>
                    <p style="text-align: justify;">উল্লেখিত বিষয়ের প্রতি আপনার সদয় দৃষ্টি আকর্ষণ পূর্বক জানানো যাচ্ছে যে, আমাদের এলসি নং - <?= htmlspecialchars($invoice['lc_number'] ?? 'N/A') ?> Dtd. <?= $lc_date_letter ?> এর বিপরীতে আমদানিকৃত <?= htmlspecialchars($product_names_string) ?> -এর বি/ই নোটিং থেকে খালাস পর্যন্ত সকল কার্যাদি সম্পাদনের জন্য C&F AGENT: <?= htmlspecialchars($invoice['cnf_agent_name_from_db'] ?? 'N/A') ?>, <?= htmlspecialchars($invoice['cnf_agent_address1_from_db'] ?? '') ?> <?= htmlspecialchars($invoice['cnf_agent_address2_from_db'] ?? '') ?>, কে ক্ষমতা অর্পণ করা হলো এবং উক্ত এলসিভুক্ত পণ্যগুলি খালাসের লক্ষ্যে নিম্নলিখিত দলিলাদি দাখিল করা হলো।</p>
                    <p style="margin-top: 1em; margin-bottom: 0.5em;"><u>দলিলাদির বিবরণ:</u></p>
                    <ol style="margin-left: 30px; list-style-type: bengali;">
                        <li>Bill of Lading No.: <?= htmlspecialchars($invoice['bl_number'] ?? 'N/A') ?> Dtd. <?= $bl_date_letter ?></li>
                        <li>Invoice No: <?= htmlspecialchars($invoice['commercial_invoice_no'] ?? 'N/A') ?> Dtd. <?= $commercial_invoice_date_letter ?></li>
                        <li>Packing List</li><li>Country of Origin Certificate,</li><li>L/C, LCA, Bill of Exchange,</li><li>Insurance, Etc.</li>
                    </ol>
                    <div style="margin-top: auto; display: flex; justify-content: space-between; padding-top: 2em;">
                         <div class="closing" style="margin-top: 0;"><p>ধন্যবাদান্তে,</p></div>
                       <div class="signature-block" style="margin-top: 0; text-align: center; padding-right: 10mm;"><p style="margin-bottom: 2.5em;">_________________________</p><p>আপনার বিশ্বস্ত</p><p style="font-weight: bold; margin-top: 1em;">TRANSCOM FOODS LTD.</p><p>MD. REZAUL KARIM</p><p>GENERAL MANAGER, SCM</p><p>CONTACT NO: +৮৮০ ১৭১৩০৮৫৪৪৩</p></div>
                    </div>
                </main>
                <footer class="page-footer"><img src="footer.jpg" alt="Footer" /></footer>
            </div>

            <div class="page page-break">
                <header class="page-header"><img src="header.jpg" alt="Header" /></header>
                <main class="page-content letter-body" role="main">
                    <p class="ref-no">Ref: <?= htmlspecialchars($invoice['reference_no'] ?? 'N/A') ?></p>
                    <p class="date"><?= date("F j, Y") ?></p>
                    <div class="address-block"><p><?= htmlspecialchars($invoice['cnf_agent_name_from_db'] ?? 'N/A') ?><br><?= htmlspecialchars($invoice['cnf_agent_address1_from_db'] ?? '') ?><br><?= htmlspecialchars($invoice['cnf_agent_address2_from_db'] ?? '') ?></p></div>
                    <?php if(!empty($invoice['cnf_agent_attn_from_db'])): ?><p class="attn">Attn: <?= htmlspecialchars($invoice['cnf_agent_attn_from_db']) ?></p><?php endif; ?>
                    <p class="subject"><strong>Re: Original Shipping Documents under L/C No. <?= htmlspecialchars($invoice['lc_number'] ?? 'N/A') ?> Dtd. <?= $lc_date_letter ?> against <br>Indent/Performa Invoice No. <?= htmlspecialchars($invoice['pi_number'] ?? 'N/A') ?> Dtd. <?= $pi_date_letter ?>.</strong></p>
                    <p>Dear Sir,</p><p style="text-align: justify;">Enclosed please find herewith following shipping documents for the above-mentioned L/C. This is for your information and doing your needful.</p>
                    <ol>
                        <li>NOC <?= getStatus($invoice['chk_noc'] ?? 0) ?></li><li>Bill of Exchanges <?= getStatus($invoice['chk_bill_of_exchange'] ?? 0) ?></li><li>Commercial Invoice No: <?= htmlspecialchars($invoice['commercial_invoice_no'] ?? 'N/A') ?> Dtd. <?= $commercial_invoice_date_letter ?></li><li>Packing List <?= getStatus($invoice['chk_packing_list'] ?? 0) ?></li><li>Certificate of Origin <?= getStatus($invoice['chk_coo'] ?? 0) ?></li><li>Bill of Lading No.: <?= htmlspecialchars($invoice['bl_number'] ?? 'N/A') ?> Dtd. <?= $bl_date_letter ?></li><li>Health Certificate <?= getStatus($invoice['chk_health_cert'] ?? 0) ?></li><li>Radioactivity Certificate <?= getStatus($invoice['chk_radioactivity_cert'] ?? 0) ?></li><li>Undertaking <?= getStatus($invoice['chk_undertaking'] ?? 0) ?></li><li>Declaration of Shipments <?= getStatus($invoice['chk_declaration'] ?? 0) ?></li><li>LC Copy <?= getStatus($invoice['chk_lc_copy'] ?? 0) ?></li><li>LCA / CAD Copy <?= getStatus($invoice['chk_lca_cad'] ?? 0) ?></li><li>Proforma Invoice <?= getStatus($invoice['chk_pi_copy'] ?? 0) ?></li><li>Insurance Certificate <?= getStatus($invoice['chk_insurance_cert'] ?? 0) ?></li><li>Form-GA <?= getStatus($invoice['chk_form_ga'] ?? 0) ?></li><li>Others Certificate <?= getStatus($invoice['chk_others_cert'] ?? 0) ?></li>
                    </ol>
                    <p style="margin-top: 25px;">Please acknowledge receipt.</p>
                    <div class="closing"><p>Thank you<br>Yours faithfully<br>For TRANSCOM FOODS LIMITED</p></div>
                    <div class="signature-block" style="margin-top: 40px;"><p class="signature-line">_________________________</p><p><strong>(Md. Rezaul Karim)</strong><br>General Manager- SCM</p></div>
                </main>
                <footer class="page-footer"><img src="footer.jpg" alt="Footer" /></footer>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>