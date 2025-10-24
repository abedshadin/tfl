<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>A4 – C&F Copy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
    :root{
      /* Layout sizes */
      --header-height: 28mm;
      --footer-height: 20mm;
      --side-padding: 15mm;
    }
    html, body { margin: 0; padding: 0; }
    body{
      background: #ccc; color: #000; font-family: "Times New Roman", Times, serif;
      font-size: 12pt; /* Slightly smaller base font */
      line-height: 1.35; /* Slightly tighter line height */
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .page{
      width: 210mm; height: 297mm; margin: 10mm auto; background: #fff;
      box-shadow: 0 0 5px rgba(0,0,0,0.1); display: flex; flex-direction: column; overflow: hidden;
    }
    .page-break { page-break-before: always; }
    .page-header, .page-footer { flex-shrink: 0; }
    .page-header { height: var(--header-height); padding: 0mm 16mm 0 16mm; }
    .page-footer { height: var(--footer-height); padding: 0 0 8mm 0; }
    .page-header img, .page-footer img { width: 100%; height: 100%; object-fit: contain; }
    .page-content { flex-grow: 1; padding: 0mm var(--side-padding); overflow: hidden; display: flex; flex-direction: column;padding-left:30mm; } /* Reduced top/bottom padding */
    .doc-title{ text-align: center; font-weight: 700; text-decoration: underline; margin: 0 0 8pt 0; text-transform: uppercase; } /* Reduced bottom margin */
    p, li { text-align: justify; }
    ol{ margin: 0; padding-left: 18pt; }
    li{ margin: 0 0 4pt 0; break-inside: avoid; page-break-inside: avoid; } /* Reduced bottom margin */
    .sig-row{ display: flex; justify-content: space-between; gap: 20pt; margin-top: auto; padding-top: 15pt; } /* Reduced top padding */
    .sig{ flex: 1 1 0; text-align: center; font-weight: 700; }

    /* Checklist Styles */
    ol.checklist { list-style-type: decimal; padding-left: 20px; }
    ol.checklist li { font-size: 12pt; font-weight: bold; margin-bottom: 10px; } /* Reduced font size and margin */

    /* Letter Styles - Reduced Margins and Line Heights */
    .letter-body { font-size: 12pt; line-height: 1.35; } /* Adjusted base size and line height */
    .letter-body p { margin-bottom: -0.5em; text-align: justify; } /* Reduced paragraph margin */
    .letter-body .ref-no { text-align: left; font-weight: bold; margin-bottom: 1.5em; } /* Reduced margin */
    .letter-body .date { text-align: left; margin-bottom: 0.5em; } /* Reduced margin */
    .letter-body .address-block p { margin-bottom: 0.1em; text-align: left; line-height: 1.3; } /* Tighter address lines */
    .letter-body .attn { margin-top: 0.8em; margin-bottom: 0.5em; text-align: left; } /* Reduced margins */
    .letter-body .subject { margin-bottom: 0em; text-align: left; font-weight: bold; line-height: 1.3; } /* Reduced margin & line height */

    .letter-body ol { margin-top: 10px; margin-left: 20px; font-size: 12pt; } /* Reduced font size for list */
    .letter-body ol li { margin-bottom: 3pt; text-align: left; } /* Tighter list items & left align */
    .letter-body .closing p { margin-top: 1em; text-align: left; margin-bottom: 0.3em; line-height: 1.3; } /* Reduced margins/line height */
    .letter-body .signature-block { margin-top: 25px; } /* Reduced top margin */
    .letter-body .signature-block p { margin-bottom: 0.1em; text-align: right; line-height: 1.3; padding-right:50px; } /* Tighter signature lines */
    .letter-body .signature-line { margin-bottom: 0; line-height: 1; } /* Minimal space */

    /* Bengali Letter Specific Adjustments */
    .bengali-letter p { margin-bottom: 1em; line-height: 1.7; font-size: 11pt;}
    .bengali-letter .address-block p { font-size: 11pt; line-height: 1.4;}
    .bengali-letter .subject { font-size: 11pt; margin-bottom: 0.8em;}
    .bengali-letter ol { font-size: 11pt; }
    .bengali-letter ol li { margin-bottom: 5pt; text-align: left; } /* Left align list items */
    .bengali-letter .closing p { font-size: 11pt; margin-top: 1.5em;}
    .bengali-letter .signature-block p { font-size: 11pt; text-align: center; }


    .toolbar{ position: sticky; top: 0; z-index: 9999; display: flex; gap: 8px; align-items: center; padding: 10px 12px; background:#fff; border-bottom:1px solid #e5e7eb; }
    .toolbar button{ border:1px solid #e5e7eb; background:#fff; padding:8px 12px; border-radius:10px; cursor:pointer; font-weight:600; }
    @page{ size: A4 portrait; margin: 0; }
    @media print{ body { background: #fff; } .page { margin: 0; box-shadow: none; } .toolbar{ display:none !important; } body, .page-content{ background:#fff !important; color:#000 !important; } a{ color: #000; text-decoration: underline; } }
  </style>
</head>
<body>
  <div class="toolbar">
    <button onclick="window.print()">🖨️ Print C&F Copy (2 Pages)</button>
  </div>

  <div class="page page-break">
<?php include 'header.php'; ?>
    <main class="page-content letter-body" role="main">
        <div class=""><?= htmlspecialchars($invoice['cnf_reference_no'] ?? 'TFL/SCM/YYYY/MM/XXX') ?></div>
        <b><p class="date"><?= $current_date_formatted ?></p></b>

        <div class="address-block">
            <div><?= htmlspecialchars($invoice['cnf_agent_name_from_db'] ?? 'N/A') ?><br>
               <?= htmlspecialchars($invoice['cnf_agent_address1_from_db'] ?? '') ?><br>
               <?= htmlspecialchars($invoice['cnf_agent_address2_from_db'] ?? '') ?>
</div>
        </div>
        <?php if(!empty($invoice['cnf_agent_attn_from_db'])): ?>
            <b><p class="attn">Attn: <?= htmlspecialchars($invoice['cnf_agent_attn_from_db']) ?></p></b>
        <?php endif; ?>

        <div class="subject">
            <strong>Re: <?= htmlspecialchars($invoice['document_status'] ?? 'Original') ?> Shipping Documents under L/C No. <?= htmlspecialchars($invoice['lc_number'] ?? 'N/A') ?> Dtd. <?= $lc_date_letter ?> against Indent/Performa Invoice No. <?= htmlspecialchars($invoice['pi_number'] ?? 'N/A') ?> Dtd. <?= $pi_date_letter ?>.</strong>
        </div>

        <p>Dear Sir,</p>
        <p>Enclosed please find herewith following shipping documents for the above-mentioned L/C. This is for your information and doing your needful.</p>

        <ol style=" margin-left: 0px; font-size: 12pt;">
            <li>NOC : <b><?= getStatus($invoice['chk_noc'] ?? 0) ?></b></li>
            <li>Bill of Exchanges : <b><?= getStatus($invoice['chk_bill_of_exchange'] ?? 0) ?></b></li>
            <li>Commercial Invoice No : <b><?= htmlspecialchars($invoice['commercial_invoice_no'] ?? 'N/A') ?> Dtd. <?= $commercial_invoice_date_letter ?></b></li>
            <li>Packing List : <b><?= getStatus($invoice['chk_packing_list'] ?? 0) ?></b></li>
            <li>Certificate of Origin : <b><?= getStatus($invoice['chk_coo'] ?? 0) ?></b></li>
            <li>Bill of Lading No. :  <b><?= htmlspecialchars($invoice['bl_number'] ?? 'N/A') ?> Dtd. <?= $bl_date_letter ?></b></li>
            <li>Health Certificate : <b><?= getStatus($invoice['chk_health_cert'] ?? 0) ?></b></li>
            <li>Radioactivity Certificate : <b><?= getStatus($invoice['chk_radioactivity_cert'] ?? 0) ?></b></li>
            <li>Undertaking : <b><?= getStatus($invoice['chk_undertaking'] ?? 0) ?></b></li>
            <li>Declaration of Shipments : <b><?= getStatus($invoice['chk_declaration'] ?? 0) ?></b></li>
            <li>LC Copy : <b><?= getStatus($invoice['chk_lc_copy'] ?? 0) ?></b></li>
            <li>LCA / CAD Copy : <b><?= getStatus($invoice['chk_lca_cad'] ?? 0) ?></b></li>
            <li>Proforma Invoice : <b><?= getStatus($invoice['chk_pi_copy'] ?? 0) ?></b></li>
            <li>Insurance Certificate : <b><?= getStatus($invoice['chk_insurance_cert'] ?? 0) ?></b></li>
            <li>Form-GA : <b><?= getStatus($invoice['chk_form_ga'] ?? 0) ?></b></li>
          <li><?= getStatus_OtherCert($invoice['od_enabled'] ?? 0, $invoice['chk_others_cert_text'] ?? '') ?></li>
        </ol>
        <p style="margin-top: 10px;">Please acknowledge receipt.</p>

        <div class="closing">
             <p>Thank you<br>
                Yours faithfully<br>
                For<b> TRANSCOM FOODS LIMITED</p></b>
        </div>

        <div class="signature-block" style="margin-top: 20px;">
            <p><strong>(Md. Rezaul Karim)</strong><br>
               General Manager- SCM</p>
        </div>
    </main>
  <?php include 'footer.php'; ?>
  </div>




  
<div class="page page-break">
 <?php include 'header.php'; ?>
    <main class="page-content letter-body" role="main" style="text-align: left; font-family: 'SolaimanLipi', 'Nikosh', Arial, sans-serif;">  <div class="address-block">
            <div style="font-size:14pt;"> বরাবর,<br>
                ডেপুটি কমিশনার অফ কাস্টমস,<br>
                কাস্টম হাউস,<br>
                চট্টগ্রাম।</div>
        </div>

        <p class="" style="margin-bottom: 0.2em; font-size:14pt;"><strong>বিষয়: ক্ষমতা অর্পণ পত্র।</strong></p>

        <div style="font-size:13t;">জনাব,</div>

        <p style="text-align: justify; line-height: 1.8; font-size:13pt;"> উল্লেখিত বিষয়ের প্রতি আপনার সদয় দৃষ্টি আকর্ষণ পূর্বক জানানো যাচ্ছে যে, আমাদের এলসি নং - <?= htmlspecialchars($invoice['lc_number'] ?? 'N/A') ?> Dtd. <?= $lc_date_letter ?> এর বিপরীতে আমদানিকৃত <?= htmlspecialchars($product_names_string) ?> -এর বি/ই নোটিং থেকে খালাস পর্যন্ত সকল কার্যাদি সম্পাদনের জন্য C&F AGENT: <?= htmlspecialchars($invoice['cnf_agent_name_from_db'] ?? 'N/A') ?>, <?= htmlspecialchars($invoice['cnf_agent_address1_from_db'] ?? '') ?><?= htmlspecialchars($invoice['cnf_agent_address2_from_db'] ?? '') ?>, কে ক্ষমতা অর্পণ করা হলো এবং উক্ত এলসিভুক্ত পণ্যগুলি খালাসের লক্ষ্যে নিম্নলিখিত দলিলাদি দাখিল করা হলো।
        </p>

        <p style="margin-top: .3em; margin-bottom: 0.5em; font-size:14pt;"><u>দলিলাদির বিবরণ:</u></p>

        <ol style="margin-left: 20px; font-size: 12pt; list-style-type: bengali;"> <li>Bill of Lading No.: <?= htmlspecialchars($invoice['bl_number'] ?? 'N/A') ?> Dtd. <?= $bl_date_letter ?></li>
            <li>Invoice No: <?= htmlspecialchars($invoice['commercial_invoice_no'] ?? 'N/A') ?> Dtd. <?= $commercial_invoice_date_letter ?></li>
            <li>Packing List</li>
            <li>Country of Origin Certificate,</li>
            <li>L/C, LCA, Bill of Exchange,</li>
            <li>Insurance, Etc.</li>
        </ol>

        <div class="closing" style="margin-top: -0.3em; float: left;"> <p style="font-size:16pt;">ধন্যবাদান্তে,</p>
        </div>

       <div class="signature-block" style="margin-top: 1em; float: right; text-align: center;"> 
            <p style="font-size:16pt;">আপনার বিশ্বস্ত</p>
          
           </div> </main>
<?php include 'footer.php'; ?>
</div>
  </body>
</html>