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

    .header-banner {
      width: 100%;
      margin-bottom: 6px;
    }
    .header-banner img {
      width: 100%;
      height: auto;
      display: block;
    }
    .company-contact-strip {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      border-bottom: 1.5px solid #000000;
      padding-bottom: 5px;
      margin-bottom: 12px;
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
      margin-top: 10px;
      margin-bottom: 18px;
    }
    .cost-table th {
      font-weight: bold;
      text-align: center;
      padding: 6px;
    }
    .cost-table td.cell-project {
      width: 35%;
      vertical-align: middle;
      font-weight: 500;
      padding: 8px;
    }
    .cost-table td.cell-item {
      width: 40%;
      padding: 4px 8px;
    }
    .cost-table td.cell-amount {
      width: 25%;
      text-align: right;
      padding: 4px 8px;
      font-weight: 500;
    }
    .cost-table tr.row-total td.label {
      text-align: right;
      font-weight: bold;
      font-style: italic;
      padding-right: 12px;
    }
    .cost-table tr.row-total td.val {
      text-align: right;
      font-weight: bold;
      padding-right: 8px;
    }

    .terms-box {
      margin-top: 14px;
      margin-bottom: 18px;
      font-size: 13.5px;
    }
    .terms-box ol {
      margin-left: 20px;
      margin-top: 6px;
    }
    .terms-box li {
      margin-bottom: 6px;
      line-height: 1.4;
    }

    .account-table {
      width: 75%;
      margin-top: 8px;
    }
    .account-table td.lbl {
      width: 140px;
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
      // Resolve Dynamic Scope Sections
      $scopeSections = is_array($quotation->scope_sections) && count($quotation->scope_sections) > 0
          ? $quotation->scope_sections
          : app(\App\Http\Controllers\Accounts\QuotationController::class)->getDefaultScopeSections();

      // Split scope sections cleanly across Page 1 and Page 2
      // Use 3 sections on Page 1 to ensure room for Consignee & Quarry Particulars table
      $totalSections = count($scopeSections);
      $splitCount = $totalSections > 3 ? 3 : $totalSections;
      $page1Sections = array_slice($scopeSections, 0, $splitCount);
      $page2Sections = array_slice($scopeSections, $splitCount);

      // Resolve Milestones
      $milestones = is_array($quotation->milestones) && count($quotation->milestones) > 0
          ? $quotation->milestones
          : app(\App\Http\Controllers\Accounts\QuotationController::class)->getDefaultMilestones();

      // Subject
      $subject = $quotation->subject ?: 'Quotation for Environmental Clearance falls in "B2" Category Project';
  @endphp

  <!-- PAGE 1 -->
  <div class="page">
    <header class="header-banner">
      <img src="{{ asset('assets/images/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
    </header>

    <div class="company-contact-strip">
      <div>
        <strong>GEO TECHNICAL MINING SOLUTIONS</strong> (A NABET Accredited &amp; ISO Certified)<br>
        No: 2/548/D, Thadangam, Thokkampatti (post), Dharmapuri-636705. Tamil Nadu.
      </div>
      <div style="text-align: right;">
        Mob: +91 9790462882, 7010076633<br>
        E-mail: info.gtmsdpi@gmail.com | Website: www.gtmsind.com
      </div>
    </div>

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

    <div class="section-heading-main">Sub: Complete Scope (All Department)</div>

    @foreach($page1Sections as $section)
      <div class="section-heading">{{ $section['title'] }}</div>
      <ul class="scope-list">
        @foreach((array) ($section['items'] ?? []) as $item)
          <li>{{ $item }}</li>
        @endforeach
      </ul>
    @endforeach

    <div class="page-footer-num">-- 1 of 3 --</div>
  </div>

  <!-- PAGE 2 -->
  <div class="page">
    <header class="header-banner">
      <img src="{{ asset('assets/images/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
    </header>

    <div class="company-contact-strip">
      <div>
        <strong>GEO TECHNICAL MINING SOLUTIONS</strong> (A NABET Accredited &amp; ISO Certified)<br>
        No: 2/548/D, Thadangam, Thokkampatti (post), Dharmapuri-636705. Tamil Nadu.
      </div>
      <div style="text-align: right;">
        Mob: +91 9790462882, 7010076633<br>
        E-mail: info.gtmsdpi@gmail.com | Website: www.gtmsind.com
      </div>
    </div>

    @foreach($page2Sections as $section)
      <div class="section-heading">{{ $section['title'] }}</div>
      <ul class="scope-list">
        @foreach((array) ($section['items'] ?? []) as $item)
          <li>{{ $item }}</li>
        @endforeach
      </ul>
    @endforeach

    <div class="section-heading-main" style="margin-top: 20px;">Payment Details</div>

    <table style="width: 75%; margin-top: 8px;">
      <thead>
        <tr>
          <th style="width: 15%; text-align: center;">S. No</th>
          <th style="width: 60%; text-align: left;">Description</th>
          <th style="width: 25%; text-align: center;">Amount in lakhs</th>
        </tr>
      </thead>
      <tbody>
        @foreach($milestones as $m)
          <tr>
            <td style="text-align: center;">{{ $m['s_no'] ?? $loop->iteration }}</td>
            <td>{{ $m['description'] ?? '' }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $m['amount_lakhs'] ?? '' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="page-footer-num">-- 2 of 3 --</div>
  </div>

  <!-- PAGE 3 -->
  <div class="page">
    <header class="header-banner">
      <img src="{{ asset('assets/images/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
    </header>

    <div class="company-contact-strip">
      <div>
        <strong>GEO TECHNICAL MINING SOLUTIONS</strong> (A NABET Accredited &amp; ISO Certified)<br>
        No: 2/548/D, Thadangam, Thokkampatti (post), Dharmapuri-636705. Tamil Nadu.
      </div>
      <div style="text-align: right;">
        Mob: +91 9790462882, 7010076633<br>
        E-mail: info.gtmsdpi@gmail.com | Website: www.gtmsind.com
      </div>
    </div>

    <table class="cost-table">
      <thead>
        <tr>
          <th>Project Details</th>
          <th>Description</th>
          <th>Amount Rs.</th>
        </tr>
      </thead>
      <tbody>
        @php
            $itemsCount = $quotation->items->count();
        @endphp
        @forelse($quotation->items as $index => $item)
          <tr>
            @if($index === 0)
              <td class="cell-project" rowspan="{{ max(1, $itemsCount) }}">
                {{ $subject }}
              </td>
            @endif
            <td class="cell-item">{{ $item->service_name }}</td>
            <td class="cell-amount">{{ number_format($item->subtotal, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td class="cell-project">{{ $subject }}</td>
            <td class="cell-item">Statutory Mining Scope Services</td>
            <td class="cell-amount">{{ number_format($quotation->subtotal, 2) }}</td>
          </tr>
        @endforelse

        <tr class="row-total">
          <td colspan="2" class="label">TOTAL</td>
          <td class="val">{{ number_format($quotation->subtotal, 2) }}</td>
        </tr>
        <tr class="row-total">
          <td colspan="2" class="label">SGST &amp; CGST @ {{ number_format($quotation->tax_rate, 0) }}%</td>
          <td class="val">{{ number_format($quotation->tax_amount, 2) }}</td>
        </tr>
        <tr class="row-total">
          <td colspan="2" class="label" style="font-style: normal; text-align: left; padding-left: 8px;">
            TOTAL ({{ $quotation->amount_in_words }})
          </td>
          <td class="val">{{ number_format($quotation->total_amount, 2) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="terms-box">
      <div class="section-heading">Terms &amp; Conditions:</div>
      <ol>
        @if($quotation->payment_terms)
          @foreach(preg_split('/\r\n|\r|\n/', $quotation->payment_terms) as $line)
            @php $clean = trim(preg_replace('/^\d+[\.\)]\s*/', '', $line)); @endphp
            @if(!empty($clean))
              <li>{{ $clean }}</li>
            @endif
          @endforeach
        @else
          <li>All payments shall be made by Online payment or Demand Draft or Cheque, payable at GTMS</li>
          <li>All the applicable statutory fees at various stages of the approvals (towards EC shall be paid by the project proponent and is not included in the scope of this proposal.</li>
          <li>The scope of our consultancy services includes Environmental Clearance (EC) from SEIAA</li>
        @endif
      </ol>

      @if($quotation->exclusions)
        <div class="section-heading" style="margin-top: 10px;">Exclusions:</div>
        <ol>
          @foreach(preg_split('/\r\n|\r|\n/', $quotation->exclusions) as $line)
            @php $cleanEx = trim(preg_replace('/^\d+[\.\)]\s*/', '', $line)); @endphp
            @if(!empty($cleanEx))
              <li>{{ $cleanEx }}</li>
            @endif
          @endforeach
        </ol>
      @endif
    </div>

    <div>
      <div class="section-heading">Account Details</div>
      <table class="account-table">
        <tbody>
          <tr>
            <td class="lbl">Name</td>
            <td>Geo Technical Mining Solutions</td>
          </tr>
          <tr>
            <td class="lbl">Account No.</td>
            <td>107302000001265</td>
          </tr>
          <tr>
            <td class="lbl">Bank/Branch</td>
            <td>Indian Overseas Bank/ Oddapatti, Dharmapuri</td>
          </tr>
          <tr>
            <td class="lbl">IFSC Code</td>
            <td>IOBA0001073</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="page-footer-num">-- 3 of 3 --</div>
  </div>

</body>
</html>
