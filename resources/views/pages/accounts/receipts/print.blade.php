<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $activeType === 'proforma' ? 'Proforma Invoice' : 'Tax Invoice' }} &bull; {{ $activeType === 'proforma' ? $invoiceData['piInvoiceNo'] : $invoiceData['tiInvoiceNo'] }} &bull; {{ $receipt->customer->company_name ?: $receipt->customer->customer_name }} &bull; GTMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Global Reset & Base Setup */
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
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
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
    .no-print-bar .brand-info {
      font-size: 13px;
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

    /* Container Visibility Control */
    .doc-format-container {
      display: none;
      width: 210mm;
    }
    .doc-format-container.active-doc {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }


    /* ======================================================== */
    /* 2. PROFORMA INVOICE (reference PI.html EXACT STYLES)     */
    /* ======================================================== */
    .pi-page {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      padding: 10mm 12mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
      margin: 12px 0;
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
      margin-bottom: 12px;
    }
    .pi-accounts-title {
      font-weight: bold;
      margin-bottom: 4px;
    }
    .pi-account-block {
      margin-bottom: 8px;
    }
    .pi-account-block .acc-header {
      font-weight: bold;
      margin-bottom: 2px;
    }
    table.pi-bank-table {
      width: 65%;
      border-collapse: collapse;
      font-size: 12.5px;
    }
    table.pi-bank-table, table.pi-bank-table td {
      border: 1px solid #000000;
      padding: 2px 6px;
    }
    table.pi-bank-table td.b-lbl {
      width: 130px;
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

    /* ======================================================== */
    /* 3. TAX INVOICE (Reference TI.html EXACT STYLES)          */
    /* ======================================================== */
    .ti-page {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      padding: 12mm 15mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      font-family: "Times New Roman", Times, serif;
      color: #000000;
      line-height: 1.35;
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
      text-align: center;
      vertical-align: middle;
      font-weight: bold;
    }
    .ti-subtotal-row td.subtotal-label {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-subtotal-row td.subtotal-val {
      text-align: center;
      font-weight: bold;
    }
    .ti-total-row td.total-label {
      text-align: right;
      padding-right: 14px;
      font-weight: bold;
    }
    .ti-total-row td.total-val {
      text-align: center;
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

    /* Print Styles */
    @media print {
      @page {
        size: A4 portrait;
        margin: 8mm 10mm;
      }
      body {
        background: transparent !important;
        padding: 0 !important;
        gap: 0 !important;
      }
      .no-print-bar {
        display: none !important;
      }
      .doc-format-container:not(.active-doc) {
        display: none !important;
      }
      .doc-format-container.active-doc {
        display: block !important;
        width: 100% !important;
      }
      .pi-page, .ti-page {
        box-shadow: none !important;
        margin: 0 !important;
        width: 100% !important;
        padding: 8mm 10mm !important;
      }
      .pi-page {
        page-break-after: always !important;
      }
      .pi-page:last-child {
        page-break-after: auto !important;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Screen Action Bar with Document Switcher & Scope Controls -->
  <div class="no-print-bar">
    <div class="bar-row-top">
      <div class="brand-info">
        <strong style="color: #60a5fa;"><i class="fas fa-file-invoice"></i> GTMS Document Portal</strong> &bull;
        Ref: <strong>{{ $receipt->receipt_number }}</strong> &bull;
        <span>{{ $receipt->customer->company_name ?: $receipt->customer->customer_name }}</span>
      </div>

      <!-- ACTIONS -->
      <div class="action-links">
        <button type="button" class="action-btn seal-status-btn blank-mode" onclick="openSealModal()" id="sealActionBtn" title="Seal & Signature Setup">
          <i class="fas fa-stamp"></i> <span id="sealStatusLabel">Seal: Blank (Manual)</span>
        </button>
        <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="action-btn secondary">
          <i class="fas fa-arrow-left"></i> Details
        </a>
        <a href="{{ route('accounts.receipts.index') }}" class="action-btn secondary">
          <i class="fas fa-list"></i> Receipts
        </a>
        <button type="button" class="action-btn" onclick="window.print()">
          <i class="fas fa-print"></i> Print Document
        </button>
      </div>
    </div>

    <div class="bar-row-bottom">
      <!-- FORMAT SWITCHER BUTTONS -->
      <div style="display: flex; align-items: center; gap: 4px;">
        <span class="bar-section-label">Invoice:</span>
        <div class="format-switcher-group">
          <button type="button" id="btn-tax" class="doc-switch-btn format-btn {{ $activeType === 'tax' ? 'active' : '' }}" onclick="switchDocFormat('tax')">
            <i class="fas fa-receipt"></i> Tax Invoice (TI)
          </button>
          <button type="button" id="btn-proforma" class="doc-switch-btn format-btn {{ $activeType === 'proforma' ? 'active' : '' }}" onclick="switchDocFormat('proforma')">
            <i class="fas fa-file-alt"></i> Proforma Invoice (PI)
          </button>
        </div>
      </div>

      <!-- SCOPE SWITCHER (Single Service vs Consolidated) -->
      <div style="display: flex; align-items: center; gap: 4px;">
        <span class="bar-section-label">Invoice Scope:</span>
        <div class="format-switcher-group">
          <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => $activeType, 'scope' => 'single']) }}"
             class="doc-switch-btn scope-link {{ $activeScope === 'single' ? 'active' : '' }}"
             title="Generate invoice for this specific statutory service only">
            <i class="fas fa-file"></i> This Service Only
          </a>
          <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => $activeType, 'scope' => 'consolidated']) }}"
             class="doc-switch-btn scope-link {{ $activeScope === 'consolidated' ? 'active' : '' }}"
             title="Generate consolidated invoice itemizing all customer statutory applications">
            <i class="fas fa-layer-group"></i> Consolidated (All Applications @if($invoiceData['customerAppsCount'] > 0)· {{ $invoiceData['customerAppsCount'] }}@endif)
          </a>
        </div>
      </div>
    </div>
  </div>





  <!-- =================================================================== -->
  <!-- 2. PROFORMA INVOICE CONTAINER (reference PI.html EXACT TEMPLATE)     -->
  <!-- =================================================================== -->
  <div id="doc-proforma" class="doc-format-container {{ $activeType === 'proforma' ? 'active-doc' : '' }}">
    @if(count($invoiceData['items']) <= 4)
      <!-- SINGLE PAGE PROFORMA INVOICE (Auto-Fit for 1 to 4 items) -->
      <div class="pi-page">
        <div>
          <header class="pi-header-banner">
            <img src="{{ asset('images/invoices/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
          </header>

          <h1 class="pi-doc-title">Proforma Invoice</h1>

          <div class="pi-top-meta-grid">
            <div class="pi-consignee-box">
              <div class="pi-consignee-title">Consignee Name:</div>
              <div class="pi-text-red">{{ $receipt->customer->company_name ?: $receipt->customer->customer_name }},</div>
              <div class="pi-text-red">{{ $receipt->customer->address ?: ($receipt->customer->area ?: 'Concession Site') }},</div>
              <div class="pi-text-red">{{ $invoiceData['districtName'] }}, Tamil Nadu.</div>
            </div>
            <table class="pi-invoice-meta-table">
              <tr>
                <td class="lbl">Invoice No</td>
                <td>: {{ $invoiceData['piInvoiceNo'] }}</td>
              </tr>
              <tr>
                <td class="lbl">Date</td>
                <td>: {{ $invoiceData['invoiceDate'] }}</td>
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
                <td style="width: 50%; font-weight: bold;">{{ $invoiceData['workOrderVia'] }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Contact Person &amp; Designation</td>
                <td>{{ $invoiceData['contactPerson'] }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Contact No</td>
                <td class="pi-text-red">{{ $invoiceData['contactNo'] }}</td>
              </tr>
            </tbody>
          </table>

          <div class="pi-sub-ref-box">
            <div class="line">
              <strong>Sub:</strong> <strong>{{ $invoiceData['particulars'] }} – Reg.</strong>
            </div>
            <div class="line">
              <strong>Ref:</strong> <span style="color: #dc2626;">{{ $invoiceData['mineralName'] }}</span> quarry lease {{ $invoiceData['locationDetails'] }}
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
              @foreach($invoiceData['items'] as $item)
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
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                </td>
                <td class="pi-col-amount">{{ number_format($item['amount'], 2) }}</td>
              </tr>
              @endforeach
              <tr class="pi-total-row">
                <td colspan="2" class="lbl">Total</td>
                <td class="val">{{ number_format($invoiceData['subtotal'], 2) }}</td>
              </tr>
              <tr class="pi-total-row">
                <td colspan="2" class="lbl" style="font-style: italic;">S G S T &amp; C G S T @ 18%</td>
                <td class="val">{{ number_format($invoiceData['sgst'] + $invoiceData['cgst'], 2) }}</td>
              </tr>
              <tr class="pi-total-row">
                <td colspan="2" class="lbl" style="text-align: left; padding-left: 8px;">
                  Total ( {{ $invoiceData['amountInWords'] }} )
                </td>
                <td class="val">{{ number_format($invoiceData['grandTotal'], 2) }}</td>
              </tr>
            </tbody>
          </table>

          <div class="pi-signature-area">
            <div class="pi-ref-staff">Reference Staff of GTMS –</div>
            <div class="pi-sign-box">
              <div class="sign-title">Authorized Signatory</div>
              <div class="invoice-seal-slot">
                @if(!empty($invoiceData['sealImageUrl']))
                  <img src="{{ $invoiceData['sealImageUrl'] }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
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
                <table class="pi-bank-table" style="width: 100%;">
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
                <table class="pi-bank-table" style="width: 100%;">
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
          <header class="pi-header-banner">
            <img src="{{ asset('images/invoices/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
          </header>

          <h1 class="pi-doc-title">Proforma Invoice</h1>

          <div class="pi-top-meta-grid">
            <div class="pi-consignee-box">
              <div class="pi-consignee-title">Consignee Name:</div>
              <div class="pi-text-red">{{ $receipt->customer->company_name ?: $receipt->customer->customer_name }},</div>
              <div class="pi-text-red">{{ $receipt->customer->address ?: ($receipt->customer->area ?: 'Concession Site') }},</div>
              <div class="pi-text-red">{{ $invoiceData['districtName'] }}, Tamil Nadu.</div>
            </div>
            <table class="pi-invoice-meta-table">
              <tr>
                <td class="lbl">Invoice No</td>
                <td>: {{ $invoiceData['piInvoiceNo'] }}</td>
              </tr>
              <tr>
                <td class="lbl">Date</td>
                <td>: {{ $invoiceData['invoiceDate'] }}</td>
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
                <td style="width: 50%; font-weight: bold;">{{ $invoiceData['workOrderVia'] }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Contact Person &amp; Designation</td>
                <td>{{ $invoiceData['contactPerson'] }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Contact No</td>
                <td class="pi-text-red">{{ $invoiceData['contactNo'] }}</td>
              </tr>
            </tbody>
          </table>

          <div class="pi-sub-ref-box">
            <div class="line">
              <strong>Sub:</strong> <strong>{{ $invoiceData['particulars'] }} – Reg.</strong>
            </div>
            <div class="line">
              <strong>Ref:</strong> <span style="color: #dc2626;">{{ $invoiceData['mineralName'] }}</span> quarry lease {{ $invoiceData['locationDetails'] }}
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
              @foreach(array_slice($invoiceData['items'], 0, 4) as $item)
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
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
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
          <header class="pi-header-banner">
            <img src="{{ asset('images/invoices/gtms_header.png') }}" alt="Geo Technical Mining Solutions Letterhead">
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
              @foreach(array_slice($invoiceData['items'], 4) as $item)
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
                  <ul class="pi-item-list">
                    @foreach($item['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                </td>
                <td class="pi-col-amount">{{ number_format($item['amount'], 2) }}</td>
              </tr>
              @endforeach
              <!-- Totals -->
              <tr class="pi-total-row">
                <td colspan="2" class="lbl">Total</td>
                <td class="val">{{ number_format($invoiceData['subtotal'], 2) }}</td>
              </tr>
              <tr class="pi-total-row">
                <td colspan="2" class="lbl" style="font-style: italic;">S G S T &amp; C G S T @ 18%</td>
                <td class="val">{{ number_format($invoiceData['sgst'] + $invoiceData['cgst'], 2) }}</td>
              </tr>
              <tr class="pi-total-row">
                <td colspan="2" class="lbl" style="text-align: left; padding-left: 8px;">
                  Total ( {{ $invoiceData['amountInWords'] }} )
                </td>
                <td class="val">{{ number_format($invoiceData['grandTotal'], 2) }}</td>
              </tr>
            </tbody>
          </table>

          <div class="pi-signature-area">
            <div class="pi-ref-staff">Reference Staff of GTMS –</div>
            <div class="pi-sign-box">
              <div class="sign-title">Authorized Signatory</div>
              <div class="invoice-seal-slot">
                @if(!empty($invoiceData['sealImageUrl']))
                  <img src="{{ $invoiceData['sealImageUrl'] }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
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
                <table class="pi-bank-table" style="width: 100%;">
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
                <table class="pi-bank-table" style="width: 100%;">
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
  </div>


  <!-- =================================================================== -->
  <!-- 3. TAX INVOICE CONTAINER (Reference TI.html EXACT TEMPLATE)         -->
  <!-- =================================================================== -->
  <div id="doc-tax" class="doc-format-container {{ $activeType === 'tax' ? 'active-doc' : '' }}">
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
              <div class="ti-company-iso">(ISO 9001: 2015 Certified Company)</div>
              <div>No: 2/548/D, Thadangam,</div>
              <div>Thokkampatti Post,</div>
              <div>Dharmapuri-636705.</div>
              <div style="margin-top: 4px;">Ph: +91 9443937841, 70100 76633.</div>
              <div>Ofc Ph.: 04342-232777</div>
              <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;04342-292377</div>
              <div style="margin-top: 2px;">E-mail: info.gtmsdpi@gmail.com.</div>
            </td>
            <td style="padding: 0; vertical-align: top;">
              <table class="ti-meta-sub-table">
                <tr>
                  <td class="m-lbl">Invoice No.:</td>
                  <td><strong>{{ $invoiceData['tiInvoiceNo'] }}</strong></td>
                </tr>
                <tr>
                  <td class="m-lbl">Invoice Date:</td>
                  <td><strong>{{ $invoiceData['invoiceDate'] }}</strong></td>
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
                  <td class="m-lbl">Ofc Ph.:</td>
                  <td>04342-232777<br>04342-292377</td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Client Info Rows -->
          <tr>
            <td class="ti-label-col">CLIENT NAME</td>
            <td class="ti-value-col" style="color: #c00000; font-style: italic; font-weight: bold;">
              {{ strtoupper($receipt->customer->company_name ?: $receipt->customer->customer_name) }}
            </td>
          </tr>
          <tr>
            <td class="ti-label-col">CLIENT ADDRESS</td>
            <td class="ti-value-col" style="font-style: italic;">
              {{ $receipt->customer->address ?: ($receipt->customer->area ?: 'Concession Site, ' . $invoiceData['districtName'] . ' District, Tamil Nadu') }}
            </td>
          </tr>
          <tr>
            <td class="ti-label-col">GSTIN</td>
            <td class="ti-value-col">
              {{ $receipt->customer->gstin ?: ($receipt->customer->pan ?: 'Unregistered / Exempt') }}
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
              <strong><em>{{ $invoiceData['particulars'] }}</em></strong>
            </td>
          </tr>
          <tr>
            <td class="ti-label-col">REFERENCE</td>
            <td class="ti-value-col">
              <em>{{ $invoiceData['mineralName'] }} quarry lease {{ $invoiceData['locationDetails'] }}</em>
            </td>
          </tr>

          <!-- Items Table Header -->
          <tr class="ti-table-header">
            <th>WORK DESCRIPTION</th>
            <th>AMOUNT</th>
          </tr>

          <!-- Dynamic Items Rows -->
          @foreach($invoiceData['items'] as $item)
          <tr>
            <td class="ti-desc-cell">
              <div style="font-weight: bold; font-size: 13.5px; color: #0F1E4D; margin-bottom: 4px;">
                @if($invoiceData['isConsolidated'] && count($invoiceData['items']) > 1)
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
              <ul>
                @foreach($item['bullets'] as $bullet)
                  <li>{{ $bullet }}</li>
                @endforeach
              </ul>
            </td>
            <td class="ti-amount-cell">{{ number_format($item['amount'], 2) }}</td>
          </tr>
          @endforeach

          @if(count($invoiceData['items']) > 1)
          <tr class="ti-subtotal-row">
            <td class="subtotal-label">Sub Total (Taxable Value)</td>
            <td class="subtotal-val">{{ number_format($invoiceData['subtotal'], 2) }}</td>
          </tr>
          @endif

          <!-- Tax & Total Rows -->
          <tr class="ti-subtotal-row">
            <td class="subtotal-label">SGST @ 9 %</td>
            <td class="subtotal-val">{{ number_format($invoiceData['sgst'], 2) }}</td>
          </tr>
          <tr class="ti-subtotal-row">
            <td class="subtotal-label">CGST @ 9%</td>
            <td class="subtotal-val">{{ number_format($invoiceData['cgst'], 2) }}</td>
          </tr>
          <tr class="ti-total-row">
            <td class="total-label">TOTAL ( {{ $invoiceData['amountInWords'] }} )</td>
            <td class="total-val">{{ number_format($invoiceData['grandTotal'], 2) }}</td>
          </tr>

          <!-- Bottom Row: Bank Details & Stamp/Signature -->
          <tr>
            <td style="padding: 0; vertical-align: bottom;">
              <table class="ti-bank-inner-table">
                <tr>
                  <td class="b-lbl">Name</td>
                  <td>Geo Technical Mining Solutions</td>
                </tr>
                <tr>
                  <td class="b-lbl">Account No.</td>
                  <td>107302000001265</td>
                </tr>
                <tr>
                  <td class="b-lbl">Bank/Branch</td>
                  <td>Indian Overseas Bank/ Oddapatti, Dharmapuri</td>
                </tr>
                <tr>
                  <td class="b-lbl">IFSC Code</td>
                  <td>IOBA0001073</td>
                </tr>
              </table>
            </td>
            <td class="ti-sign-box-cell">
              <div class="invoice-seal-slot">
                @if(!empty($invoiceData['sealImageUrl']))
                  <img src="{{ $invoiceData['sealImageUrl'] }}" alt="Authorized Signatory Seal" class="invoice-seal-img">
                  <div class="seal-empty-space" style="display: none;"></div>
                @else
                  <img src="" alt="Authorized Signatory Seal" class="invoice-seal-img" style="display: none;">
                  <div class="seal-empty-space"></div>
                @endif
              </div>
              <div class="ti-sign-person-title">
                Ms. V. Kaviya,<br>
                Finance Executive - GTMS
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Interactive Client-side Document Switching Script -->
  <script>
    function switchDocFormat(type) {
      // 1. Hide all document containers
      document.querySelectorAll('.doc-format-container').forEach(function(el) {
        el.classList.remove('active-doc');
      });

      // 2. Remove active class from buttons
      document.querySelectorAll('.format-btn').forEach(function(btn) {
        btn.classList.remove('active');
      });

      // 3. Activate selected document container & button
      var targetDoc = document.getElementById('doc-' + type);
      var targetBtn = document.getElementById('btn-' + type);
      if (targetDoc) {
        targetDoc.classList.add('active-doc');
      }
      if (targetBtn) {
        targetBtn.classList.add('active');
      }

      // 4. Update Document Title
      if (type === 'proforma') {
        document.title = "Proforma Invoice • " + "{{ $invoiceData['piInvoiceNo'] }}" + " • GTMS";
      } else {
        document.title = "Tax Invoice • " + "{{ $invoiceData['tiInvoiceNo'] }}" + " • GTMS";
      }

      // 5. Update URL state without reload
      if (window.history && window.history.replaceState) {
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('type', type);
        window.history.replaceState(null, '', currentUrl.toString());
      }

      // 6. Update scope switch links to preserve format type
      document.querySelectorAll('.scope-link').forEach(function(link) {
        try {
          var u = new URL(link.href, window.location.origin);
          u.searchParams.set('type', type);
          link.href = u.pathname + u.search;
        } catch(e) {}
      });
    }
  </script>

  @include('pages.accounts.partials.invoice_seal_modal')
</body>
</html>
