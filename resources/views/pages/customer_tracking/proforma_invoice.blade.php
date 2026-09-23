<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proforma Invoice &bull; {{ $customer->company_name ?: $customer->customer_name }} &bull; GTMS</title>
  <style>
    /* Reset & Base Setup */
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

    /* A4 Paper Sheets */
    .page-sheet {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto 25px auto;
      background: #fff;
      padding: 10mm 15mm 12mm 15mm;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Header Banner */
    .header-banner-container {
      width: 100%;
      text-align: center;
      margin-bottom: 8px;
    }
    .header-banner-img {
      width: 100%;
      max-width: 100%;
      height: auto;
      display: block;
    }

    /* Document Title */
    .pi-title {
      text-align: center;
      font-size: 18pt;
      font-weight: bold;
      text-decoration: underline;
      margin: 4px 0 10px 0;
      letter-spacing: 0.5px;
    }

    /* Consignee & Meta Section */
    .consignee-meta-grid {
      width: 100%;
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      font-size: 11pt;
      line-height: 1.35;
    }
    .consignee-col {
      width: 55%;
    }
    .consignee-title {
      font-weight: bold;
      margin-bottom: 2px;
    }
    .text-maroon {
      color: #b22222;
    }
    .meta-col {
      width: 45%;
      text-align: left;
      padding-left: 20px;
    }
    .meta-row {
      display: flex;
      margin-bottom: 2px;
    }
    .meta-label {
      width: 105px;
      font-weight: normal;
    }
    .meta-val {
      flex: 1;
      font-weight: normal;
    }

    /* Work Order Box */
    .work-order-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
      margin-bottom: 12px;
    }
    .work-order-table td {
      border: 1px solid #000;
      padding: 3.5px 8px;
      font-size: 11pt;
    }
    .work-order-table td.wo-label {
      font-weight: bold;
      width: 48%;
    }
    .work-order-table td.wo-val {
      width: 52%;
      text-align: center;
    }

    /* Subject & Reference */
    .sub-ref-container {
      font-size: 10.5pt;
      line-height: 1.4;
      margin-bottom: 12px;
    }
    .sub-ref-container p {
      margin-bottom: 4px;
    }
    .sub-bold {
      font-weight: bold;
    }

    /* Items Table */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      margin-bottom: 12px;
    }
    .items-table th, .items-table td {
      border: 1px solid #000;
      padding: 4px 8px;
      font-size: 10.5pt;
      line-height: 1.3;
    }
    .items-table th {
      font-weight: bold;
      text-align: center;
    }
    .items-table td.sl-col {
      width: 7%;
      text-align: center;
      vertical-align: middle;
      font-size: 11pt;
    }
    .items-table td.desc-col {
      width: 73%;
      vertical-align: middle;
      padding: 4px 10px;
    }
    .items-table td.amt-col {
      width: 20%;
      text-align: center;
      vertical-align: middle;
      font-size: 11pt;
    }
    .bullet-list {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }
    .bullet-list li {
      position: relative;
      padding-left: 16px;
      margin-bottom: 2px;
    }
    .bullet-list li::before {
      content: "•";
      position: absolute;
      left: 2px;
      font-size: 13pt;
      top: -1px;
    }

    /* Summary Tax Rows */
    .summary-label-col {
      text-align: right;
      font-weight: bold;
      padding-right: 15px !important;
    }
    .summary-val-col {
      text-align: center;
      font-weight: normal;
    }
    .words-label {
      font-weight: bold;
      text-align: center;
      padding: 6px !important;
    }

    /* Terms Section on Page 2 */
    .terms-title {
      font-size: 13pt;
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 12px;
    }
    .terms-list {
      padding-left: 24px;
      font-size: 11pt;
      line-height: 1.5;
    }
    .terms-list li {
      margin-bottom: 8px;
    }

    /* Payment Terms Table */
    .payment-terms-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
      margin: 15px 0;
    }
    .payment-terms-table th, .payment-terms-table td {
      border: 1px solid #000;
      padding: 6px 10px;
      font-size: 10.5pt;
    }
    .payment-terms-table th {
      background-color: #f1f5f9;
      font-weight: bold;
      text-align: left;
    }

    /* Banking Coordinates Box */
    .bank-box {
      border: 1px solid #000;
      padding: 10px 14px;
      margin: 15px 0;
      font-size: 10.5pt;
      line-height: 1.4;
      background: #fafafa;
    }
    .bank-box-title {
      font-weight: bold;
      margin-bottom: 4px;
      text-decoration: underline;
    }

    /* Authorized Signature Stamp */
    .signatory-container {
      width: 100%;
      display: flex;
      justify-content: flex-end;
      margin-top: 15px;
    }
    .signatory-wrapper {
      text-align: center;
      width: 240px;
    }
    .stamp-img {
      width: 175px;
      height: auto;
      display: block;
      margin: 0 auto 4px auto;
    }
    .company-rep-title {
      font-weight: bold;
      font-size: 11pt;
      margin-bottom: 2px;
    }
    .company-rep-sub {
      font-size: 10pt;
      color: #333;
    }

    /* Print Styles */
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
      .page-sheet {
        box-shadow: none;
        margin: 0 auto;
        padding: 0;
        width: 100%;
        min-height: 290mm;
        page-break-after: always;
      }
      .page-sheet:last-child {
        page-break-after: avoid;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Action Bar -->
  <div class="no-print-bar">
    <div>
      <strong>GTMS Official Proforma Invoice</strong> &nbsp;|&nbsp; Ref: {{ $invoiceNo }} (Page 1 & 2 of 2) &bull; {{ $customer->company_name ?: $customer->customer_name }}
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('customer-tracking.show', $customer->slug ?? $customer->id) }}" class="secondary">
        &larr; Back to Customer 360
      </a>
      <a href="{{ route('customer-tracking.tax-invoice', $customer->slug ?? $customer->id) }}" class="secondary">
        View Tax Invoice &rarr;
      </a>
      <button onclick="window.print()">
        🖨️ Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PAGE 1 OF 2 : QUOTATION & SCOPE OF WORK     -->
  <!-- ========================================== -->
  <div class="page-sheet">
    <div>
      <!-- Top Letterhead Banner -->
      <div class="header-banner-container">
        <img src="{{ asset('images/invoices/gtms_pi_banner.png') }}" class="header-banner-img" alt="GTMS Letterhead">
      </div>

      <!-- Title -->
      <div class="pi-title">PROFORMA INVOICE</div>

      <!-- Consignee Details & Meta Subtable -->
      <div class="consignee-meta-grid">
        <div class="consignee-col">
          <div class="consignee-title">Consignee :</div>
          <div class="text-maroon" style="font-weight: bold; font-size: 11.5pt;">
            {{ strtoupper($customer->company_name ?: $customer->customer_name) }}
          </div>
          <div>
            {{ $customer->address ?: ($customer->area ? $customer->area . ', ' . $districtName : 'Concession Site, ' . $districtName . ' District, Tamil Nadu') }}
          </div>
          <div>Mobile: {{ $customer->mobile_num ?: 'Authorized Representative' }}</div>
          <div>GSTIN: {{ $customer->gstin ?: ($customer->pan ? '33' . $customer->pan . '1Z5' : '33AAACK5706F1Z8') }}</div>
          <div>PAN: {{ $customer->pan ?: 'AAACK5706F' }}</div>
          <div>State: Tamil Nadu (Code : 33)</div>
        </div>

        <div class="meta-col">
          <div class="meta-row"><span class="meta-label">PI No</span><span class="meta-val">: {{ $invoiceNo }}</span></div>
          <div class="meta-row"><span class="meta-label">Date</span><span class="meta-val">: {{ $invoiceDate }}</span></div>
          <div class="meta-row"><span class="meta-label">Rev.No.</span><span class="meta-val">: 00</span></div>
          <div class="meta-row"><span class="meta-label">Place of Supply</span><span class="meta-val">: Tamil Nadu (33)</span></div>
          <div class="meta-row"><span class="meta-label">Cust ID</span><span class="meta-val">: {{ $customer->mimas_no ?: 'CUST-'.str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span></div>
        </div>
      </div>

      <!-- Work Order Details Box -->
      <table class="work-order-table">
        <tr>
          <td class="wo-label">Work Order / Direct Memo No</td>
          <td class="wo-val">{{ $workOrderNo }}</td>
        </tr>
        <tr>
          <td class="wo-label">Work Order Date</td>
          <td class="wo-val">{{ $workOrderDate }}</td>
        </tr>
        <tr>
          <td class="wo-label">Mode of Engagement</td>
          <td class="wo-val">Authorized Statutory Engineering Mandate</td>
        </tr>
        <tr>
          <td class="wo-label">Contact Person</td>
          <td class="wo-val">{{ $customer->customer_name ?: 'Managing Director' }}</td>
        </tr>
        <tr>
          <td class="wo-label">Contact Mobile</td>
          <td class="wo-val">{{ $customer->mobile_num ?: '+91 94432 12345' }}</td>
        </tr>
      </table>

      <!-- Subject & Reference Lines -->
      <div class="sub-ref-container">
        <p>
          <span class="sub-bold">Sub:</span> Mineral Concession Filing, Mining Plan Preparation, DGPS Land Demarcation, Drone Survey &amp; Environmental Clearance (B1/B2) – Reg.
        </p>
        <p>
          <span class="sub-bold">Ref:</span> <span class="text-maroon">{{ $mineralName }} quarry lease</span> {{ $locationDetails }}
        </p>
      </div>

      <!-- Items Table -->
      <table class="items-table">
        <thead>
          <tr style="background:#f8fafc;">
            <th style="width: 7%;">Sl.<br>No.</th>
            <th style="width: 73%;">Description of Statutory Consultancy Work</th>
            <th style="width: 20%;">Amount<br>(INR)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $idx => $it)
            <tr>
              <td class="sl-col">{{ $idx + 1 }}</td>
              <td class="desc-col">
                <strong>{{ $it['title'] }}</strong>
                @if(!empty($it['sac']))
                  <span style="font-size:9pt; color:#64748b;">[SAC: {{ $it['sac'] }}]</span>
                @endif
                @if(!empty($it['bullets']))
                  <ul class="bullet-list" style="margin-top: 3px;">
                    @foreach($it['bullets'] as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </td>
              <td class="amt-col">₹ {{ number_format($it['amount'], 2) }}</td>
            </tr>
          @endforeach

          <!-- Subtotal -->
          <tr>
            <td colspan="2" class="summary-label-col">Total Basic Quotation Value</td>
            <td class="summary-val-col" style="font-weight: bold;">₹ {{ number_format($subtotal, 2) }}</td>
          </tr>

          <!-- CGST & SGST -->
          <tr>
            <td colspan="2" class="summary-label-col">Add: CGST @ 9%</td>
            <td class="summary-val-col">₹ {{ number_format($cgst, 2) }}</td>
          </tr>
          <tr>
            <td colspan="2" class="summary-label-col">Add: SGST @ 9%</td>
            <td class="summary-val-col">₹ {{ number_format($sgst, 2) }}</td>
          </tr>

          <!-- Grand Total -->
          <tr style="background:#f1f5f9;">
            <td colspan="2" class="words-label">
              Total Quoted Amount: <em>{{ $amountInWords }}</em>
            </td>
            <td class="summary-val-col" style="font-weight: bold; font-size: 11.5pt; color:#0F1E4D;">
              ₹ {{ number_format($grandTotal, 2) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Page 1 Footer Note -->
    <div style="font-size:9.5pt; text-align: center; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 4px;">
      Page 1 of 2 &bull; Continued on Page 2 for Commercial Terms, Milestone Schedule &amp; Bank Coordinates
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PAGE 2 OF 2 : TERMS, MILESTONES & BANKING  -->
  <!-- ========================================== -->
  <div class="page-sheet">
    <div>
      <!-- Top Letterhead Banner -->
      <div class="header-banner-container">
        <img src="{{ asset('images/invoices/gtms_pi_banner.png') }}" class="header-banner-img" alt="GTMS Letterhead">
      </div>

      <!-- Commercial Terms -->
      <div class="terms-title">Commercial Terms &amp; Regulatory Conditions:</div>
      <ol class="terms-list">
        <li><strong>Statutory Fees:</strong> Departmental scrutiny fees, SPCB consent application fees, and public hearing newspaper publication charges (if applicable) shall be remitted directly by the project proponent.</li>
        <li><strong>Document Authenticity:</strong> Revenue records including Patta, Chitta, Adangal, and Combined Sketch provided by the client are presumed genuine and legally certified.</li>
        <li><strong>Boundary Pillars:</strong> The client shall ensure site access and erection of standard boundary pillars as marked during DGPS survey.</li>
        <li><strong>Government Portal Approvals:</strong> Delivery schedules are contingent upon governmental online portal clearance times (MIMAS, PARIVESH, SEIAA-TN).</li>
        <li><strong>GST Regulations:</strong> Taxes are levied as per prevailing GST statutory council guidelines (SAC: 9983 Mining &amp; Technical Consultancy).</li>
        <li><strong>Validity:</strong> This Proforma Invoice &amp; commercial quotation remains valid for 30 calendar days from the date of issue.</li>
      </ol>

      <!-- Milestone Payment Schedule Table -->
      <div class="terms-title" style="margin-top: 20px;">Milestone Payment Schedule:</div>
      <table class="payment-terms-table">
        <thead>
          <tr>
            <th style="width: 25%;">Milestone Stage</th>
            <th style="width: 50%;">Deliverable / Trigger</th>
            <th style="width: 25%; text-align: center;">Percentage / Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>1. Mobilization Advance</strong></td>
            <td>Upon issuance of Work Order &amp; commencement of field DGPS / drone survey</td>
            <td style="text-align: center; font-weight: bold;">40% (₹ {{ number_format($grandTotal * 0.40, 2) }})</td>
          </tr>
          <tr>
            <td><strong>2. Draft Submission</strong></td>
            <td>Upon preparation of Draft Mining Plan &amp; PARIVESH Form-1 / EMP docket</td>
            <td style="text-align: center; font-weight: bold;">40% (₹ {{ number_format($grandTotal * 0.40, 2) }})</td>
          </tr>
          <tr>
            <td><strong>3. Final Approval</strong></td>
            <td>Upon final SEIAA / Department of Geology presentation &amp; grant order dispatch</td>
            <td style="text-align: center; font-weight: bold;">20% (₹ {{ number_format($grandTotal * 0.20, 2) }})</td>
          </tr>
        </tbody>
      </table>

      <!-- Banking Details Box -->
      <div class="bank-box">
        <div class="bank-box-title">Electronic Fund Transfer &amp; Bank Coordinates:</div>
        <div style="display: flex; justify-content: space-between; margin-top: 6px;">
          <div>
            <div><strong>Beneficiary:</strong> GEO TECHNICAL MINING SOLUTIONS</div>
            <div><strong>Account Number:</strong> 38472910482 (Current Account)</div>
            <div><strong>Bank Name:</strong> State Bank of India</div>
          </div>
          <div>
            <div><strong>Branch:</strong> Meyyanur Branch, Salem</div>
            <div><strong>IFSC Code:</strong> SBIN0001234</div>
            <div><strong>MICR Code:</strong> 636002015</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page 2 Footer Signatory -->
    <div>
      <div class="signatory-container">
        <div class="signatory-wrapper">
          <div class="company-rep-title">For Geo Technical Mining Solutions</div>
          <img src="{{ asset('images/invoices/gtms_stamp.png') }}" class="stamp-img" alt="Official Seal">
          <div class="company-rep-title" style="font-size: 11pt;">Dr. S. Karuppannan, M.Sc., Ph.D.</div>
          <div class="company-rep-sub">Managing Partner / RQP</div>
          <div style="font-size: 8.5pt; color: #64748b;">(Authorized Signatory)</div>
        </div>
      </div>

      <div style="font-size:9pt; text-align: center; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 15px;">
        Page 2 of 2 &bull; Geo Technical Mining Solutions &bull; Corporate Office: Meyyanur, Salem, Tamil Nadu
      </div>
    </div>
  </div>

</body>
</html>
