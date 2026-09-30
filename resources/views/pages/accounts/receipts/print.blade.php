<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt Voucher &bull; {{ $receipt->receipt_number }} &bull; {{ $receipt->customer->company_name ?: $receipt->customer->customer_name }} &bull; GTMS</title>
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
    .format-btn {
      background: #1e293b;
      padding: 6px 12px;
      font-size: 13px;
    }
    .format-btn.active {
      background: #0284c7;
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

    /* A5 Landscape Mode */
    .sheet.a5-mode {
      width: 210mm;
      min-height: 148mm;
      padding: 8mm 10mm;
    }

    /* Master Table Grid */
    .receipt-table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      table-layout: fixed;
    }
    .receipt-table td, .receipt-table th {
      border: 1px solid #000;
      padding: 5px 8px;
      vertical-align: middle;
      font-size: 10pt;
      line-height: 1.3;
    }

    /* Corporate Header Elements */
    .company-logo {
      width: 95px;
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
      font-size: 8.5pt;
      line-height: 1.25;
      text-align: left;
    }

    .title-cell {
      text-align: center;
      vertical-align: middle;
      padding: 6px 0 !important;
      background-color: #f8fafc;
    }
    .receipt-heading {
      color: #0F1E4D;
      font-size: 16pt;
      font-weight: bold;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }
    .receipt-subheading {
      font-size: 9pt;
      font-style: italic;
      color: #334155;
    }

    /* Information Sections */
    .section-header-cell {
      background-color: #f1f5f9;
      font-weight: bold;
      font-size: 9.5pt;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 4px 8px !important;
    }
    .f-label {
      font-weight: bold;
      width: 25%;
      font-size: 9.5pt;
      background-color: #fafafa;
    }
    .f-val {
      font-size: 9.5pt;
    }

    .amount-box {
      border: 2px solid #000;
      padding: 6px 12px;
      display: inline-block;
      font-weight: bold;
      font-size: 14pt;
      background-color: #f8fafc;
      letter-spacing: 0.5px;
    }

    .words-text {
      font-size: 10pt;
      font-weight: bold;
      color: #0F1E4D;
      line-height: 1.35;
    }

    /* Statement of Account Inner Table */
    .soa-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2px;
    }
    .soa-table td, .soa-table th {
      border: 1px solid #000;
      padding: 4px 8px;
      font-size: 9pt;
    }
    .soa-table th {
      background-color: #f8fafc;
      font-weight: bold;
      text-align: center;
    }
    .soa-table td.num {
      text-align: right;
    }

    /* Signatory Zone */
    .signatory-box {
      text-align: center;
      padding: 4px 10px;
      float: right;
      width: 220px;
    }
    .stamp-img {
      width: 110px;
      height: auto;
      display: block;
      margin: 0 auto 2px auto;
    }
    .signatory-name {
      font-size: 9.5pt;
      font-weight: bold;
      line-height: 1.25;
    }
    .signatory-title {
      font-size: 8.5pt;
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
      .sheet.a5-mode {
        width: 100%;
        min-height: auto;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Screen Action Bar -->
  <div class="no-print-bar">
    <div>
      <strong>GTMS Official Receipt Voucher</strong> &nbsp;|&nbsp;
      Ref: <strong>{{ $receipt->receipt_number }}</strong> &bull;
      {{ $receipt->customer->company_name ?: $receipt->customer->customer_name }}
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="secondary">
        &larr; Back to Details
      </a>
      <a href="{{ route('accounts.receipts.index') }}" class="secondary">
        Receipts List
      </a>
      <button onclick="window.print()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/>
        </svg>
        Print Voucher
      </button>
    </div>
  </div>

  <!-- Physical Paper Sheet -->
  <div class="sheet" id="printSheet">
    <table class="receipt-table">
      <!-- 1. Corporate Header -->
      <tr>
        <td style="width: 22%; text-align: center; border-right: none;">
          @if(file_exists(public_path('images/invoices/gtms_logo.png')))
            <img src="{{ asset('images/invoices/gtms_logo.png') }}" alt="GTMS Insignia" class="company-logo">
          @else
            <div style="font-weight: bold; font-size: 16pt; color: #0F1E4D;">GTMS</div>
          @endif
        </td>
        <td colspan="3" style="width: 78%; border-left: none;">
          <div class="company-title">GRANITE / MINING TRACKING MANAGEMENT SYSTEM (GTMS)</div>
          <div class="iso-tag">An ISO 9001:2015 Certified Statutory Mining &amp; Environmental Consultancy</div>
          <div class="company-address">
            Corporate Headquarters: No. 12, Mining Corridor, Fairlands, Salem - 636 016, Tamil Nadu, India<br>
            GSTIN: <strong>33AAACG1234F1Z5</strong> &bull; PAN: <strong>AAACG1234F</strong> &bull; Web: www.gtms-tn.gov.in &bull; Email: accounts@gtms.in
          </div>
        </td>
      </tr>

      <!-- 2. Document Title & Voucher Number -->
      <tr>
        <td colspan="4" class="title-cell">
          <div class="receipt-heading">OFFICIAL MONEY RECEIPT</div>
          <div class="receipt-subheading">Statutory Mining Processing &amp; Technical Consultation Payment Voucher</div>
        </td>
      </tr>

      <!-- 3. Voucher Reference & Date Row -->
      <tr>
        <td class="f-label">Receipt Voucher No:</td>
        <td style="width: 30%;">
          <strong style="font-size: 11pt; color: #0F1E4D;">{{ $receipt->receipt_number }}</strong>
        </td>
        <td class="f-label" style="width: 20%;">Date of Payment:</td>
        <td style="width: 25%;">
          <strong style="font-size: 10pt;">
            {{ $receipt->transaction_date ? $receipt->transaction_date->format('d/m/Y') : date('d/m/Y') }}
          </strong>
        </td>
      </tr>

      <!-- 4. Payer Details (Received From) -->
      <tr>
        <td colspan="4" class="section-header-cell">1. Payer Particulars (Received With Thanks From)</td>
      </tr>
      <tr>
        <td class="f-label">Client / Quarry Operator:</td>
        <td colspan="3" class="f-val">
          <strong style="font-size: 11pt;">{{ $receipt->customer->company_name ?: $receipt->customer->customer_name }}</strong>
          @if($receipt->customer->company_name && $receipt->customer->customer_name)
            &nbsp; (Proprietor / Managing Partner: {{ $receipt->customer->customer_name }})
          @endif
        </td>
      </tr>
      <tr>
        <td class="f-label">Address &amp; Location:</td>
        <td colspan="3" class="f-val">
          {{ $receipt->customer->address ?: 'Tamil Nadu, India' }}
          @if($receipt->customer->district)
            &bull; District: {{ $receipt->customer->district->name }}
          @endif
        </td>
      </tr>
      <tr>
        <td class="f-label">Mobile Contact:</td>
        <td>{{ $receipt->customer->mobile_num ?: 'N/A' }}</td>
        <td class="f-label">Client GSTIN / PAN:</td>
        <td>
          {{ $receipt->customer->gstin ?: ($receipt->customer->pan ?: 'Unregistered / Exempt') }}
        </td>
      </tr>

      <!-- 5. Statutory Application & Concession Details -->
      <tr>
        <td colspan="4" class="section-header-cell">2. Statutory Service Account &amp; Concession Reference</td>
      </tr>
      <tr>
        <td class="f-label">Statutory Service:</td>
        <td colspan="3" class="f-val">
          <strong style="color: #0F1E4D;">
            @php
              $typeLabels = [
                'lease'          => 'Lease Application & Quarry Concession Processing',
                'mining'         => 'Mining Plan Preparation, Progressive Mine Closure & RQP Representation',
                'environment'    => 'Environmental Clearance (Form-1 / Form-2 / EIA / SEIAA Presentation)',
                'ppt'            => 'SEAC / SEIAA Technical Presentation & Defense Dossier',
                'dgps'           => 'DGPS Demarcation, Boundary Pillar Fixing & Geo-referencing',
                'drone'          => 'Drone Photogrammetry, 3D Topo Volumetric Survey & Contouring',
                'ec'             => 'Environmental Clearance Certificate Processing',
                'ec_certificate' => 'Environmental Clearance Certificate Processing',
                'ec_compliance'  => 'Half-Yearly Environmental Compliance Monitoring & Filing',
                'general'        => 'General Direct Payment / Retainer Advance on Account',
              ];
              $appLabel = $typeLabels[strtolower($receipt->application_type)] ?? ucfirst($receipt->application_type ?: 'General Payment');
            @endphp
            {{ $appLabel }}
          </strong>
        </td>
      </tr>
      <tr>
        <td class="f-label">Application / File Ref:</td>
        <td>
          <strong>{{ $receipt->application_reference }}</strong>
        </td>
        <td class="f-label">Linked Concession / S.F.:</td>
        <td>
          @php
            $concessionText = 'N/A';
            if ($receipt->application) {
              $app = $receipt->application;
              $parts = array_filter([
                $app->survey_numbers_text ?? null,
                $app->village ?? null,
                $app->taluk ?? null,
                $app->location ?? null
              ]);
              if (!empty($parts)) {
                $concessionText = implode(', ', $parts);
              }
            }
          @endphp
          {{ $concessionText }}
        </td>
      </tr>

      <!-- 6. Payment Instrument Particulars -->
      <tr>
        <td colspan="4" class="section-header-cell">3. Payment Instrument Particulars</td>
      </tr>
      <tr>
        <td class="f-label">Payment Mode:</td>
        <td>
          <strong style="font-size: 10.5pt;">{{ $receipt->payment_mode }}</strong>
        </td>
        <td class="f-label">Bank Name:</td>
        <td>
          {{ $receipt->bank_name ?: 'Direct Depository / Cash' }}
        </td>
      </tr>
      <tr>
        <td class="f-label">UTR / Cheque / Ref No:</td>
        <td colspan="3" class="f-val">
          <strong style="letter-spacing: 0.5px;">{{ $receipt->reference_number ?: 'N/A (Cash / Counter Settlement)' }}</strong>
        </td>
      </tr>

      <!-- 7. Amount Paid & Words -->
      <tr>
        <td colspan="4" class="section-header-cell">4. Amount Collected &amp; Realized</td>
      </tr>
      <tr>
        <td class="f-label" style="vertical-align: middle;">Amount in Figures:</td>
        <td colspan="3">
          <div class="amount-box">
            ₹ {{ number_format($receipt->amount_paid, 2) }}
          </div>
        </td>
      </tr>
      <tr>
        <td class="f-label">Amount in Words:</td>
        <td colspan="3">
          <div class="words-text">
            {{ $receipt->amount_in_words }}
          </div>
        </td>
      </tr>

      <!-- 8. Statement of Application Account Table -->
      <tr>
        <td colspan="4" class="section-header-cell">5. Statement of Account for this Application</td>
      </tr>
      <tr>
        <td colspan="4" style="padding: 0;">
          <table class="soa-table">
            <thead>
              <tr>
                <th style="width: 25%;">Total Agreed Value (₹)</th>
                <th style="width: 25%;">Previously Paid (₹)</th>
                <th style="width: 25%;">Current Payment (₹)</th>
                <th style="width: 25%;">Remaining Balance Due (₹)</th>
              </tr>
            </thead>
            <tbody>
              @php
                $agreedVal = $receipt->application?->product_value
                  ?? ($receipt->previous_paid + $receipt->amount_paid + $receipt->balance_due);
              @endphp
              <tr>
                <td class="num">₹ {{ number_format($agreedVal, 2) }}</td>
                <td class="num">₹ {{ number_format($receipt->previous_paid, 2) }}</td>
                <td class="num" style="font-weight: bold; background-color: #f0fdf4;">₹ {{ number_format($receipt->amount_paid, 2) }}</td>
                <td class="num" style="font-weight: bold; {{ $receipt->balance_due > 0 ? 'color: #dc2626;' : 'color: #15803d;' }}">
                  @if($receipt->balance_due > 0)
                    ₹ {{ number_format($receipt->balance_due, 2) }}
                  @else
                    NIL (Settled)
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>

      @if($receipt->notes)
      <tr>
        <td class="f-label">Narration / Notes:</td>
        <td colspan="3" class="f-val" style="font-style: italic;">
          {{ $receipt->notes }}
        </td>
      </tr>
      @endif

      <!-- 9. Signatory & Official Seal Zone -->
      <tr>
        <td colspan="2" style="vertical-align: top; border-right: none; padding-top: 10px;">
          <div style="font-size: 8.5pt; color: #334155; line-height: 1.35;">
            <strong>Terms &amp; Statutory Acknowledgement:</strong><br>
            1. All statutory fees paid are acknowledged subject to realization of Cheque / NEFT / RTGS transfers.<br>
            2. Government challan payments (SEIAA, TNPCB, Geology) are processed strictly in accordance with approved estimates.<br>
            3. This receipt voucher is an official financial instrument issued under the GTMS enterprise framework.<br>
            <br>
            <strong>Issued By Officer:</strong> {{ $receipt->creator->name ?? 'System Officer' }} &bull; Branch: {{ $receipt->branch->name ?? 'Head Office' }}<br>
            <strong>Generated At:</strong> {{ $receipt->created_at ? $receipt->created_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s') }}
          </div>
        </td>
        <td colspan="2" style="vertical-align: bottom; border-left: none; padding-bottom: 8px;">
          <div class="signatory-box">
            @if(file_exists(public_path('images/invoices/gtms_stamp.png')))
              <img src="{{ asset('images/invoices/gtms_stamp.png') }}" alt="GTMS Official Seal" class="stamp-img">
            @endif
            <div class="signatory-name">For GRANITE / MINING TRACKING MANAGEMENT SYSTEM</div>
            <div class="signatory-title">Authorized Signatory &bull; Accounts Department</div>
          </div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
