<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proforma Invoice &bull; {{ $piInvoiceNo }} &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; GTMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @page {
      size: A4 portrait;
      margin: 8mm 10mm;
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
      background: #2563eb;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
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

    /* A4 Document Page (Reference PI exact styling) */
    .pi-page {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      padding: 10mm 12mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      page-break-after: always;
      font-family: "Times New Roman", Times, serif;
      color: #000000;
      line-height: 1.35;
    }
    .pi-header-banner {
      width: 100%;
      margin-bottom: 8px;
    }
    .pi-header-banner img {
      width: 100%;
      height: auto;
      display: block;
    }
    .gtms-letterhead {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      padding-bottom: 8px;
      border-bottom: 2px solid #BA1B1F;
      margin-bottom: 12px;
      width: 100%;
    }
    .gtms-letterhead-logo {
      flex: 0 0 100px;
      max-width: 100px;
      text-align: center;
    }
    .gtms-letterhead-logo img {
      width: 100%;
      height: auto;
      max-height: 80px;
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
      font-size: 21px;
      font-weight: bold;
      color: #BA1B1F;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
      line-height: 1.15;
    }
    .gtms-letterhead-accreditation {
      font-size: 13px;
      font-weight: bold;
      color: #000000;
      margin-bottom: 2px;
      line-height: 1.25;
    }
    .gtms-letterhead-address {
      font-size: 12px;
      color: #000000;
      line-height: 1.35;
      margin-bottom: 2px;
    }
    .gtms-letterhead-contact {
      font-size: 11.5px;
      color: #000000;
      line-height: 1.35;
    }
    .pi-doc-title {
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      text-decoration: underline;
      letter-spacing: 1.5px;
      margin-bottom: 8px;
    }
    .pi-top-meta-grid {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 13.5px;
    }
    .pi-consignee-box {
      width: 50%;
    }
    .pi-consignee-title {
      font-weight: bold;
      margin-bottom: 3px;
    }
    .pi-text-red {
      color: #dc2626;
      font-weight: bold;
    }
    .pi-invoice-meta-table {
      width: 45%;
      border-collapse: collapse;
      border: none;
    }
    .pi-invoice-meta-table td {
      border: none;
      padding: 1px 4px;
      font-size: 13px;
    }
    .pi-invoice-meta-table td.lbl {
      width: 100px;
      font-weight: bold;
    }
    table.pi-data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      margin-bottom: 8px;
    }
    table.pi-data-table, table.pi-data-table th, table.pi-data-table td {
      border: 1px solid #000000;
    }
    table.pi-data-table th {
      padding: 5px;
      font-weight: bold;
      text-align: center;
      background: #f8fafc;
    }
    table.pi-data-table td {
      padding: 4px 6px;
      vertical-align: top;
    }
    .pi-sub-ref-box {
      font-size: 13px;
      margin-bottom: 8px;
      line-height: 1.35;
    }
    .pi-sub-ref-box .line {
      margin-bottom: 4px;
    }
    ul.pi-item-list {
      margin-left: 18px;
      list-style-type: disc;
    }
    ul.pi-item-list li {
      margin-bottom: 2px;
      font-size: 12.5px;
    }
    .pi-col-sl {
      text-align: center;
      width: 45px;
      font-weight: bold;
    }
    .pi-col-desc {
      width: 75%;
    }
    .pi-col-amount {
      text-align: center;
      width: 20%;
      vertical-align: middle;
      font-weight: bold;
    }
    .pi-total-row td.lbl {
      text-align: right;
      font-weight: bold;
      padding-right: 12px;
    }
    .pi-total-row td.val {
      text-align: center;
      font-weight: bold;
    }
    .pi-signature-area {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin: 10px 0;
      font-size: 13px;
    }
    .pi-ref-staff {
      font-weight: bold;
    }
    .pi-sign-box {
      text-align: center;
      margin-right: 20px;
    }
    .pi-sign-box .sign-title {
      font-weight: bold;
      margin-bottom: 4px;
    }
    .pi-sign-box img {
      width: 140px;
      height: auto;
      display: block;
      margin: 0 auto;
    }
    .pi-accounts-section {
      font-size: 13px;
      margin-bottom: 8px;
    }
    .pi-accounts-title {
      font-weight: bold;
      margin-bottom: 4px;
    }
    .pi-account-block {
      margin-bottom: 6px;
    }
    .pi-account-block .acc-header {
      font-weight: bold;
      margin-bottom: 2px;
    }
    table.pi-bank-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }
    table.pi-bank-table, table.pi-bank-table td {
      border: 1px solid #000000;
      padding: 2px 6px;
    }
    table.pi-bank-table td.b-lbl {
      width: 110px;
      font-weight: bold;
    }
    .pi-footer-banner {
      width: 100%;
      margin-top: auto;
    }
    .pi-footer-banner img {
      width: 100%;
      height: auto;
      display: block;
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
      .pi-page {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 8mm 10mm !important;
        width: 100% !important;
        page-break-after: always;
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
          <a href="{{ route('customer-tracking.tax-invoice', array_merge(['customer' => $customer->slug ?? $customer->id], request()->has('apps') ? ['apps' => request('apps')] : (count($selectedAppKeys) ? ['apps' => implode(',', $selectedAppKeys)] : []))) }}" class="doc-switch-btn">
            <i class="fa fa-receipt"></i> Switch to Tax Invoice
          </a>
          <button type="button" class="doc-switch-btn active">
            <i class="fa fa-file-invoice"></i> Proforma Invoice
          </button>
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

  @if(count($items) <= 4)
    <!-- SINGLE PAGE PROFORMA INVOICE (Auto-Fit for 1 to 4 items) -->
    <div class="pi-page">
      <div>
        <header class="gtms-letterhead">
          <div class="gtms-letterhead-logo">
            <img src="{{ asset('assets/images/gtms_logo.png') }}" alt="GTMS Logo">
          </div>
          <div class="gtms-letterhead-content">
            <div class="gtms-letterhead-title">GEO TECHNICAL MINING SOLUTIONS</div>
            <div class="gtms-letterhead-accreditation">(A NABET Accredited &amp; ISO Certified)</div>
            <div class="gtms-letterhead-address">
              No: 2/548/D, Thadangam, Thokkampatti (post),<br>
              Dharmapuri-636705. Tamil Nadu.
            </div>
            <div class="gtms-letterhead-contact">
              <div><strong>Mob:</strong> +91 9790462882, 7010076633</div>
              <div><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
            </div>
          </div>
        </header>

        <h1 class="pi-doc-title">Proforma Invoice</h1>

        <div class="pi-top-meta-grid">
          <div class="pi-consignee-box">
            <div class="pi-consignee-title">Consignee Name:</div>
            <div class="pi-text-red">{{ $customer->company_name ?: $customer->customer_name }},</div>
            <div class="pi-text-red">{{ $customer->address ?: ($customer->area ?: 'Concession Site') }},</div>
            <div class="pi-text-red">{{ $districtName }}, Tamil Nadu.</div>
          </div>
          <table class="pi-invoice-meta-table">
            <tr>
              <td class="lbl">Invoice No</td>
              <td>: {{ $piInvoiceNo }}</td>
            </tr>
            <tr>
              <td class="lbl">Date</td>
              <td>: {{ $invoiceDate }}</td>
            </tr>
            <tr>
              <td class="lbl">GSTIN</td>
              <td>: 33AAQFG5164R1ZN</td>
            </tr>
            <tr>
              <td class="lbl">PAN</td>
              <td>: AAQFG5164R</td>
            </tr>
          </table>
        </div>

        <table class="pi-data-table" style="width: 100%; margin-bottom: 8px;">
          <tbody>
            <tr>
              <td style="width: 50%; font-weight: bold;">Work Order via</td>
              <td style="width: 50%; font-weight: bold;">{{ $workOrderVia }}</td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Contact Person &amp; Designation</td>
              <td>{{ $contactPerson }}</td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Contact No</td>
              <td class="pi-text-red">{{ $contactNo }}</td>
            </tr>
          </tbody>
        </table>

        <div class="pi-sub-ref-box">
          <div class="line">
            <strong>Sub:</strong> <strong>{{ $particulars }} – Reg.</strong>
          </div>
          <div class="line">
            <strong>Ref:</strong> <span style="color: #dc2626;">{{ $mineralName }}</span> quarry lease {{ $locationDetails }}
          </div>
        </div>

        <table class="pi-data-table">
          <thead>
            <tr>
              <th style="width: 8%;">Sl.<br>No.</th>
              <th style="width: 72%;">Description</th>
              <th style="width: 20%;">Amount<br>Rs.</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
            <tr>
              <td class="pi-col-sl">{{ $item['sl'] }}</td>
              <td class="pi-col-desc">
                <div style="font-weight: bold; margin-bottom: 2px; color: #0F1E4D;">
                  {{ $item['service_name'] }}
                  @if(!empty($item['sac'])) <span style="font-size: 10.5px; font-weight: normal; color: #475569;">[SAC: {{ $item['sac'] }}]</span> @endif
                </div>
                @if(!empty($item['ref']))
                  <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-bottom: 3px; font-style: italic;">
                    {{ $item['ref'] }}
                  </div>
                @endif
                @if(!empty($item['bullets']) && count($item['bullets']) > 0)
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </td>
              <td class="pi-col-amount">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="pi-total-row">
              <td colspan="2" class="lbl">Total</td>
              <td class="val">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr class="pi-total-row">
              <td colspan="2" class="lbl" style="font-style: italic;">S G S T &amp; C G S T @ 18%</td>
              <td class="val">{{ number_format($sgst + $cgst, 2) }}</td>
            </tr>
            <tr class="pi-total-row">
              <td colspan="2" class="lbl" style="text-align: left; padding-left: 8px;">
                Total ( {{ $amountInWords }} )
              </td>
              <td class="val">{{ number_format($grandTotal, 2) }}</td>
            </tr>
          </tbody>
        </table>

        <div class="pi-signature-area">
          <div class="pi-ref-staff">Reference Staff of GTMS –</div>
          <div class="pi-sign-box">
            <div class="sign-title">Authorized Signatory</div>
            <div class="invoice-seal-slot">
              @if(!empty($sealImageUrl))
                <img src="{{ $sealImageUrl }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
                <div class="seal-empty-space" style="display: none;"></div>
              @else
                <img src="" alt="Authorized Signatory Seal" class="invoice-seal-img" style="display: none;">
                <div class="seal-empty-space"></div>
              @endif
            </div>
          </div>
        </div>

        <div class="pi-accounts-section">
          <div class="pi-accounts-title">Account Details:</div>
          <div style="display: flex; gap: 12px;">
            <div class="pi-account-block" style="flex: 1;">
              <div class="acc-header">Account 1:</div>
              <table class="pi-bank-table">
                <tbody>
                  <tr><td class="b-lbl">Name</td><td>Geo Technical Mining Solutions</td></tr>
                  <tr><td class="b-lbl">Account No.</td><td>107302000001265</td></tr>
                  <tr><td class="b-lbl">Bank/Branch</td><td>Indian Overseas Bank/ Oddapatti, Dharmapuri</td></tr>
                  <tr><td class="b-lbl">IFSC Code</td><td>IOBA0001073</td></tr>
                </tbody>
              </table>
            </div>
            <div class="pi-account-block" style="flex: 1;">
              <div class="acc-header">Account 2:</div>
              <table class="pi-bank-table">
                <tbody>
                  <tr><td class="b-lbl">Name</td><td>Geo Technical Mining Solutions</td></tr>
                  <tr><td class="b-lbl">Account No.</td><td>272511100000876</td></tr>
                  <tr><td class="b-lbl">Bank/Branch</td><td>Union Bank of India/ Dharmapuri</td></tr>
                  <tr><td class="b-lbl">IFSC Code</td><td>UBIN0827258</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <footer class="pi-footer-banner">
        <img src="{{ asset('images/invoices/gtms_footer.png') }}" alt="GTMS Services Footer">
      </footer>
    </div>
  @else
    <!-- MULTI-PAGE PROFORMA INVOICE (Consolidated 5+ items) -->
    <!-- PAGE 1 OF 2 -->
    <div class="pi-page">
      <div>
        <header class="gtms-letterhead">
          <div class="gtms-letterhead-logo">
            <img src="{{ asset('assets/images/gtms_logo.png') }}" alt="GTMS Logo">
          </div>
          <div class="gtms-letterhead-content">
            <div class="gtms-letterhead-title">GEO TECHNICAL MINING SOLUTIONS</div>
            <div class="gtms-letterhead-accreditation">(A NABET Accredited &amp; ISO Certified)</div>
            <div class="gtms-letterhead-address">
              No: 2/548/D, Thadangam, Thokkampatti (post),<br>
              Dharmapuri-636705. Tamil Nadu.
            </div>
            <div class="gtms-letterhead-contact">
              <div><strong>Mob:</strong> +91 9790462882, 7010076633</div>
              <div><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
            </div>
          </div>
        </header>

        <h1 class="pi-doc-title">Proforma Invoice</h1>

        <div class="pi-top-meta-grid">
          <div class="pi-consignee-box">
            <div class="pi-consignee-title">Consignee Name:</div>
            <div class="pi-text-red">{{ $customer->company_name ?: $customer->customer_name }},</div>
            <div class="pi-text-red">{{ $customer->address ?: ($customer->area ?: 'Concession Site') }},</div>
            <div class="pi-text-red">{{ $districtName }}, Tamil Nadu.</div>
          </div>
          <table class="pi-invoice-meta-table">
            <tr>
              <td class="lbl">Invoice No</td>
              <td>: {{ $piInvoiceNo }}</td>
            </tr>
            <tr>
              <td class="lbl">Date</td>
              <td>: {{ $invoiceDate }}</td>
            </tr>
            <tr>
              <td class="lbl">GSTIN</td>
              <td>: 33AAQFG5164R1ZN</td>
            </tr>
            <tr>
              <td class="lbl">PAN</td>
              <td>: AAQFG5164R</td>
            </tr>
          </table>
        </div>

        <table class="pi-data-table" style="width: 100%; margin-bottom: 8px;">
          <tbody>
            <tr>
              <td style="width: 50%; font-weight: bold;">Work Order via</td>
              <td style="width: 50%; font-weight: bold;">{{ $workOrderVia }}</td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Contact Person &amp; Designation</td>
              <td>{{ $contactPerson }}</td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Contact No</td>
              <td class="pi-text-red">{{ $contactNo }}</td>
            </tr>
          </tbody>
        </table>

        <div class="pi-sub-ref-box">
          <div class="line">
            <strong>Sub:</strong> <strong>{{ $particulars }} – Reg.</strong>
          </div>
          <div class="line">
            <strong>Ref:</strong> <span style="color: #dc2626;">{{ $mineralName }}</span> quarry lease {{ $locationDetails }}
          </div>
        </div>

        <table class="pi-data-table">
          <thead>
            <tr>
              <th style="width: 8%;">Sl.<br>No.</th>
              <th style="width: 72%;">Description</th>
              <th style="width: 20%;">Amount<br>Rs.</th>
            </tr>
          </thead>
          <tbody>
            @foreach(array_slice($items, 0, 4) as $item)
            <tr>
              <td class="pi-col-sl">{{ $item['sl'] }}</td>
              <td class="pi-col-desc">
                <div style="font-weight: bold; margin-bottom: 2px; color: #0F1E4D;">
                  {{ $item['service_name'] }}
                  @if(!empty($item['sac'])) <span style="font-size: 10.5px; font-weight: normal; color: #475569;">[SAC: {{ $item['sac'] }}]</span> @endif
                </div>
                @if(!empty($item['ref']))
                  <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-bottom: 3px; font-style: italic;">
                    {{ $item['ref'] }}
                  </div>
                @endif
                @if(!empty($item['bullets']) && count($item['bullets']) > 0)
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </td>
              <td class="pi-col-amount">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <footer class="pi-footer-banner">
        <img src="{{ asset('images/invoices/gtms_footer.png') }}" alt="GTMS Services Footer">
      </footer>
    </div>

    <!-- PAGE 2 OF 2 -->
    <div class="pi-page">
      <div>
        <header class="gtms-letterhead">
          <div class="gtms-letterhead-logo">
            <img src="{{ asset('assets/images/gtms_logo.png') }}" alt="GTMS Logo">
          </div>
          <div class="gtms-letterhead-content">
            <div class="gtms-letterhead-title">GEO TECHNICAL MINING SOLUTIONS</div>
            <div class="gtms-letterhead-accreditation">(A NABET Accredited &amp; ISO Certified)</div>
            <div class="gtms-letterhead-address">
              No: 2/548/D, Thadangam, Thokkampatti (post),<br>
              Dharmapuri-636705. Tamil Nadu.
            </div>
            <div class="gtms-letterhead-contact">
              <div><strong>Mob:</strong> +91 9790462882, 7010076633</div>
              <div><strong>E-mail:</strong> info.gtmsdpi@gmail.com, &nbsp;<strong>Website:</strong> www.gtmsind.com</div>
            </div>
          </div>
        </header>

        <table class="pi-data-table">
          <thead>
            <tr>
              <th style="width: 8%;">Sl.<br>No.</th>
              <th style="width: 72%;">Description</th>
              <th style="width: 20%;">Amount<br>Rs.</th>
            </tr>
          </thead>
          <tbody>
            @foreach(array_slice($items, 4) as $item)
            <tr>
              <td class="pi-col-sl">{{ $item['sl'] }}</td>
              <td class="pi-col-desc">
                <div style="font-weight: bold; margin-bottom: 2px; color: #0F1E4D;">
                  {{ $item['service_name'] }}
                  @if(!empty($item['sac'])) <span style="font-size: 10.5px; font-weight: normal; color: #475569;">[SAC: {{ $item['sac'] }}]</span> @endif
                </div>
                @if(!empty($item['ref']))
                  <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-bottom: 3px; font-style: italic;">
                    {{ $item['ref'] }}
                  </div>
                @endif
                @if(!empty($item['bullets']) && count($item['bullets']) > 0)
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </td>
              <td class="pi-col-amount">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="pi-total-row">
              <td colspan="2" class="lbl">Total</td>
              <td class="val">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr class="pi-total-row">
              <td colspan="2" class="lbl" style="font-style: italic;">S G S T &amp; C G S T @ 18%</td>
              <td class="val">{{ number_format($sgst + $cgst, 2) }}</td>
            </tr>
            <tr class="pi-total-row">
              <td colspan="2" class="lbl" style="text-align: left; padding-left: 8px;">
                Total ( {{ $amountInWords }} )
              </td>
              <td class="val">{{ number_format($grandTotal, 2) }}</td>
            </tr>
          </tbody>
        </table>

        <div class="pi-signature-area">
          <div class="pi-ref-staff">Reference Staff of GTMS –</div>
          <div class="pi-sign-box">
            <div class="sign-title">Authorized Signatory</div>
            <div class="invoice-seal-slot">
              @if(!empty($sealImageUrl))
                <img src="{{ $sealImageUrl }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
                <div class="seal-empty-space" style="display: none;"></div>
              @else
                <img src="" alt="Authorized Signatory Seal" class="invoice-seal-img" style="display: none;">
                <div class="seal-empty-space"></div>
              @endif
            </div>
          </div>
        </div>

        <div class="pi-accounts-section">
          <div class="pi-accounts-title">Account Details:</div>
          <div style="display: flex; gap: 12px;">
            <div class="pi-account-block" style="flex: 1;">
              <div class="acc-header">Account 1:</div>
              <table class="pi-bank-table">
                <tbody>
                  <tr><td class="b-lbl">Name</td><td>Geo Technical Mining Solutions</td></tr>
                  <tr><td class="b-lbl">Account No.</td><td>107302000001265</td></tr>
                  <tr><td class="b-lbl">Bank/Branch</td><td>Indian Overseas Bank/ Oddapatti, Dharmapuri</td></tr>
                  <tr><td class="b-lbl">IFSC Code</td><td>IOBA0001073</td></tr>
                </tbody>
              </table>
            </div>
            <div class="pi-account-block" style="flex: 1;">
              <div class="acc-header">Account 2:</div>
              <table class="pi-bank-table">
                <tbody>
                  <tr><td class="b-lbl">Name</td><td>Geo Technical Mining Solutions</td></tr>
                  <tr><td class="b-lbl">Account No.</td><td>272511100000876</td></tr>
                  <tr><td class="b-lbl">Bank/Branch</td><td>Union Bank of India/ Dharmapuri</td></tr>
                  <tr><td class="b-lbl">IFSC Code</td><td>UBIN0827258</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <footer class="pi-footer-banner">
        <img src="{{ asset('images/invoices/gtms_footer.png') }}" alt="GTMS Services Footer">
      </footer>
    </div>
  @endif

  <!-- MULTI-SELECT APPLICATIONS MODAL (IDEA 2) -->
  <div id="appsModal" class="app-modal-backdrop" onclick="handleBackdropClick(event)">
    <div class="app-modal-box">
      <div class="app-modal-header">
        <h3><i class="fa fa-sliders text-warning"></i> Customize Applications for Proforma Invoice</h3>
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

      <form id="filterAppsForm" method="GET" action="{{ route('customer-tracking.proforma-invoice', $customer->slug ?? $customer->id) }}">
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
            <button type="submit" class="action-btn" style="background:#2563eb;">
              <i class="fa fa-refresh"></i> Update Proforma Invoice
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
