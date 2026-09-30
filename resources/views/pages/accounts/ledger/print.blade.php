<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statement of Account &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; GTMS</title>
  <style>
    /* Reset & Core Setup */
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

    /* Simulated A4 Sheet */
    .sheet {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      padding: 12mm 14mm 10mm 14mm;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
      position: relative;
    }

    /* Print Media Queries */
    @media print {
      @page {
        size: A4 portrait;
        margin: 8mm;
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

    /* Header Banner Styling */
    .header-banner {
      width: 100%;
      margin-bottom: 8px;
      display: block;
    }
    .header-banner img {
      width: 100%;
      height: auto;
      max-height: 105px;
      object-fit: contain;
    }

    .statement-title-wrap {
      text-align: center;
      margin: 8px 0 12px 0;
      border-bottom: 2px solid #0F1E4D;
      padding-bottom: 4px;
    }
    .statement-title {
      font-size: 16pt;
      font-weight: bold;
      color: #0F1E4D;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .statement-subtitle {
      font-size: 9.5pt;
      color: #333;
      margin-top: 2px;
      font-style: italic;
    }

    /* Metadata & Client Grids */
    .info-grid {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      font-size: 9.5pt;
    }
    .info-grid td {
      vertical-align: top;
      padding: 5px 8px;
      border: 1px solid #000;
    }
    .info-grid .header-cell {
      background-color: #f1f5f9;
      font-weight: bold;
      color: #0F1E4D;
      width: 25%;
    }

    /* Itemized Table */
    .ledger-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      font-size: 9pt;
    }
    .ledger-table th, .ledger-table td {
      border: 1px solid #000;
      padding: 5px 6px;
    }
    .ledger-table th {
      background-color: #0F1E4D;
      color: #fff;
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 8.5pt;
    }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }

    /* Summary & Total Block */
    .summary-box {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      font-size: 9.5pt;
    }
    .summary-box td {
      border: 1px solid #000;
      padding: 5px 8px;
    }

    .words-box {
      border: 1px solid #000;
      background-color: #f8fafc;
      padding: 6px 10px;
      font-size: 9pt;
      margin-bottom: 10px;
    }

    /* Bank Details & Terms */
    .bank-box {
      border: 1px solid #000;
      padding: 6px 10px;
      font-size: 8.5pt;
      line-height: 1.35;
      margin-bottom: 12px;
      background: #fafafa;
    }

    /* Signatory Zone */
    .signatory-zone {
      width: 100%;
      margin-top: 15px;
      font-size: 9pt;
      page-break-inside: avoid;
    }
    .signatory-zone td {
      vertical-align: bottom;
      padding: 0 10px;
    }
    .stamp-img {
      max-height: 70px;
      margin-bottom: 4px;
    }
  </style>
