<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quotation &bull; {{ $quotation->quotation_number }} &bull; {{ $quotation->company_name ?: $quotation->customer_name }} &bull; GTMS</title>
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

    /* Floating Screen Action Bar */
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

    /* A4 Physical Paper Simulation */
    .sheet {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      padding: 12mm 14mm 10mm 14mm;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
      position: relative;
    }

    /* Master Table Grid */
    .qtn-table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      table-layout: fixed;
    }
    .qtn-table td, .qtn-table th {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: middle;
      font-size: 10pt;
      line-height: 1.25;
    }

    /* Corporate Header Elements */
    .company-logo {
      width: 105px;
      height: auto;
      display: block;
      margin: 0 auto 3px auto;
    }
    .company-title {
      font-size: 11pt;
      font-weight: bold;
      text-align: left;
      margin-bottom: 2px;
      letter-spacing: 0.3px;
    }
    .iso-tag {
      font-size: 9.5pt;
      font-weight: bold;
      text-align: left;
      margin-bottom: 2px;
    }
    .company-address {
      font-size: 9pt;
      line-height: 1.3;
      text-align: left;
    }

    .title-cell {
      text-align: center;
      vertical-align: middle;
      padding: 8px 0 !important;
    }
    .quotation-heading {
      color: #0F1E4D;
      font-size: 20pt;
      font-weight: bold;
      letter-spacing: 1px;
      display: inline-block;
    }
    .quotation-subheading {
      font-size: 9pt;
      font-weight: bold;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 2px;
    }

    /* Metadata Subtable */
    .meta-subtable {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }
    .meta-subtable td {
      border: none;
      border-top: 1px solid #000;
      padding: 3px 5px;
      font-size: 9.5pt;
    }
    .meta-subtable tr:first-child td {
      border-top: 1px solid #000;
    }
    .meta-subtable td.label-col {
      font-weight: bold;
      width: 38%;
      border-right: 1px solid #000;
    }
    .meta-subtable td.val-col {
      width: 62%;
    }

    /* Section Subheaders */
    .section-banner {
      background-color: #f1f5f9;
      font-weight: bold;
      font-size: 9.5pt;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 4px 6px;
      border-top: 1px solid #000;
      border-bottom: 1px solid #000;
    }

    .info-label {
      font-weight: bold;
      font-size: 9.5pt;
      width: 30%;
    }
    .info-val {
      font-size: 9.5pt;
    }
    .client-title {
      font-size: 11pt;
      font-weight: bold;
      color: #0F1E4D;
      font-style: italic;
    }

    /* Items Table */
    .items-header th {
      font-size: 9.5pt;
      font-weight: bold;
      text-align: center;
      background-color: #f8fafc;
      padding: 5px 4px;
    }
    .item-num {
      text-align: center;
      font-size: 9.5pt;
    }
    .item-desc-cell {
      padding: 6px 8px !important;
      font-size: 9.5pt;
    }
    .item-scope-text {
      font-size: 8.5pt;
      color: #334155;
      margin-top: 3px;
      line-height: 1.3;
    }
    .text-center-cell {
      text-align: center;
      font-size: 9.5pt;
    }
    .amount-cell {
      text-align: right;
      font-size: 9.5pt;
      padding-right: 8px !important;
    }

    /* Summary & Totals */
    .tax-summary-label {
      text-align: right;
      font-size: 9.5pt;
      font-weight: normal;
      padding-right: 12px !important;
    }
    .total-row td {
      font-weight: bold;
      font-size: 10.5pt;
      padding: 5px 8px !important;
      background-color: #f8fafc;
    }

    /* Terms & Footer */
    .terms-heading {
      font-weight: bold;
      font-size: 9.5pt;
      text-decoration: underline;
      margin-bottom: 3px;
    }
    .terms-text {
      font-size: 8.5pt;
      line-height: 1.35;
      white-space: pre-line;
      color: #1e293b;
    }

    .bank-table {
      width: 95%;
      border-collapse: collapse;
      border: 1px solid #000;
      margin-top: 6px;
    }
    .bank-table td {
      border: 1px solid #000;
      padding: 3px 5px;
      font-size: 8.5pt;
    }
    .bank-table td.b-label {
      font-weight: bold;
      width: 32%;
    }

    .signatory-box {
      text-align: center;
      padding: 6px 10px;
      float: right;
      width: 220px;
    }
    .stamp-img {
      width: 145px;
      height: auto;
      display: block;
      margin: 0 auto 3px auto;
    }
    .signatory-name {
      font-size: 10pt;
      font-weight: bold;
      line-height: 1.25;
    }
    .signatory-title {
      font-size: 9pt;
      line-height: 1.2;
    }

    /* Print CSS Media Queries */
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
      <strong>GTMS Statutory Quotation</strong> &nbsp;|&nbsp; Ref: {{ $quotation->quotation_number }} &bull; {{ $quotation->company_name ?: $quotation->customer_name }}
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="secondary">
        &larr; Back to Details
      </a>
      <a href="{{ route('accounts.quotations.index') }}" class="secondary">
        Quotations Directory
      </a>
      <button onclick="window.print()">
        🖨️ Print Standalone A4 / Save as PDF
      </button>
    </div>
  </div>

  <!-- A4 Document Sheet -->
  <div class="sheet">
    <table class="qtn-table">
      <colgroup>
        <col style="width: 52%;">
        <col style="width: 48%;">
      </colgroup>

      <!-- Row 1: Corporate Letterhead & Document Title -->
      <tr>
        <td style="padding: 8px 10px; vertical-align: top;">
          <div style="text-align: center; margin-bottom: 4px;">
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
            <span class="quotation-heading">QUOTATION</span>
            <div class="quotation-subheading">Statutory Mining Consultancy & Engineering Services</div>
          </div>
          <table class="meta-subtable">
            <tr>
              <td class="label-col">QUOTATION NO.</td>
              <td class="val-col"><strong>{{ $quotation->quotation_number }}</strong></td>
            </tr>
            <tr>
              <td class="label-col">DATE</td>
              <td class="val-col">{{ $quotation->created_at->format('d-M-Y') }}</td>
            </tr>
            <tr>
              <td class="label-col">OFFER VALIDITY</td>
              <td class="val-col">{{ $quotation->validity_days }} Days (until {{ $quotation->created_at->addDays($quotation->validity_days)->format('d-M-Y') }})</td>
            </tr>
            <tr>
              <td class="label-col">STATE / CODE</td>
              <td class="val-col">Tamil Nadu (Code : 33)</td>
            </tr>
            <tr>
              <td class="label-col">PREPARED BY</td>
              <td class="val-col">Managing Partner / RQP Division</td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Row 2: Client Profile (Left) & Quarry Concession Details (Right) -->
      <tr>
        <td class="section-banner">CLIENT / PROPOSAL TO</td>
        <td class="section-banner">QUARRY CONCESSION &amp; LOCATION</td>
      </tr>

      <tr>
        <!-- Left: Client Info -->
        <td style="vertical-align: top; padding: 8px 10px;">
          <div class="client-title">
            {{ strtoupper($quotation->company_name ?: $quotation->customer_name) }}
          </div>
          @if($quotation->customer_name && $quotation->company_name && $quotation->customer_name !== $quotation->company_name)
            <div style="font-size: 9pt; color: #334155; margin-bottom: 3px;">
              <strong>Kind Attn:</strong> Thiru. {{ $quotation->customer_name }}
            </div>
          @endif
          <div style="font-size: 9pt; line-height: 1.35; margin-top: 4px;">
            <strong>Address:</strong> {{ $quotation->address ?: ($quotation->village ? $quotation->village . ', ' . ($quotation->district?->name ?? 'Tamil Nadu') : 'Mining Concession Zone, Tamil Nadu') }}<br>
            @if($quotation->gst_number)
              <strong>GSTIN:</strong> {{ $quotation->gst_number }}<br>
            @endif
            @if($quotation->phone)
              <strong>Contact:</strong> {{ $quotation->phone }}<br>
            @endif
            @if($quotation->email)
              <strong>Email:</strong> {{ $quotation->email }}
            @endif
          </div>
        </td>

        <!-- Right: Quarry Info -->
        <td style="vertical-align: top; padding: 8px 10px;">
          <div style="font-size: 10pt; font-weight: bold; color: #0F1E4D; margin-bottom: 3px;">
            {{ $quotation->quarry_name ?: ($quotation->village ? $quotation->village . ' Quarry' : 'Quarry Concession') }}
          </div>
          <div style="font-size: 9pt; line-height: 1.35;">
            <strong>S.F. Numbers:</strong> {{ $quotation->survey_numbers ?: 'N/A' }}<br>
            <strong>Village / Taluk:</strong> {{ $quotation->village ?: 'N/A' }}, {{ $quotation->taluk ?: 'N/A' }}<br>
            <strong>District:</strong> {{ $quotation->district?->name ?? 'Tamil Nadu' }}<br>
            <strong>Sanctioned Extent:</strong> {{ $quotation->area_extent_ha ? number_format($quotation->area_extent_ha, 4) . ' Hectares' : 'N/A' }}<br>
            <strong>Target Mineral:</strong> {{ $quotation->mineral_name ?: 'Rough Stone / Multi-Coloured Granite' }}
          </div>
        </td>
      </tr>
    </table>

    <!-- Table 2: Services Scope & Line Items Breakdown -->
    <table class="qtn-table" style="border-top: none;">
      <colgroup>
        <col style="width: 5%;">
        <col style="width: 45%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
      </colgroup>
      <tr class="items-header">
        <th style="border-top: none;">S.No</th>
        <th style="border-top: none;">STATUTORY SERVICE DESCRIPTION &amp; SCOPE</th>
        <th style="border-top: none;">SAC CODE</th>
        <th style="border-top: none;">QTY / AREA</th>
        <th style="border-top: none;">UNIT</th>
        <th style="border-top: none;">RATE (INR)</th>
        <th style="border-top: none;">SUBTOTAL (INR)</th>
      </tr>

      @foreach($quotation->items as $item)
      <tr>
        <td class="item-num">{{ $loop->iteration }}</td>
        <td class="item-desc-cell">
          <strong>{{ $item->service_name }}</strong>
          @if($item->description)
            <div class="item-scope-text">{{ $item->description }}</div>
          @endif
        </td>
        <td class="text-center-cell">{{ $item->sac_code ?: '998341' }}</td>
        <td class="text-center-cell">{{ number_format($item->quantity, 2) }}</td>
        <td class="text-center-cell">{{ $item->unit ?: 'Nos' }}</td>
        <td class="amount-cell">{{ number_format($item->unit_rate, 2) }}</td>
        <td class="amount-cell">{{ number_format($item->subtotal, 2) }}</td>
      </tr>
      @endforeach

      <!-- Subtotal Row -->
      <tr>
        <td colspan="6" class="tax-summary-label"><strong>SERVICES SUBTOTAL</strong></td>
        <td class="amount-cell"><strong>{{ number_format($quotation->subtotal, 2) }}</strong></td>
      </tr>

      <!-- CGST Row -->
      <tr>
        <td colspan="6" class="tax-summary-label">CGST @ {{ number_format($quotation->tax_rate / 2, 2) }} %</td>
        <td class="amount-cell">{{ number_format($quotation->tax_amount / 2, 2) }}</td>
      </tr>

      <!-- SGST Row -->
      <tr>
        <td colspan="6" class="tax-summary-label">SGST @ {{ number_format($quotation->tax_rate / 2, 2) }} %</td>
        <td class="amount-cell">{{ number_format($quotation->tax_amount / 2, 2) }}</td>
      </tr>

      <!-- Total Row -->
      <tr class="total-row">
        <td colspan="6" style="text-align: right; padding-right: 12px !important;">
          TOTAL QUOTATION VALUE (INR):
        </td>
        <td class="amount-cell" style="font-weight: bold; font-size: 11pt;">
          ₹ {{ number_format($quotation->total_amount, 2) }}
        </td>
      </tr>

      <!-- Amount in Words Row -->
      <tr>
        <td colspan="7" style="padding: 6px 10px; background-color: #fcfcfc;">
          <strong>Amount Chargeable (in words):</strong>
          <span style="font-style: italic; font-weight: bold; color: #0F1E4D;">{{ $quotation->amount_in_words }}</span>
        </td>
      </tr>
    </table>

    <!-- Commercial Terms & Statutory Exclusions Section -->
    <table class="qtn-table" style="border-top: none;">
      <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
      </colgroup>
      <tr>
        <td style="vertical-align: top; padding: 6px 10px;">
          <div class="terms-heading">MILESTONE PAYMENT TERMS:</div>
          <div class="terms-text">{{ $quotation->payment_terms ?: "1. 50% Mobilization advance along with confirmed work order.\n2. 30% upon preparation & submission of draft statutory mining / environmental documentation.\n3. 20% upon final statutory clearance & dispatch of statutory order copies." }}</div>
        </td>
        <td style="vertical-align: top; padding: 6px 10px;">
          <div class="terms-heading">GOVERNMENT STATUTORY EXCLUSIONS:</div>
          <div class="terms-text">{{ $quotation->exclusions ?: "1. Statutory government scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans.\n2. In-person client representation before statutory committees if required." }}</div>
        </td>
      </tr>
    </table>

    <!-- Bank Coordinates & Signatory Footer -->
    <table class="qtn-table" style="border-top: none;">
      <colgroup>
        <col style="width: 55%;">
        <col style="width: 45%;">
      </colgroup>
      <tr>
        <!-- Bank Details -->
        <td style="vertical-align: top; padding: 6px 10px;">
          <div style="font-weight: bold; font-size: 9pt; text-transform: uppercase;">Bank Remittance Coordinates:</div>
          <table class="bank-table">
            <tr>
              <td class="b-label">Beneficiary Name</td>
              <td><strong>GEO TECHNICAL MINING SOLUTIONS</strong></td>
            </tr>
            <tr>
              <td class="b-label">Current Account No.</td>
              <td><strong>38472910482</strong></td>
            </tr>
            <tr>
              <td class="b-label">Bank &amp; Branch</td>
              <td>State Bank of India, Meyyanur Branch, Salem</td>
            </tr>
            <tr>
              <td class="b-label">IFSC Code</td>
              <td><strong>SBIN0001234</strong></td>
            </tr>
          </table>
        </td>

        <!-- Seal & Signatory -->
        <td style="vertical-align: top; text-align: center; padding: 6px 10px;">
          <div class="signatory-box">
            <img src="{{ asset('images/invoices/gtms_stamp.png') }}" class="stamp-img" alt="Official Seal">
            <div class="signatory-name">Dr. S. Karuppannan, M.Sc., Ph.D.</div>
            <div class="signatory-title">Managing Partner / RQP</div>
            <div style="font-size: 8pt; color: #475569;">(Authorised Signatory)</div>
          </div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
