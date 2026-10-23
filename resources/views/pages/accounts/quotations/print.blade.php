<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quotation - {{ $quotation->quotation_number }} - {{ $quotation->company_name ?: $quotation->customer_name }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: "Times New Roman", Times, serif;
      background-color: #525659;
      color: #000000;
      line-height: 1.4;
      padding: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
    }

    /* Screen Action Bar (Hidden in Print) */
    .screen-action-bar {
      width: 210mm;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0F1E4D;
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.25);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .screen-action-bar a, .screen-action-bar button {
      background: #2563EB;
      color: #ffffff;
      border: none;
      padding: 8px 16px;
      border-radius: 5px;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: background 0.15s ease;
    }
    .screen-action-bar a:hover, .screen-action-bar button:hover {
      background: #1D4ED8;
    }
    .screen-action-bar a.btn-back {
      background: #334155;
    }
    .screen-action-bar a.btn-back:hover {
      background: #1E293B;
    }

    /* A4 Physical Sheet Simulation */
    .page {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      padding: 12mm 15mm 15mm 15mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      position: relative;
      page-break-after: always;
    }
    .page:last-child {
      page-break-after: auto;
    }

    /* GTMS Authentic Letterhead Header */
    .gtms-letterhead {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 16px;
      padding-bottom: 8px;
      border-bottom: 2px solid #BA1B1F;
      margin-bottom: 14px;
    }
    .gtms-letterhead-logo {
      flex: 0 0 110px;
      max-width: 110px;
      text-align: center;
    }
    .gtms-letterhead-logo img {
      width: 100%;
      height: auto;
      max-height: 88px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }
    .gtms-letterhead-content {
      flex: 1;
      text-align: center;
      font-family: "Times New Roman", Times, serif;
    }
    .gtms-letterhead-title {
      font-size: 22px;
      font-weight: bold;
      color: #BA1B1F;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
      line-height: 1.15;
    }
    .gtms-letterhead-accreditation {
      font-size: 13.5px;
      font-weight: bold;
      color: #000000;
      margin-bottom: 3px;
      line-height: 1.25;
    }
    .gtms-letterhead-address {
      font-size: 12.5px;
      color: #000000;
      line-height: 1.35;
      margin-bottom: 2px;
    }
    .gtms-letterhead-contact {
      font-size: 12px;
      color: #000000;
      line-height: 1.35;
    }

    .doc-title {
      text-align: center;
      font-size: 21px;
      font-weight: bold;
      text-decoration: underline;
      letter-spacing: 1.5px;
      margin-bottom: 14px;
    }
    .meta-bar {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13.5px;
    }
    table, th, td {
      border: 1px solid #000000;
    }
    th, td {
      padding: 6px 8px;
    }

    .consignee-table {
      margin-bottom: 16px;
    }
    .consignee-table td.header-cell {
      font-weight: bold;
      width: 42%;
      vertical-align: top;
    }
    .consignee-table td.value-cell {
      width: 58%;
      vertical-align: top;
    }
    .subject-row td {
      padding: 6px 8px;
    }

    .section-heading-main {
      font-size: 15px;
      font-weight: bold;
      text-decoration: underline;
      margin: 12px 0 10px 0;
    }
    .section-heading {
      font-size: 14px;
      font-weight: bold;
      text-decoration: underline;
      margin: 10px 0 4px 0;
    }
    ul.scope-list {
      margin-left: 24px;
      margin-bottom: 8px;
      list-style-type: disc;
    }
    ul.scope-list li {
      margin-bottom: 3px;
      font-size: 13.5px;
    }

    .cost-table {
      margin-top: 8px;
      margin-bottom: 10px;
      font-size: 12px;
    }
    .cost-table th {
      background-color: #f8fafc;
      font-weight: bold;
      text-align: center;
      padding: 5px 6px;
      font-size: 11.5px;
    }
    .cost-table td {
      padding: 4px 6px;
      vertical-align: top;
      font-size: 12px;
    }
    .cost-table td.cell-sno {
      width: 6%;
      text-align: center;
      font-weight: 600;
    }
    .cost-table td.cell-desc {
      width: 44%;
      text-align: left;
    }
    .cost-table td.cell-desc .service-title {
      font-weight: bold;
      color: #000000;
      font-size: 12px;
      margin-bottom: 2px;
      line-height: 1.25;
    }
    .cost-table td.cell-desc .service-desc {
      font-size: 10.5px;
      color: #374151;
      line-height: 1.25;
    }
    .cost-table td.cell-qty {
      width: 10%;
      text-align: center;
      font-weight: 500;
    }
    .cost-table td.cell-unit {
      width: 9%;
      text-align: center;
      color: #374151;
    }
    .cost-table td.cell-rate {
      width: 15%;
      text-align: right;
      font-weight: 500;
    }
    .cost-table td.cell-total {
      width: 16%;
      text-align: right;
      font-weight: bold;
    }
    .cost-table tr.row-total td.label {
      text-align: right;
      font-weight: bold;
      font-style: italic;
      padding-right: 10px;
      font-size: 11.5px;
    }
    .cost-table tr.row-total td.val {
      text-align: right;
      font-weight: bold;
      padding-right: 6px;
      font-size: 11.5px;
    }

    .terms-box {
      margin-top: 8px;
      margin-bottom: 10px;
      font-size: 11.5px;
    }
    .terms-box .section-heading {
      font-size: 12.5px;
      margin: 4px 0 2px 0;
    }
    .terms-box ol {
      margin-left: 18px;
      margin-top: 3px;
      margin-bottom: 4px;
    }
    .terms-box li {
      margin-bottom: 3px;
      line-height: 1.25;
    }

    .account-table {
      width: 75%;
      margin-top: 4px;
      font-size: 11.5px;
    }
    .account-table td {
      padding: 3px 6px;
    }
    .account-table td.lbl {
      width: 130px;
      font-weight: bold;
    }

    .page-footer-num {
      text-align: center;
      margin-top: auto;
      font-size: 12px;
      color: #6b7280;
      padding-top: 10px;
    }

    @media print {
      body {
        background: none;
        padding: 0;
        gap: 0;
      }
      .screen-action-bar {
        display: none !important;
      }
      .page {
        box-shadow: none;
        padding: 10mm 12mm;
        margin: 0;
        width: 100%;
        min-height: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Floating Action Bar (Hidden during Print) -->
  <div class="screen-action-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
      <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Quotation
      </a>
      <span style="font-size: 13px; font-weight: 500; opacity: 0.9;">
        Document: <strong>{{ $quotation->quotation_number }}</strong>
      </span>
    </div>
    <div style="display: flex; gap: 10px;">
      <button onclick="window.print()" style="background: #059669;">
        <i class="fas fa-print"></i> Print / Save as PDF
      </button>
    </div>
  </div>

  @php
      // Subject
      $subject = $quotation->subject ?: 'Quotation for Environmental Clearance falls in "B2" Category Project';
      $bank = $quotation->bankAccount ?? \App\Models\BankAccount::where('is_default', true)->first() ?? \App\Models\BankAccount::first();
  @endphp

  <!-- PAGE 1: COMMERCIAL PROPOSAL & SCHEDULE -->
  <div class="page">
    <header class="gtms-letterhead">
      <div class="gtms-letterhead-logo">
        <img src="{{ asset('assets/images/gtms_logo.png') }}" alt="GTMS Logo">
      </div>
      <div class="gtms-letterhead-content">
        <h1 class="gtms-letterhead-title">GEO TECHNICAL MINING SOLUTIONS</h1>
        <div class="gtms-letterhead-accreditation">(A NABET Accredited &amp; ISO Certified)</div>
        <div class="gtms-letterhead-address">
          No: 2/548/D, Thadangam, Thokkampatti (post),<br>
          Dharmapuri-636705. Tamil Nadu.
        </div>
        <div class="gtms-letterhead-contact">
          <div><strong>GSTIN:</strong> 33AAQFG5164R1ZN &nbsp;|&nbsp; <strong>PAN:</strong> AAQFG5164R</div>
          <div><strong>Mob:</strong> +91 9790462882, 7010076633</div>
          <div><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
        </div>
      </div>
    </header>

    <h1 class="doc-title">QUOTATION</h1>

    <div class="meta-bar">
      <span>Quot No : {{ $quotation->quotation_number }}</span>
      <span>Date: {{ $quotation->created_at ? $quotation->created_at->format('d.m.Y') : date('d.m.Y') }}</span>
    </div>

    <table class="consignee-table">
      <tbody>
        <tr>
          <td class="header-cell">Name and Address of the Consignee</td>
          <td class="value-cell">
            <strong>M/s. {{ $quotation->company_name ?: $quotation->customer_name }},</strong><br>
            @if($quotation->address)
              {!! nl2br(e($quotation->address)) !!}<br>
            @else
              @if($quotation->village) {{ $quotation->village }}, @endif
              @if($quotation->taluk) {{ $quotation->taluk }}, @endif
              {{ $quotation->district?->name ?? 'Tamil Nadu' }}<br>
            @endif
            Tamil Nadu State.
            @if($quotation->gst_number)
              <div style="margin-top: 3px;"><strong>GSTIN:</strong> {{ $quotation->gst_number }}</div>
            @endif
          </td>
        </tr>
        <tr>
          <td class="header-cell">Contact Person &amp; Designation</td>
          <td class="value-cell">
            {{ $quotation->customer_name ?: 'Authorized Representative' }}<br>
            {{ $quotation->phone ? '+91 ' . preg_replace('/^\+?91\s*/', '', $quotation->phone) : 'N/A' }}
            @if($quotation->email)
              | {{ $quotation->email }}
            @endif
          </td>
        </tr>
        @if($quotation->quarry_name || $quotation->survey_numbers || $quotation->area_extent_ha)
        <tr>
          <td class="header-cell">Quarry / Project Particulars</td>
          <td class="value-cell">
            @if($quotation->quarry_name)<strong>{{ $quotation->quarry_name }}</strong>@endif
            @if($quotation->survey_numbers) &bull; S.F. No: {{ $quotation->survey_numbers }}@endif
            @if($quotation->area_extent_ha) &bull; Extent: {{ number_format($quotation->area_extent_ha, 4) }} Ha @endif
            @if($quotation->mineral_name) &bull; Mineral: {{ $quotation->mineral_name }}@endif
            @if($quotation->village || $quotation->taluk)
              <br><span style="font-size: 12px; color: #333333;">Location: {{ implode(', ', array_filter([$quotation->village, $quotation->taluk, $quotation->district?->name])) }}</span>
            @endif
          </td>
        </tr>
        @endif
        <tr class="subject-row">
          <td colspan="2">
            <strong>Subject:</strong> {{ $subject }}
          </td>
        </tr>
      </tbody>
    </table>

    <table class="cost-table">
      <thead>
        <tr>
          <th style="width: 6%;">S. No.</th>
          <th style="width: 44%; text-align: left;">Service Description &amp; Statutory Scope</th>
          <th style="width: 10%;">Qty / Extent</th>
          <th style="width: 9%;">Unit</th>
          <th style="width: 15%; text-align: right;">Unit Rate (₹)</th>
          <th style="width: 16%; text-align: right;">Total (₹)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($quotation->items as $index => $item)
          <tr>
            <td class="cell-sno">{{ $loop->iteration }}</td>
            <td class="cell-desc">
              <div class="service-title">{{ $item->service_name }}</div>
              @if($item->description)
                <div class="service-desc">{{ $item->description }}</div>
              @endif
            </td>
            <td class="cell-qty">{{ number_format($item->quantity, 2) }}</td>
            <td class="cell-unit">{{ $item->unit ?: 'Job' }}</td>
            <td class="cell-rate">{{ number_format($item->unit_rate, 2) }}</td>
            <td class="cell-total">{{ number_format($item->subtotal, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td class="cell-sno">1</td>
            <td class="cell-desc">
              <div class="service-title">Statutory Mining Scope Services</div>
            </td>
            <td class="cell-qty">1.00</td>
            <td class="cell-unit">Job</td>
            <td class="cell-rate">{{ number_format($quotation->subtotal, 2) }}</td>
            <td class="cell-total">{{ number_format($quotation->subtotal, 2) }}</td>
          </tr>
        @endforelse

        <tr class="row-total">
          <td colspan="5" class="label">TOTAL</td>
          <td class="val">{{ number_format($quotation->subtotal, 2) }}</td>
        </tr>
        <tr class="row-total">
          <td colspan="5" class="label">SGST &amp; CGST @ {{ number_format($quotation->tax_rate, 0) }}%</td>
          <td class="val">{{ number_format($quotation->tax_amount, 2) }}</td>
        </tr>
        <tr class="row-total">
          <td colspan="5" class="label" style="font-style: normal; text-align: left; padding-left: 8px;">
            TOTAL ({{ $quotation->amount_in_words }})
          </td>
          <td class="val">{{ number_format($quotation->total_amount, 2) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="page-footer-num">-- Page 1 of 2 --</div>
  </div>

  <!-- PAGE 2: TERMS, REMITTANCE & SIGNATORY -->
  <div class="page">
    <header class="gtms-letterhead">
      <div class="gtms-letterhead-logo">
        <img src="{{ asset('assets/images/gtms_logo.png') }}" alt="GTMS Logo">
      </div>
      <div class="gtms-letterhead-content">
        <h1 class="gtms-letterhead-title">GEO TECHNICAL MINING SOLUTIONS</h1>
        <div class="gtms-letterhead-accreditation">(A NABET Accredited &amp; ISO Certified)</div>
        <div class="gtms-letterhead-address">
          No: 2/548/D, Thadangam, Thokkampatti (post),<br>
          Dharmapuri-636705. Tamil Nadu.
        </div>
        <div class="gtms-letterhead-contact">
          <div><strong>GSTIN:</strong> 33AAQFG5164R1ZN &nbsp;|&nbsp; <strong>PAN:</strong> AAQFG5164R</div>
          <div><strong>Mob:</strong> +91 9790462882, 7010076633</div>
          <div><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
        </div>
      </div>
    </header>

    <div class="meta-bar" style="margin-bottom: 16px;">
      <span>Quot No : {{ $quotation->quotation_number }}</span>
      <span>Date: {{ $quotation->created_at ? $quotation->created_at->format('d.m.Y') : date('d.m.Y') }}</span>
    </div>

    <div class="terms-box" style="font-size: 13px; margin-top: 10px; margin-bottom: 20px;">
      <div class="section-heading" style="font-size: 14px; margin-bottom: 6px;">Terms &amp; Conditions:</div>
      <ol style="margin-left: 20px; line-height: 1.45;">
        @if($quotation->payment_terms)
          @foreach(preg_split('/\r\n|\r|\n/', $quotation->payment_terms) as $line)
            @php $clean = trim(preg_replace('/^\d+[\.\)]\s*/', '', $line)); @endphp
            @if(!empty($clean))
              <li style="margin-bottom: 6px;">{{ $clean }}</li>
            @endif
          @endforeach
        @else
          <li style="margin-bottom: 6px;">All payments shall be made by Online payment or Demand Draft or Cheque, payable at GTMS.</li>
          <li style="margin-bottom: 6px;">All the applicable statutory fees at various stages of the approvals (towards EC) shall be paid by the project proponent and is not included in the scope of this proposal.</li>
          <li style="margin-bottom: 6px;">The scope of our consultancy services includes Environmental Clearance (EC) from SEIAA.</li>
        @endif
      </ol>

      @if($quotation->exclusions)
        <div class="section-heading" style="font-size: 14px; margin-top: 14px; margin-bottom: 6px;">Exclusions:</div>
        <ol style="margin-left: 20px; line-height: 1.45;">
          @foreach(preg_split('/\r\n|\r|\n/', $quotation->exclusions) as $line)
            @php $cleanEx = trim(preg_replace('/^\d+[\.\)]\s*/', '', $line)); @endphp
            @if(!empty($cleanEx))
              <li style="margin-bottom: 6px;">{{ $cleanEx }}</li>
            @endif
          @endforeach
        </ol>
      @endif
    </div>

    <div style="margin-top: 10px;">
      <div class="section-heading" style="font-size: 14px; margin-bottom: 8px;">Official Remittance Account Details</div>
      <table class="account-table" style="width: 80%; font-size: 13px;">
        <tbody>
          <tr>
            <td class="lbl" style="width: 140px; padding: 6px 8px;">Beneficiary Name</td>
            <td style="padding: 6px 8px; font-weight: bold;">{{ $bank->account_name ?? 'Geo Technical Mining Solutions' }}</td>
          </tr>
          <tr>
            <td class="lbl" style="padding: 6px 8px;">Account No.</td>
            <td style="padding: 6px 8px; font-family: monospace; font-weight: bold; font-size: 14px;">{{ $bank->account_number ?? '107302000001265' }}</td>
          </tr>
          <tr>
            <td class="lbl" style="padding: 6px 8px;">Bank / Branch</td>
            <td style="padding: 6px 8px;">{{ $bank->bank_name ?? 'Indian Overseas Bank' }}{{ !empty($bank->branch_name) ? '/ ' . $bank->branch_name : '' }}</td>
          </tr>
          <tr>
            <td class="lbl" style="padding: 6px 8px;">IFSC Code</td>
            <td style="padding: 6px 8px; font-family: monospace; font-weight: bold; color: #1E3A8A;">{{ $bank->ifsc_code ?? 'IOBA0001073' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Authorized Signatory Stamp & Signature Block -->
    <div style="margin-top: 60px; display: flex; justify-content: space-between; align-items: flex-end;">
      <div style="font-size: 12px; color: #64748b;">
        <div>Prepared by: <strong>GTMS Accounts Department</strong></div>
        <div style="margin-top: 3px;">System Generated Document &bull; Verified Copy</div>
      </div>
      <div style="text-align: center; min-width: 240px; max-width: 320px;">
        <div style="font-size: 13px; font-weight: bold; margin-bottom: 45px;">For GEO TECHNICAL MINING SOLUTIONS</div>
        <div style="border-top: 1px dashed #000000; padding-top: 5px; font-size: 13px; font-weight: 700; color: #0F172A;">
          {{ $quotation->authorized_signatory ?: 'Dr. S. Karuppannan, M.Sc., Ph.D.' }}
        </div>
        <div style="font-size: 11px; color: #475569; margin-top: 2px;">Authorized Signatory / Branch</div>
      </div>
    </div>

    <div class="page-footer-num">-- Page 2 of 2 --</div>
  </div>

</body>
</html>
