<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>A4 – Print Document</title>
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
      background: #ccc; /* Grey background to see pages on screen */
      color: #000; font-family: "Times New Roman", Times, serif;
      font-size: 12pt;
      line-height: 1.5;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .page{ 
      width: 210mm; 
      height: 297mm; /* Fixed height for A4 */
      margin: 10mm auto;
      background: #fff; 
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      overflow: hidden; /* Ensure content doesn't break page height */
    }
    .page-break { page-break-before: always; } /* Force page break before this element */

    .page-header, .page-footer {
        flex-shrink: 0; /* Prevent header/footer from shrinking */
    }
    .page-header {
        height: var(--header-height);
        padding: 0mm 16mm 0 16mm;
    }
    .page-footer {
        height: var(--footer-height);
        padding: 0 0 8mm 0;
    }
    .page-header img, .page-footer img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .page-content {
        flex-grow: 1; /* Makes content fill the remaining space */
        padding: 0mm var(--side-padding);
        overflow: hidden; /* Prevents content from spilling out */
        display: flex; /* Use flexbox for vertical alignment */
        flex-direction: column; /* Stack content vertically */
        padding-left:25mm;
    }
    .doc-title{ text-align: center; font-weight: 700; text-decoration: underline; margin: 0 0 10pt 0; text-transform: uppercase; }
    p, li { text-align: justify; }
    ol{ margin: 0; padding-left: 18pt; }
    li{ margin: 0 0 6pt 0; break-inside: avoid; page-break-inside: avoid; } 
    .sig-row{ display: flex; justify-content: space-between; gap: 20pt; margin-top: auto; padding-top: 20pt; } /* Pushes signatures to bottom */
    .sig{ flex: 1 1 0; text-align: center; font-weight: 700; }
    
    /* Styles for the checklist on page 2 */
    ol.checklist { list-style-type: decimal; padding-left: 20px; }
    ol.checklist li { font-size: 14pt; font-weight: bold; margin-bottom: 15px; }

    /* Styles for Authorization Letter */
    .letter-body p {
        font-size: 12pt;
        line-height: 1.6;
        margin-bottom: 1.2em;
        text-align: justify; /* Justify text */
    }
    .letter-body .date, .letter-body .address-block, .letter-body .subject {
        margin-bottom: 1.5em;
        text-align: left; /* Keep these left-aligned */
    }
     .letter-body .closing {
        margin-top: 3em;
        text-align: left; /* Keep these left-aligned */
    }
    .letter-body .signature-block {
        margin-top: 2em;
        text-align: left; /* Keep these left-aligned */
    }
    .letter-body .signature-block p {
        margin-bottom: 0.5em; /* Reduce space between signature lines */
         text-align: left; /* Keep these left-aligned */
    }

    .toolbar{
      position: sticky; top: 0; z-index: 9999; display: flex; gap: 8px; align-items: center;
      padding: 10px 12px; background:#fff; border-bottom:1px solid #e5e7eb;
    }
    .toolbar button{ border:1px solid #e5e7eb; background:#fff; padding:8px 12px; border-radius:10px; cursor:pointer; font-weight:600; }
    
    @page{ size: A4 portrait; margin: 0; }
    @media print{
      body { background: #fff; }
      .page { 
        margin: 0; 
        box-shadow: none;
      }
      .toolbar{ display:none !important; }
      body, .page-content{ background:#fff !important; color:#000 !important; }
      a{ color: #000; text-decoration: underline; }
    }
  </style>
</head>
<body>
  <div class="toolbar">
    <button onclick="window.print()">🖨️ Print Document (3 Pages)</button>
  </div>

  <?php
    // Include the 3 page templates
    // These files will use the variables defined in print_invoice.php
    include 'print_parts/_page_checklist.php';

    include 'print_parts/_page_terms.php';
  ?>

</body>
</html>