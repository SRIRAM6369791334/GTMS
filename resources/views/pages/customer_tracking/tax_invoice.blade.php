<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tax Invoice &bull; {{ $tiInvoiceNo }} &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; GTMS</title>
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
      line-height: 1.35;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
      -webkit-font-smoothing: antialiased;
    }

    /* Floating Screen Action Bar */
    .no-print-bar {
      width: 210mm;
      display: flex;
      flex-direction: column;
      gap: 10px;
      background: #0F1E4D;
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      box-shadow: 0 4px 14px rgba(0,0,0,0.3);
      position: sticky;
      top: 10px;
      z-index: 9999;
    }
    .bar-row-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }
    .bar-row-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      padding-top: 8px;
      border-top: 1px solid rgba(255, 255, 255, 0.15);
      font-size: 12px;
    }
    .bar-section-label {
      font-weight: 600;
      color: #94a3b8;
      font-size: 11.5px;
      margin-right: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .format-switcher-group {
      display: inline-flex;
      background: #1e293b;
      padding: 3px;
      border-radius: 6px;
      gap: 4px;
    }
    .doc-switch-btn {
      background: transparent;
      color: #cbd5e1;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s ease;
      text-decoration: none;
    }
    .doc-switch-btn:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.1);
    }
    .doc-switch-btn.active {
      background: #059669;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(5, 150, 105, 0.4);
    }
    .action-links {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .action-btn {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 7px 14px;
      border-radius: 5px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.15s ease;
    }
    .action-btn:hover {
      background: #1d4ed8;
      color: #fff;
    }
    .action-btn.secondary {
      background: #334155;
    }
    .action-btn.secondary:hover {
      background: #1e293b;
    }
    .action-btn.filter-btn {
      background: #d97706;
    }
    .action-btn.filter-btn:hover {
      background: #b45309;
    }

    /* A4 Document Page */
    .ti-page {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      padding: 12mm 15mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
    }
    .ti-invoice-grid {
      width: 100%;
      border-collapse: collapse;
      font-size: 13.5px;
      border: 1px solid #000000;
    }
    .ti-invoice-grid, .ti-invoice-grid th, .ti-invoice-grid td {
      border: 1px solid #000000;
    }
    .ti-invoice-grid td, .ti-invoice-grid th {
      padding: 5px 8px;
      vertical-align: middle;
    }
    .ti-top-logo-cell {
      width: 45%;
      text-align: center;
      padding: 10px;
    }
    .ti-top-logo-cell img {
      width: 125px;
      height: auto;
      display: block;
      margin: 0 auto;
    }
    .ti-top-title-cell {
      width: 55%;
      text-align: center;
      vertical-align: middle;
    }
    .ti-tax-invoice-heading {
      color: #c00000;
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .ti-company-info-cell {
      vertical-align: top;
      line-height: 1.4;
      padding: 8px;
    }
    .ti-company-name {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 2px;
    }
    .ti-company-iso {
      font-weight: bold;
      margin-bottom: 4px;
    }
    .ti-meta-sub-table {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }
    .ti-meta-sub-table td {
      border-top: none;
      border-left: none;
      border-right: none;
      border-bottom: 1px solid #000000;
      padding: 4px 6px;
    }
    .ti-meta-sub-table tr:last-child td {
      border-bottom: none;
    }
    .ti-meta-sub-table td.m-lbl {
      width: 105px;
      font-weight: bold;
    }
    .ti-label-col {
      font-weight: bold;
      width: 25%;
    }
    .ti-value-col {
      width: 75%;
    }
    .ti-table-header th {
      font-weight: bold;
      text-align: center;
      padding: 6px;
      background: #f8fafc;
    }
    .ti-desc-cell {
      width: 80%;
      padding: 12px 10px;
      vertical-align: top;
    }
    .ti-desc-cell ul {
      margin-left: 20px;
      list-style-type: disc;
    }
    .ti-desc-cell li {
      margin-bottom: 5px;
    }
    .ti-amount-cell {
      width: 20%;
      text-align: right;
      padding-right: 14px !important;
      vertical-align: top;
      font-weight: bold;
    }
    .ti-subtotal-row td.subtotal-label {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-subtotal-row td.subtotal-val {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-total-row td.total-label {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-total-row td.total-val {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-bank-inner-table {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }
    .ti-bank-inner-table td {
      border: 1px solid #000000;
      padding: 4px 6px;
    }
    .ti-bank-inner-table td.b-lbl {
      width: 110px;
      font-weight: bold;
    }
    .ti-sign-box-cell {
      text-align: center;
      vertical-align: bottom;
      padding: 10px;
      width: 38%;
    }
    .ti-sign-box-cell img {
      width: 130px;
      height: auto;
      display: block;
      margin: 0 auto 6px auto;
    }
    .ti-sign-person-title {
      font-weight: bold;
      font-size: 13px;
      line-height: 1.3;
    }

    /* Multi-Select Applications Modal Styles */
    .app-modal-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(4px);
      z-index: 100000;
      justify-content: center;
      align-items: center;
      padding: 20px;
      font-family: system-ui, -apple-system, sans-serif;
    }
    .app-modal-backdrop.show {
      display: flex;
    }
    .app-modal-box {
      background: #ffffff;
      border-radius: 12px;
      width: 100%;
      max-width: 760px;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
      overflow: hidden;
      animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
      from { opacity: 0; transform: scale(0.97); }
      to { opacity: 1; transform: scale(1); }
    }
    .app-modal-header {
      padding: 16px 22px;
      background: #0F1E4D;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .app-modal-header h3 {
      font-size: 17px;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .app-modal-close {
      background: transparent;
      border: none;
      color: #94a3b8;
      font-size: 20px;
      cursor: pointer;
      line-height: 1;
      padding: 4px;
    }
    .app-modal-close:hover {
      color: #ffffff;
    }
    .app-modal-toolbar {
      padding: 10px 22px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
    }
    .app-modal-body {
      padding: 16px 22px;
      overflow-y: auto;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .app-choice-card {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 12px 14px;
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      transition: all 0.15s ease;
      cursor: pointer;
      background: #ffffff;
    }
    .app-choice-card:hover {
      border-color: #93c5fd;
      background: #f8fafc;
    }
    .app-choice-card.selected {
      border-color: #2563eb;
      background: #eff6ff;
    }
    .app-choice-card input[type="checkbox"] {
      margin-top: 4px;
      width: 17px;
      height: 17px;
      cursor: pointer;
      accent-color: #2563eb;
    }
    .app-choice-info {
      flex: 1;
    }
    .app-badge {
      display: inline-block;
      padding: 2px 7px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      margin-right: 6px;
    }
    .app-title-text {
      font-weight: 600;
      color: #0f172a;
      font-size: 14px;
    }
    .app-sub-detail {
      font-size: 12.5px;
      color: #475569;
      margin-top: 3px;
    }
    .app-amount-tag {
      font-weight: 700;
      color: #059669;
      font-size: 14px;
      white-space: nowrap;
    }
    .app-modal-footer {
      padding: 14px 22px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .app-summary-pills {
      font-size: 13px;
      color: #334155;
    }

    /* Print Media Rules */
    @media print {
      body {
        background: transparent !important;
        padding: 0 !important;
        gap: 0 !important;
      }
      .no-print-bar, .app-modal-backdrop {
        display: none !important;
      }
      .ti-page {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: auto !important;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Screen Action Bar -->
  <div class="no-print-bar">
    <div class="bar-row-top">
      <div style="display:flex; align-items:center; gap:12px;">
        <span class="bar-section-label">Document:</span>
        <div class="format-switcher-group">
          <button type="button" class="doc-switch-btn active">
            <i class="fa fa-receipt"></i> Tax Invoice
          </button>
          <a href="{{ route('customer-tracking.proforma-invoice', array_merge(['customer' => $customer->slug ?? $customer->id], request()->has('apps') ? ['apps' => request('apps')] : (count($selectedAppKeys) ? ['apps' => implode(',', $selectedAppKeys)] : []))) }}" class="doc-switch-btn">
            <i class="fa fa-file-invoice"></i> Switch to Proforma Invoice
          </a>
        </div>
      </div>

      <div class="action-links">
        <button type="button" class="action-btn seal-status-btn blank-mode" onclick="openSealModal()" id="sealActionBtn" title="Seal & Signature Setup">
          <i class="fa fa-stamp"></i> <span id="sealStatusLabel">Seal: Blank (Manual)</span>
        </button>
        <button type="button" class="action-btn filter-btn" onclick="openAppsModal()">
          <i class="fa fa-sliders"></i> Filter Applications ({{ count($items) }}/{{ count($allCustomerApps) }})
        </button>
        <button type="button" class="action-btn" onclick="window.print()">
          <i class="fa fa-print"></i> Print Invoice
        </button>
        <a href="{{ route('customer-tracking.show', $customer->slug ?? $customer->id) }}" class="action-btn secondary">
          <i class="fa fa-arrow-left"></i> Back to Tracking
        </a>
      </div>
    </div>

    <div class="bar-row-bottom">
      <div>
        <strong>Client:</strong> {{ $customer->company_name ?: $customer->customer_name }}
        @if($customer->mimas_no) &bull; <span style="color:#93c5fd;">{{ $customer->mimas_no }}</span> @endif
      </div>
      <div>
        <span>Mode: <strong>{{ $isConsolidated ? 'Consolidated (' . count($items) . ' Applications)' : 'Single Application' }}</strong></span>
        &bull; <span>Subtotal: <strong>₹{{ number_format($subtotal, 2) }}</strong></span>
        &bull; <span>GST: <strong>₹{{ number_format($cgst + $sgst, 2) }}</strong></span>
        &bull; <span>Grand Total: <strong style="color:#34d399;">₹{{ number_format($grandTotal, 2) }}</strong></span>
      </div>
    </div>
  </div>

  <!-- A4 TAX INVOICE SHEET -->
  <div class="ti-page">
    <table class="ti-invoice-grid">
      <tbody>
        <!-- Top Row: Logo & Heading -->
        <tr>
          <td class="ti-top-logo-cell">
            <img src="{{ asset('images/invoices/gtms_logo.png') }}" alt="GTMS Logo">
          </td>
          <td class="ti-top-title-cell">
            <div class="ti-tax-invoice-heading">TAX INVOICE</div>
          </td>
        </tr>

        <!-- Company Details & Invoice Meta -->
        <tr>
          <td class="ti-company-info-cell">
            <div class="ti-company-name">GEO TECHNICAL MINING SOLUTIONS</div>
            <div class="ti-company-iso">(A NABET Accredited &amp; ISO Certified)</div>
            <div>No: 2/548/D, Thadangam, Thokkampatti (post),</div>
            <div>Dharmapuri-636705. Tamil Nadu.</div>
            <div style="margin-top: 3px;"><strong>GSTIN:</strong> 33AAQFG5164R1ZN &nbsp;|&nbsp; <strong>PAN:</strong> AAQFG5164R</div>
            <div style="margin-top: 2px;"><strong>Mob:</strong> +91 9790462882, 7010076633</div>
            <div style="margin-top: 2px;"><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
          </td>
          <td style="padding: 0; vertical-align: top;">
            <table class="ti-meta-sub-table">
              <tr>
                <td class="m-lbl">Invoice No.:</td>
                <td><strong>{{ $tiInvoiceNo }}</strong></td>
              </tr>
              <tr>
                <td class="m-lbl">Invoice Date:</td>
                <td><strong>{{ $invoiceDate }}</strong></td>
              </tr>
              <tr>
                <td class="m-lbl">GSTIN:</td>
                <td><strong>33AAQFG5164R1ZN</strong></td>
              </tr>
              <tr>
                <td class="m-lbl">PAN:</td>
                <td><strong>AAQFG5164R</strong></td>
              </tr>
              <tr>
                <td class="m-lbl">Web:</td>
                <td>www.gtmsind.com</td>
              </tr>
              <tr>
                <td class="m-lbl">Mob:</td>
                <td>+91 9790462882<br>+91 7010076633</td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Client Info Rows -->
        <tr>
          <td class="ti-label-col">CLIENT NAME</td>
          <td class="ti-value-col" style="color: #c00000; font-style: italic; font-weight: bold;">
            {{ strtoupper($customer->company_name ?: $customer->customer_name) }}
          </td>
        </tr>
        <tr>
          <td class="ti-label-col">CLIENT ADDRESS</td>
          <td class="ti-value-col" style="font-style: italic;">
            {{ $customer->address ?: ($customer->area ?: 'Concession Site, ' . $districtName . ' District, Tamil Nadu') }}
          </td>
        </tr>
        <tr>
          <td class="ti-label-col">GSTIN</td>
          <td class="ti-value-col">
            {{ $customer->gstin ?: ($customer->pan ?: 'Unregistered / Exempt') }}
          </td>
        </tr>
        <tr>
          <td class="ti-label-col">WORK AUTHORIZED BY</td>
          <td class="ti-value-col">
            Dr.S.Karuppannan,<br>
            Managing Partner,<br>
            Geo Technical Mining Solutions.
          </td>
        </tr>
        <tr>
          <td class="ti-label-col">PARTICULARS</td>
          <td class="ti-value-col">
            <strong><em>{{ $particulars }}</em></strong>
          </td>
        </tr>
        <tr>
          <td class="ti-label-col">REFERENCE</td>
          <td class="ti-value-col">
            <em>{{ $mineralName }} quarry lease {{ $locationDetails }}</em>
          </td>
        </tr>

        <!-- Items Table Header -->
        <tr class="ti-table-header">
          <th>WORK DESCRIPTION</th>
          <th>AMOUNT</th>
        </tr>

        <!-- Dynamic Items Rows -->
        @foreach($items as $item)
        <tr>
          <td class="ti-desc-cell">
            <div style="font-weight: bold; font-size: 13.5px; color: #0F1E4D; margin-bottom: 4px;">
              @if($isConsolidated)
                {{ $item['sl'] }}.
              @endif
              {{ $item['service_name'] }}
              @if(!empty($item['sac']))
                <span style="font-weight: normal; font-size: 11px; color: #475569;">(SAC: {{ $item['sac'] }})</span>
              @endif
            </div>
            @if(!empty($item['ref']))
              <div style="font-size: 11.5px; color: #c00000; font-weight: bold; margin-bottom: 5px; font-style: italic;">
                {{ $item['ref'] }}
              </div>
            @endif
            @if(!empty($item['bullets']) && count($item['bullets']) > 0)
              <ul style="margin-left: 20px; list-style-type: disc; margin-bottom: 6px;">
                @foreach($item['bullets'] as $bullet)
                  <li style="margin-bottom: 3px; font-size: 12.5px;">{{ $bullet }}</li>
                @endforeach
              </ul>
            @endif
          </td>
          <td class="ti-amount-cell">
            Rs. {{ number_format($item['amount'], 2) }}
          </td>
        </tr>
        @endforeach

        <!-- Subtotal Row -->
        <tr class="ti-subtotal-row">
          <td class="subtotal-label">TOTAL</td>
          <td class="subtotal-val">Rs. {{ number_format($subtotal, 2) }}</td>
        </tr>

        <!-- CGST Row -->
        <tr class="ti-subtotal-row">
          <td class="subtotal-label">ADD: CGST @ 9%</td>
          <td class="subtotal-val">Rs. {{ number_format($cgst, 2) }}</td>
        </tr>

        <!-- SGST Row -->
        <tr class="ti-subtotal-row">
          <td class="subtotal-label">ADD: SGST @ 9%</td>
          <td class="subtotal-val">Rs. {{ number_format($sgst, 2) }}</td>
        </tr>

        <!-- Grand Total Row -->
        <tr class="ti-total-row">
          <td class="total-label">GRAND TOTAL</td>
          <td class="total-val">Rs. {{ number_format($grandTotal, 2) }}</td>
        </tr>

        <!-- Amount In Words Row -->
        <tr>
          <td colspan="2" style="padding: 8px; font-size: 13.5px;">
            <strong>Amount in Words:</strong> Rupees {{ $amountInWords }}.
          </td>
        </tr>

        <!-- Bank Details & Authorized Signatory Footer Row -->
        <tr>
          <td style="padding: 0; vertical-align: top;">
            <table class="ti-bank-inner-table">
              <tr>
                <td colspan="2" style="font-weight: bold; background-color: #ffffff; text-align: left; padding: 4px 6px;">
                  Bank Details:
                </td>
              </tr>
              <tr>
                <td class="b-lbl">Bank Name:</td>
                <td>Karur Vysya Bank</td>
              </tr>
              <tr>
                <td class="b-lbl">Branch:</td>
                <td>Dharmapuri Branch</td>
              </tr>
              <tr>
                <td class="b-lbl">A/C No.:</td>
                <td style="font-weight: bold; font-size: 14px; letter-spacing: 0.5px;">1124 135 0000 13745</td>
              </tr>
              <tr>
                <td class="b-lbl">IFSC Code:</td>
                <td style="font-weight: bold;">KVBL0001124</td>
              </tr>
            </table>
          </td>
          <td class="ti-sign-box-cell">
            <div style="font-weight: bold; font-size: 11.5px; margin-bottom: 4px;">
              For GEO TECHNICAL MINING SOLUTIONS
            </div>
            <div class="invoice-seal-slot">
              @if(!empty($sealImageUrl))
                <img src="{{ $sealImageUrl }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
                <div class="seal-empty-space" style="display: none;"></div>
              @else
                <img src="" alt="Authorized Signatory Seal" class="invoice-seal-img" style="display: none;">
                <div class="seal-empty-space"></div>
              @endif
            </div>
            <div class="ti-sign-person-title">
              Dr.S.Karuppannan, Ph.D.<br>
              Authorized Signatory
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- MULTI-SELECT APPLICATIONS MODAL (IDEA 2) -->
  <div id="appsModal" class="app-modal-backdrop" onclick="handleBackdropClick(event)">
    <div class="app-modal-box">
      <div class="app-modal-header">
        <h3><i class="fa fa-sliders text-warning"></i> Customize Applications for Tax Invoice</h3>
        <button type="button" class="app-modal-close" onclick="closeAppsModal()">&times;</button>
      </div>

      <div class="app-modal-toolbar">
        <div>
          <button type="button" class="action-btn secondary" style="padding: 4px 10px; font-size: 12px;" onclick="toggleAllApps(true)">
            <i class="fa fa-check-square"></i> Select All
          </button>
          <button type="button" class="action-btn secondary" style="padding: 4px 10px; font-size: 12px; margin-left: 6px;" onclick="toggleAllApps(false)">
            <i class="fa fa-square"></i> Clear All
          </button>
        </div>
        <div style="font-weight: 600; color: #475569;">
          Total Registered: {{ count($allCustomerApps) }} Applications
        </div>
      </div>

      <form id="filterAppsForm" method="GET" action="{{ route('customer-tracking.tax-invoice', $customer->slug ?? $customer->id) }}">
        <div class="app-modal-body">
          @forelse($allCustomerApps as $app)
            @php $isSelected = in_array($app['key'], $selectedAppKeys); @endphp
            <label class="app-choice-card {{ $isSelected ? 'selected' : '' }}" id="card-{{ $app['key'] }}">
              <input type="checkbox" name="apps[]" value="{{ $app['key'] }}" {{ $isSelected ? 'checked' : '' }} onchange="onAppCheckChange(this)" data-amount="{{ $app['amount'] }}">
              <div class="app-choice-info">
                <div>
                  <span class="app-badge" style="background: {{ $app['badge_color'] }}; color: #ffffff;">{{ $app['module_label'] }}</span>
                  <span class="app-title-text">{{ $app['app_no'] }}</span>
                  @if(!empty($app['status']))
                    <span style="font-size: 11px; background: #e2e8f0; color: #334155; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">{{ $app['status'] }}</span>
                  @endif
                </div>
                <div class="app-sub-detail">
                  <strong>Location:</strong> {{ $app['location'] ?: 'Concession site' }}
                  @if(!empty($app['extent'])) &bull; <strong>Extent:</strong> {{ $app['extent'] }} @endif
                </div>
              </div>
              <div class="app-amount-tag">
                ₹ {{ number_format($app['amount'], 2) }}
              </div>
            </label>
          @empty
            <div style="text-align: center; padding: 20px; color: #64748b;">
              No applications recorded for this customer.
            </div>
          @endforelse
        </div>

        <div class="app-modal-footer">
          <div class="app-summary-pills">
            <span id="modalSelectedCount">{{ count($selectedAppKeys) }}</span> selected &bull;
            Estimated Subtotal: <strong id="modalSubtotal" style="color:#059669;">₹{{ number_format($subtotal, 2) }}</strong>
          </div>
          <div style="display: flex; gap: 8px;">
            <button type="button" class="action-btn secondary" onclick="closeAppsModal()">Cancel</button>
            <button type="submit" class="action-btn" style="background:#059669;">
              <i class="fa fa-refresh"></i> Update Tax Invoice
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openAppsModal() {
      document.getElementById('appsModal').classList.add('show');
      recalcModalSummary();
    }

    function closeAppsModal() {
      document.getElementById('appsModal').classList.remove('show');
    }

    function handleBackdropClick(e) {
      if (e.target.id === 'appsModal') {
        closeAppsModal();
      }
    }

    function onAppCheckChange(cb) {
      var card = document.getElementById('card-' + cb.value);
      if (card) {
        if (cb.checked) {
          card.classList.add('selected');
        } else {
          card.classList.remove('selected');
        }
      }
      recalcModalSummary();
    }

    function toggleAllApps(select) {
      var checkboxes = document.querySelectorAll('#filterAppsForm input[type="checkbox"]');
      checkboxes.forEach(function(cb) {
        cb.checked = select;
        var card = document.getElementById('card-' + cb.value);
        if (card) {
          if (select) card.classList.add('selected');
          else card.classList.remove('selected');
        }
      });
      recalcModalSummary();
    }

    function recalcModalSummary() {
      var checkboxes = document.querySelectorAll('#filterAppsForm input[type="checkbox"]:checked');
      var total = 0;
      checkboxes.forEach(function(cb) {
        var amt = parseFloat(cb.getAttribute('data-amount') || 0);
        total += amt;
      });
      var countEl = document.getElementById('modalSelectedCount');
      var subEl = document.getElementById('modalSubtotal');
      if (countEl) countEl.innerText = checkboxes.length;
      if (subEl) subEl.innerText = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
  </script>

  @include('pages.accounts.partials.invoice_seal_modal')
</body>
</html>