</head>
<body>

  <!-- Floating Screen Action Bar -->
  <div class="no-print-bar">
    <div>
      <strong>Statement of Account</strong> &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; Ref: {{ $statementRef }}
    </div>
    <div style="display:flex; gap:10px;">
      <a href="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" class="secondary">&larr; Back to Dossier</a>
      <button onclick="window.print()">🖨️ Print / Save PDF</button>
    </div>
  </div>

  <!-- A4 Printable Sheet -->
  <div class="sheet">
    <!-- Header Banner -->
    <div class="header-banner">
      <img src="{{ asset('images/invoices/gtms_pi_banner.png') }}" alt="GTMS Banner" onerror="this.style.display='none'">
    </div>

    <!-- Title Bar -->
    <div class="statement-title-wrap">
      <div class="statement-title">STATEMENT OF ACCOUNT / FINANCIAL LEDGER</div>
      <div class="statement-subtitle">Itemized Running Balance Statement for Commercial &amp; Statutory Services</div>
    </div>

    <!-- Statement Metadata & Client Profile -->
    <table class="info-grid">
      <tr>
        <td class="header-cell">Statement Ref No.</td>
        <td style="font-weight: bold; font-family: monospace;">{{ $statementRef }}</td>
        <td class="header-cell">Statement Date</td>
        <td>{{ $statementDate }}</td>
      </tr>
      <tr>
        <td class="header-cell">Statement Period</td>
        <td colspan="3">
          <strong>{{ $fromDate ? date('d-M-Y', strtotime($fromDate)) : 'All Inception' }}</strong> to <strong>{{ $toDate ? date('d-M-Y', strtotime($toDate)) : date('d-M-Y') }}</strong>
        </td>
      </tr>
      <tr>
        <td class="header-cell">Client / Entity Name</td>
        <td colspan="3" style="font-size: 10.5pt; font-weight: bold; color: #0F1E4D;">
          {{ $customer->company_name ?: $customer->customer_name }}
        </td>
      </tr>
      <tr>
        <td class="header-cell">Representative &amp; Phone</td>
        <td>{{ $customer->customer_name ?: 'Authorized Representative' }} &bull; {{ $customer->mobile_num ?: 'N/A' }}</td>
        <td class="header-cell">MIMAS Reg. ID</td>
        <td style="font-family: monospace;">{{ $customer->mimas_no ?: ($customer->mimas_number ?: 'N/A') }}</td>
      </tr>
      <tr>
        <td class="header-cell">Tax Identifiers</td>
        <td>GSTIN: {{ $customer->gstin ?: 'Unregistered' }} &bull; PAN: {{ $customer->pan ?: 'N/A' }}</td>
        <td class="header-cell">District / Location</td>
        <td>{{ $customer->district?->name ?? ($customer->district?->district_name ?? 'Tamil Nadu') }}</td>
      </tr>
      <tr>
        <td class="header-cell">Registered Concession</td>
        <td colspan="3">{{ $customer->address ?: 'Concession details on statutory file' }}</td>
      </tr>
    </table>

    <!-- Chronological Transactions Table -->
    <table class="ledger-table">
      <thead>
        <tr>
          <th style="width: 32px;">#</th>
          <th style="width: 75px;">Date</th>
          <th style="width: 65px;">Type</th>
          <th style="width: 130px;">Reference No.</th>
          <th>Particulars / Description</th>
          <th style="width: 85px;" class="text-end">Debit (₹)</th>
          <th style="width: 85px;" class="text-end">Credit (₹)</th>
          <th style="width: 95px;" class="text-end">Balance (₹)</th>
        </tr>
      </thead>
      <tbody>
        @if($fromDate && $openingBalance != 0)
          <tr style="background-color: #f8fafc;">
            <td class="text-center">—</td>
            <td class="text-center">{{ date('d-M-Y', strtotime($fromDate)) }}</td>
            <td class="text-center"><b>Opening</b></td>
            <td style="font-family: monospace; text-align: center;">—</td>
            <td><b>Opening Balance Brought Forward</b></td>
            <td class="text-end">—</td>
            <td class="text-end">—</td>
            <td class="text-end fw-bold">{{ number_format($openingBalance, 2) }}</td>
          </tr>
        @endif

        @forelse($transactions as $index => $tx)
          <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">{{ $tx->date->format('d-M-Y') }}</td>
            <td class="text-center">{{ $tx->type === 'quotation' ? 'Invoice' : 'Receipt' }}</td>
            <td style="font-family: monospace; font-weight: bold;">{{ $tx->ref_no }}</td>
            <td>{{ $tx->description }}</td>
            <td class="text-end">{{ $tx->debit > 0 ? number_format($tx->debit, 2) : '—' }}</td>
            <td class="text-end">{{ $tx->credit > 0 ? number_format($tx->credit, 2) : '—' }}</td>
            <td class="text-end fw-bold">{{ number_format($tx->running_balance, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center" style="padding: 20px;">
              No financial transactions recorded for this customer in the selected statement period.
            </td>
          </tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="5" class="text-end">STATEMENT TOTALS:</td>
          <td class="text-end">{{ number_format($periodBilled, 2) }}</td>
          <td class="text-end">{{ number_format($periodReceived, 2) }}</td>
          <td class="text-end" style="color: #0F1E4D; font-size: 10pt;">₹ {{ number_format($netBalance, 2) }}</td>
        </tr>
      </tfoot>
    </table>

    <!-- Net Dues in Words -->
    <div class="words-box">
      <strong>Net Balance Due (in words):</strong>
      <span style="font-style: italic; font-weight: bold; color: #0F1E4D;">{{ $netBalanceInWords }}</span>
    </div>

    <!-- Summary Box & Bank Details -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
      <tr>
        <td style="width: 58%; vertical-align: top; padding-right: 8px;">
          <div class="bank-box">
            <strong>OFFICIAL REMITTANCE BANK DETAILS:</strong><br>
            Beneficiary Name: <strong>GEO TECHNICAL MINING SOLUTIONS</strong><br>
            Current Account No.: <strong>38472910482</strong><br>
            Bank &amp; Branch: <strong>State Bank of India, Meyyanur Branch, Salem</strong><br>
            IFSC Code: <strong>SBIN0001234</strong> &bull; MICR: <strong>636002012</strong><br>
            <span style="font-size: 8pt; color: #555;">Please quote client name or quotation reference upon RTGS/NEFT remittance.</span>
          </div>
        </td>
        <td style="width: 42%; vertical-align: top;">
          <table class="summary-box">
            <tr>
              <td>Total Invoiced / Quoted:</td>
              <td class="text-end fw-bold">₹ {{ number_format($allTimeBilled, 2) }}</td>
            </tr>
            <tr>
              <td>Total Payments Received:</td>
              <td class="text-end fw-bold" style="color: green;">₹ {{ number_format($allTimeReceived, 2) }}</td>
            </tr>
            <tr style="background: #f1f5f9;">
              <td style="font-weight: bold; color: #0F1E4D;">NET BALANCE DUE:</td>
              <td class="text-end fw-bold" style="color: #b91c1c; font-size: 10.5pt;">₹ {{ number_format($netBalance, 2) }}</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <!-- Terms & Signatory Zone -->
    <table class="signatory-zone">
      <tr>
        <td style="width: 50%;">
          <div style="font-size: 8pt; color: #555; line-height: 1.3;">
            <strong>Verification Note:</strong><br>
            This statement of account reflects verified quotations and official money receipts issued up to {{ $statementDate }}. For any discrepancies or reconciliation requests, please contact accounts@gtmsmining.com within 7 working days.
          </div>
          <div style="margin-top: 30px;">
            <div style="border-top: 1px dotted #000; width: 180px; text-align: center; padding-top: 4px; font-weight: bold;">
              Prepared / Verified By
            </div>
            <div style="font-size: 8pt; color: #555;">Accounts Department</div>
          </div>
        </td>
        <td style="width: 50%; text-align: right;">
          <div>
            <img src="{{ asset('images/invoices/gtms_stamp.png') }}" class="stamp-img" alt="Official Seal" onerror="this.style.display='none'">
          </div>
          <div style="font-weight: bold; color: #0F1E4D;">For GEO TECHNICAL MINING SOLUTIONS</div>
          <div style="margin-top: 18px;">
            <div style="font-weight: bold;">Dr. S. Karuppannan, M.Sc., Ph.D.</div>
            <div style="font-size: 8.5pt; color: #333;">Managing Partner / RQP (Authorized Signatory)</div>
          </div>
        </td>
      </tr>
    </table>

  </div>

</body>
</html>
