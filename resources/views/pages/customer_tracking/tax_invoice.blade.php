<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tax Invoice &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; GTMS</title>
  <style>
    /* Reset & Page Setup */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      background-color: #525659;
      color: #000;
      -webkit-font-smoothing: antialiased;
      padding: 20px 0;
    }

    /* Screen Action Bar */
    .no-print-bar {
      width: 210mm;
      margin: 0 auto 15px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0F1E4D;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      font-family: system-ui, -apple-system, sans-serif;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
    }
    .no-print-bar a, .no-print-bar button {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.15s ease;
    }
    .no-print-bar button:hover, .no-print-bar a:hover {
      background: #1d4ed8;
    }
    .no-print-bar a.secondary {
      background: #334155;
    }
    .no-print-bar a.secondary:hover {
      background: #1e293b;
    }

    /* A4 Paper Sheet */
    .sheet {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      padding: 14mm 14mm 10mm 14mm;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
      position: relative;
    }

    /* Outer Wrapper Table */
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      table-layout: fixed;
    }
    .invoice-table td, .invoice-table th {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: middle;
      font-size: 11pt;
      line-height: 1.25;
    }

    /* Header Section */
    .header-logo-cell {
      text-align: center;
      padding: 8px 6px 4px 6px !important;
      border-bottom: 1px solid #000;
    }
    .company-logo {
      width: 115px;
      height: auto;
      display: block;
      margin: 0 auto 4px auto;
    }
    .company-title {
      font-size: 11pt;
      font-weight: bold;
      text-align: left;
      margin-bottom: 2px;
    }
    .iso-tag {
      font-size: 10.5pt;
      font-weight: bold;
      text-align: left;
      margin-bottom: 2px;
    }
    .company-address {
      font-size: 10.5pt;
      line-height: 1.35;
      text-align: left;
    }

    .title-cell {
      text-align: center;
      vertical-align: middle;
      padding: 10px 0 !important;
    }
    .tax-invoice-heading {
      color: #c00000;
      font-size: 22pt;
      font-weight: bold;
      letter-spacing: 0.5px;
      display: inline-block;
    }

    /* Meta Table inside right column */
    .meta-subtable {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }
    .meta-subtable td {
      border: none;
      border-top: 1px solid #000;
      border-bottom: none;
      padding: 3.5px 6px;
      font-size: 10.5pt;
    }
    .meta-subtable tr:first-child td {
      border-top: 1px solid #000;
    }
    .meta-subtable td.label-col {
      font-weight: bold;
      width: 32%;
      border-right: 1px solid #000;
    }
    .meta-subtable td.val-col {
      width: 68%;
    }

    /* Information Rows */
    .row-label {
      font-weight: bold;
      font-size: 10.5pt;
      width: 25%;
      letter-spacing: 0.2px;
    }
    .client-name-val {
      color: #c00000;
      font-weight: bold;
      font-style: italic;
      font-size: 12pt;
    }
    .italic-val {
      font-style: italic;
    }
    .bold-italic-val {
      font-weight: bold;
      font-style: italic;
    }

    /* Items / Work Description Table */
    .items-header th {
      font-size: 11pt;
      font-weight: bold;
      text-align: center;
      padding: 6px;
    }
    .work-desc-cell {
      vertical-align: top;
      padding: 12px 14px !important;
      min-height: 110px;
    }
    .work-desc-list {
      list-style-type: none;
      padding-left: 0;
      margin: 0;
    }
    .work-desc-list li {
      position: relative;
      padding-left: 18px;
      margin-bottom: 7px;
      font-size: 10.5pt;
      line-height: 1.35;
    }
    .work-desc-list li::before {
      content: "•";
      position: absolute;
      left: 2px;
      font-size: 14pt;
      line-height: 1;
      top: -1px;
    }
    .amount-cell {
      text-align: center;
      vertical-align: middle;
      font-size: 11pt;
      font-weight: normal;
    }

    /* Tax & Total rows */
    .tax-label {
      text-align: right;
      font-weight: normal;
      font-size: 10.5pt;
      padding-right: 15px !important;
    }
    .total-row td {
      font-weight: bold;
      font-size: 11pt;
      padding: 6px !important;
    }
    .total-label {
      text-align: center;
      font-weight: bold;
    }

    /* Bank & Signatory Section */
    .footer-section {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }
    .footer-section td {
      border: none;
      vertical-align: bottom;
      padding: 0;
    }
    .bank-table {
      width: 90%;
      border-collapse: collapse;
      border: 1px solid #000;
      margin-top: 15px;
    }
    .bank-table td {
      border: 1px solid #000;
      padding: 4px 6px;
      font-size: 10pt;
      height: 23px;
    }
    .bank-table td.b-label {
      font-weight: bold;
      width: 32%;
    }

    .signatory-box {
      text-align: center;
      padding: 10px 10px 5px 10px;
      float: right;
      width: 220px;
    }
    .stamp-img {
      width: 165px;
      height: auto;
      display: block;
      margin: 0 auto 3px auto;
    }
    .signatory-name {
      font-size: 10.5pt;
      font-weight: bold;
      line-height: 1.3;
    }
    .signatory-title {
      font-size: 10pt;
      line-height: 1.3;
    }

    /* Print Styles */
    @media print {
      @page {
        size: A4 portrait;
        margin: 10mm;
      }
      body {
        background: transparent;
        padding: 0;
      }
      .no-print-bar {
        display: none !important;
      }
      .sheet {
        box-shadow: none;
        margin: 0;
        padding: 0;
        width: 100%;
        min-height: auto;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Action Bar -->
  <div class="no-print-bar">
    <div>
      <strong>GTMS Official Tax Invoice</strong> &nbsp;|&nbsp; Ref: {{ $invoiceNo }} &bull; {{ $customer->company_name ?: $customer->customer_name }}
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('customer-tracking.show', $customer->slug ?? $customer->id) }}" class="secondary">
        &larr; Back to Customer 360
      </a>
      <a href="{{ route('customer-tracking.proforma-invoice', $customer->slug ?? $customer->id) }}" class="secondary">
        View Proforma Invoice &rarr;
      </a>
      <button onclick="window.print()">
        🖨️ Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- A4 Document Sheet -->
  <div class="sheet">
    <table class="invoice-table">
      <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
      </colgroup>

      <!-- Row 1: Company Header (Left) & Tax Invoice Meta (Right) -->
      <tr>
        <td style="padding: 10px 12px; vertical-align: top;">
          <div style="text-align: center; margin-bottom: 6px;">
            <img src="{{ asset('images/invoices/gtms_logo.png') }}" class="company-logo" alt="GTMS Logo">
          </div>
          <div class="company-title">GEO TECHNICAL MINING SOLUTIONS</div>
          <div class="iso-tag">AN ISO 9001 : 2015 CERTIFIED COMPANY</div>
          <div class="company-address">
            1/237, AR Complex, Salem Main Road,<br>
            Meyyanur, Salem – 636 004, Tamil Nadu, India.<br>
            GSTIN: 33ABCDE1234F1Z5 &bull; PAN: ABCDE1234F<br>
            Mobile: +91 94432 12345 / 94432 54321<br>
            Email: info@gtmsmining.com &bull; Web: www.gtmsmining.com
          </div>
        </td>

        <td style="vertical-align: top; padding: 0;">
          <div class="title-cell">
            <span class="tax-invoice-heading">TAX INVOICE</span>
          </div>
          <table class="meta-subtable">
            <tr>
              <td class="label-col">INVOICE NO.</td>
              <td class="val-col">{{ $invoiceNo }}</td>
            </tr>
            <tr>
              <td class="label-col">DATE</td>
              <td class="val-col">{{ $invoiceDate }}</td>
            </tr>
            <tr>
              <td class="label-col">REVERSE CHARGE</td>
              <td class="val-col">No</td>
            </tr>
            <tr>
              <td class="label-col">STATE</td>
              <td class="val-col">Tamil Nadu (Code : 33)</td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Client Name -->
      <tr>
        <td class="row-label">CLIENT NAME</td>
        <td class="client-name-val">{{ strtoupper($customer->company_name ?: $customer->customer_name) }}</td>
      </tr>

      <!-- Client Address -->
      <tr>
        <td class="row-label">CLIENT ADDRESS</td>
        <td class="italic-val">
          {{ $customer->address ?: ($customer->area ? $customer->area . ', ' . $districtName : 'Concession Zone, ' . $districtName . ' District, Tamil Nadu') }}
        </td>
      </tr>

      <!-- GSTIN -->
      <tr>
        <td class="row-label">GSTIN</td>
        <td>{{ $customer->gstin ?: ($customer->pan ? '33' . $customer->pan . '1Z5' : '33AAACK5706F1Z8') }}</td>
      </tr>

      <!-- Work Authorized By -->
      <tr>
        <td class="row-label">WORK AUTHORIZED BY</td>
        <td class="italic-val" style="line-height: 1.35;">
          Dr. S. Karuppannan, M.Sc., Ph.D.,<br>
          Managing Partner,<br>
          Geo Technical Mining Solutions.
        </td>
      </tr>

      <!-- Particulars -->
      <tr>
        <td class="row-label">PARTICULARS</td>
        <td class="bold-italic-val">Statutory Mining Consultancy, Mineral Concession &amp; Environmental Approvals</td>
      </tr>

      <!-- Reference -->
      <tr>
        <td class="row-label">REFERENCE</td>
        <td class="italic-val">{{ $locationDetails }}</td>
      </tr>
    </table>

    <!-- Work Description Table -->
    <table class="invoice-table" style="border-top: none;">
      <colgroup>
        <col style="width: 80%;">
        <col style="width: 20%;">
      </colgroup>
      <tr class="items-header">
        <th style="border-top: none;">WORK DESCRIPTION</th>
        <th style="border-top: none;">AMOUNT (INR)</th>
      </tr>
      <tr>
        <td class="work-desc-cell">
          <ul class="work-desc-list">
            @foreach($items as $item)
              <li>
                <strong>{{ $item['name'] }}</strong>
                @if(!empty($item['sac']))
                  <span style="font-size:9.5pt; color:#475569;">[SAC: {{ $item['sac'] }}]</span>
                @endif
              </li>
            @endforeach
          </ul>
        </td>
        <td class="amount-cell">{{ number_format($subtotal, 2) }}</td>
      </tr>

      <!-- SGST Row -->
      <tr>
        <td class="tax-label">SGST @ 9 %</td>
        <td class="amount-cell">{{ number_format($sgst, 2) }}</td>
      </tr>

      <!-- CGST Row -->
      <tr>
        <td class="tax-label">CGST @ 9 %</td>
        <td class="amount-cell">{{ number_format($cgst, 2) }}</td>
      </tr>

      <!-- Total Row -->
      <tr class="total-row">
        <td class="total-label">TOTAL ({{ $amountInWords }})</td>
        <td class="amount-cell" style="font-weight: bold;">₹ {{ number_format($grandTotal, 2) }}</td>
      </tr>
    </table>

    <!-- Footer: Bank Details (Left) & Signatory Stamp (Right) -->
    <table class="footer-section" style="margin-top: 15px;">
      <tr>
        <!-- Bank Details -->
        <td style="width: 58%; vertical-align: top;">
          <table class="bank-table">
            <tr>
              <td class="b-label">Name</td>
              <td>GEO TECHNICAL MINING SOLUTIONS</td>
            </tr>
            <tr>
              <td class="b-label">Account No.</td>
              <td>38472910482</td>
            </tr>
            <tr>
              <td class="b-label">Bank/Branch</td>
              <td>State Bank of India, Meyyanur, Salem</td>
            </tr>
            <tr>
              <td class="b-label">IFSC Code</td>
              <td>SBIN0001234</td>
            </tr>
          </table>
        </td>

        <!-- Seal & Signatory -->
        <td style="width: 42%; vertical-align: top; text-align: center;">
          <div class="signatory-box">
            <img src="{{ asset('images/invoices/gtms_stamp.png') }}" class="stamp-img" alt="Official Seal">
            <div class="signatory-name">Dr. S. Karuppannan, M.Sc., Ph.D.</div>
            <div class="signatory-title">Managing Partner / RQP</div>
            <div style="font-size:8.5pt; color:#64748b;">(Authorised Signatory)</div>
          </div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
