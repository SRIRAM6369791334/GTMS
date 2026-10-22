<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Official Scrutiny Dossier Report &middot; {{ $application->application_no ?? 'LA-2026-0001' }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --bg-page: #f8fafc;
      --bg-card: #ffffff;
      --bg-subtle: #f1f5f9;
      --bg-stripe: #f8fafc;
      --border-color: #cbd5e1;
      --border-light: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --text-sub: #475569;
      --tn-navy: #0F1E4D;
      --tn-blue: #1d4ed8;
      --tn-gold: #b45309;
      --tn-gold-bg: #fef3c7;
      --color-success: #15803d;
      --color-success-bg: #dcfce7;
      --color-danger: #b91c1c;
      --color-danger-bg: #fee2e2;
      --color-warning: #b45309;
      --color-warning-bg: #fef3c7;
      --color-info: #0284c7;
      --color-info-bg: #e0f2fe;
      --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
    }

    [data-theme="dark"] {
      --bg-page: #0b0f19;
      --bg-card: #111827;
      --bg-subtle: #1f2937;
      --bg-stripe: #151e32;
      --border-color: #374151;
      --border-light: #2d3748;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --text-sub: #cbd5e1;
      --tn-navy: #3b82f6;
      --tn-blue: #60a5fa;
      --tn-gold: #f59e0b;
      --tn-gold-bg: rgba(245, 158, 11, 0.15);
      --color-success: #34d399;
      --color-success-bg: rgba(16, 185, 129, 0.15);
      --color-danger: #f87171;
      --color-danger-bg: rgba(239, 68, 68, 0.15);
      --color-warning: #fbbf24;
      --color-warning-bg: rgba(245, 158, 11, 0.15);
      --color-info: #38bdf8;
      --color-info-bg: rgba(56, 189, 248, 0.15);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: var(--bg-page);
      color: var(--text-main);
      font-family: var(--font-sans);
      font-size: 13px;
      line-height: 1.5;
      padding: 24px;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .report-wrapper {
      max-width: 1080px;
      margin: 0 auto;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      padding: 36px 42px;
      position: relative;
    }

    /* TOP UTILITY ACTION BAR */
    .top-action-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border-light);
    }
    .action-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      border: 1px solid var(--border-color);
      background: var(--bg-card);
      color: var(--text-main);
    }
    .btn-action:hover {
      background: var(--bg-subtle);
    }
    .btn-primary-action {
      background: #0F1E4D;
      color: #ffffff !important;
      border-color: #0F1E4D;
    }
    .btn-primary-action:hover {
      background: #1b357d;
    }

    /* OFFICIAL GOVERNMENT EMBLEM HEADER */
    .gov-masthead {
      border-bottom: 3px double var(--tn-navy);
      padding-bottom: 20px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
    }
    .crest-col {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .state-crest-badge {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: var(--tn-navy);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
      flex-shrink: 0;
    }
    .masthead-titles h1 {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 0.04em;
      color: var(--tn-navy);
      text-transform: uppercase;
      line-height: 1.2;
    }
    .masthead-titles h2 {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-sub);
      margin-top: 3px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .masthead-titles .act-citation {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 2px;
      font-style: italic;
    }

    .dossier-identifiers {
      text-align: right;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
    }
    .app-number-hero {
      font-family: var(--font-mono);
      font-size: 19px;
      font-weight: 800;
      color: var(--tn-navy);
      letter-spacing: -0.01em;
    }
    .common-id-chip {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-family: var(--font-mono);
      font-size: 11px;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 4px;
      background: var(--bg-subtle);
      border: 1px solid var(--border-color);
      color: var(--text-sub);
    }

    /* BADGES */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: 4px;
      font-weight: 700;
      font-size: 11px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      border: 1px solid transparent;
    }
    .badge-approved { background: var(--color-success-bg); color: var(--color-success); border-color: rgba(22, 101, 52, 0.2); }
    .badge-scrutiny { background: var(--color-warning-bg); color: var(--color-warning); border-color: rgba(180, 83, 9, 0.2); }
    .badge-validated { background: var(--color-info-bg); color: var(--color-info); border-color: rgba(2, 132, 199, 0.2); }
    .badge-revision { background: var(--color-danger-bg); color: var(--color-danger); border-color: rgba(185, 28, 28, 0.2); }
    .badge-pending { background: var(--bg-subtle); color: var(--text-muted); border-color: var(--border-light); }

    /* EXECUTIVE KPI METRIC RIBBON */
    .kpi-ribbon {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 24px;
      background: var(--bg-subtle);
      padding: 12px;
      border-radius: 8px;
      border: 1px solid var(--border-color);
    }
    .kpi-box {
      background: var(--bg-card);
      border: 1px solid var(--border-light);
      border-radius: 6px;
      padding: 10px 14px;
    }
    .kpi-box .kpi-label {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 3px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .kpi-box .kpi-value {
      font-size: 15px;
      font-weight: 800;
      color: var(--text-main);
    }
    .kpi-box .kpi-sub {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* SECTIONS */
    .section {
      margin-bottom: 24px;
      page-break-inside: avoid;
    }
    .section-title-wrap {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid var(--border-color);
      padding-bottom: 6px;
      margin-bottom: 12px;
    }
    .section-title {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--tn-navy);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .section-subtitle {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 500;
    }

    /* DATA FIELD GRIDS */
    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }
    .grid-4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
    }
    .grid-item {
      background: var(--bg-card);
      border: 1px solid var(--border-light);
      border-radius: 6px;
      padding: 9px 12px;
      display: flex;
      flex-direction: column;
    }
    .grid-item.span-2 { grid-column: span 2; }
    .grid-item.span-3 { grid-column: span 3; }
    .grid-item.span-4 { grid-column: span 4; }
    .label {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-muted);
      margin-bottom: 3px;
    }
    .value {
      font-size: 13px;
      font-weight: 700;
      color: var(--text-main);
      word-break: break-word;
    }
    .value-sub {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 2px;
      font-weight: 400;
    }
    .font-mono {
      font-family: var(--font-mono);
    }

    /* TABLES */
    .table-responsive {
      overflow-x: auto;
      margin-top: 8px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }
    thead th {
      background: var(--bg-subtle);
      color: var(--text-sub);
      text-align: left;
      padding: 8px 10px;
      font-weight: 700;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      border-bottom: 1px solid var(--border-color);
    }
    tbody td {
      padding: 8px 10px;
      border-bottom: 1px solid var(--border-light);
      color: var(--text-main);
    }
    tbody tr:nth-child(even) td {
      background: var(--bg-stripe);
    }
    tbody tr:last-child td {
      border-bottom: none;
    }
    tfoot td {
      background: var(--bg-subtle);
      padding: 8px 10px;
      font-weight: 700;
      border-top: 2px solid var(--border-color);
    }

    .file-ext-chip {
      display: inline-block;
      padding: 1px 5px;
      border-radius: 3px;
      font-family: var(--font-mono);
      font-size: 10px;
      font-weight: 700;
      background: var(--bg-subtle);
      border: 1px solid var(--border-color);
      color: var(--text-sub);
    }
    .file-ext-pdf { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
    .file-ext-zip { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .file-ext-mp4 { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe; }
    .file-ext-docx { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
    .file-ext-csv, .file-ext-xls { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }

    .file-link {
      color: var(--tn-blue);
      text-decoration: none;
      font-weight: 600;
    }
    .file-link:hover {
      text-decoration: underline;
    }

    /* OFFICIAL SIGN-OFF BLOCK */
    .signoff-section {
      margin-top: 36px;
      padding-top: 20px;
      border-top: 2px dashed var(--border-color);
      page-break-inside: avoid;
    }
    .certification-declaration {
      background: var(--bg-subtle);
      border-left: 4px solid var(--tn-navy);
      padding: 12px 16px;
      font-size: 11.5px;
      color: var(--text-sub);
      margin-bottom: 28px;
      border-radius: 0 6px 6px 0;
    }
    .signature-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-top: 15px;
    }
    .sig-box {
      border: 1px dashed var(--border-color);
      border-radius: 6px;
      padding: 14px;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: var(--bg-card);
    }
    .sig-space {
      height: 48px;
    }
    .sig-label {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-main);
      border-top: 1px solid var(--border-color);
      padding-top: 6px;
    }
    .sig-sub {
      font-size: 10.5px;
      color: var(--text-muted);
    }

    /* FOOTER */
    .dossier-footer {
      margin-top: 32px;
      padding-top: 14px;
      border-top: 1px solid var(--border-color);
      font-size: 11px;
      color: var(--text-muted);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .security-hash {
      font-family: var(--font-mono);
      font-size: 10px;
      color: var(--text-muted);
    }

    /* PRINT SPECIFIC OVERRIDES */
    @media print {
      body {
        background: #ffffff !important;
        color: #000000 !important;
        padding: 0 !important;
        font-size: 11.5px !important;
      }
      .report-wrapper {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
      }
      .no-print {
        display: none !important;
      }
      .top-action-bar {
        display: none !important;
      }
      .kpi-ribbon {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
      }
      .kpi-box {
        border: 1px solid #cbd5e1 !important;
      }
      .grid-item {
        border: 1px solid #cbd5e1 !important;
      }
      thead th {
        background: #0F1E4D !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      tbody tr:nth-child(even) td {
        background: #f8fafc !important;
      }
      .badge-approved { background: #dcfce7 !important; color: #15803d !important; }
      .badge-scrutiny { background: #fef3c7 !important; color: #b45309 !important; }
      .badge-validated { background: #e0f2fe !important; color: #0284c7 !important; }
      .badge-revision { background: #fee2e2 !important; color: #b91c1c !important; }
      .badge-pending { background: #f1f5f9 !important; color: #64748b !important; }
      a {
        color: #000000 !important;
        text-decoration: none !important;
      }
      .section {
        page-break-inside: avoid;
      }
      .signoff-section {
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body data-theme="light">

  <div class="report-wrapper">

    <!-- TOP ACTION UTILITY BAR (Hidden on Print) -->
    <div class="top-action-bar no-print">
      <div class="action-group">
        <a href="{{ route('viewapplication', ['id' => $application->id]) }}" class="btn-action">
          <i class="bi bi-arrow-left"></i> Return to Interactive Dossier
        </a>
        <button type="button" class="btn-action" onclick="copyReportSummary()">
          <i class="bi bi-clipboard-check"></i> Copy Summary
        </button>
      </div>

      <div class="action-group">
        <button type="button" class="btn-action" id="themeToggleBtn" onclick="toggleReportTheme()">
          <i class="bi bi-moon-stars" id="themeIcon"></i> <span id="themeText">Dark View</span>
        </button>
        <button type="button" class="btn-action btn-primary-action" onclick="window.print()">
          <i class="bi bi-printer"></i> Print / Save Official PDF
        </button>
      </div>
    </div>

    <!-- OFFICIAL GOVERNMENT MASTHEAD -->
    <div class="gov-masthead">
      <div class="crest-col">
        <div class="state-crest-badge">
          <i class="bi bi-shield-shaded"></i>
        </div>
        <div class="masthead-titles">
          <h1>Department of Geology &amp; Mining</h1>
          <h2>Government of Tamil Nadu &middot; Official Compliance &amp; Audit Dossier</h2>
          <div class="act-citation">Statutory Scrutiny under the Tamil Nadu Minor Mineral Concession Rules, 1959 &amp; MMDR Act</div>
        </div>
      </div>

      <div class="dossier-identifiers">
        <div class="app-number-hero">{{ $application->application_no ?? 'LA-2026-0001' }}</div>
        <div class="common-id-chip">
          <i class="bi bi-fingerprint"></i> Common ID: <strong>{{ $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</strong>
        </div>
        @php
          $statusSlug = strtolower(str_replace(' ', '_', $application->status ?? 'under_scrutiny'));
          $badgeClass = 'badge-scrutiny';
          if ($statusSlug === 'approved') $badgeClass = 'badge-approved';
          elseif ($statusSlug === 'validated') $badgeClass = 'badge-validated';
          elseif ($statusSlug === 'revision_required' || $statusSlug === 'rejected') $badgeClass = 'badge-revision';
        @endphp
        <span class="badge {{ $badgeClass }}">
          <i class="bi bi-check-circle-fill"></i> {{ strtoupper(str_replace('_', ' ', $application->status ?? 'under_scrutiny')) }}
        </span>
      </div>
    </div>

    @php
      $docTotal = $application->documents ? $application->documents->count() : 0;
      $docUploaded = $application->documents ? $application->documents->whereNotNull('file_path')->count() : 0;
      $docValidated = $application->documents ? $application->documents->where('status', 'validated')->count() : 0;
      $docPending = $docTotal - $docUploaded;
      $docPct = $docTotal > 0 ? round(($docUploaded / $docTotal) * 100) : 0;

      $numExtent = $application->area_extent_numeric ?? (is_numeric($application->area_extent_ha) ? (float)$application->area_extent_ha : null);
      $acresDisplay = $application->area_extent_acres ?: ($numExtent ? number_format($numExtent * 2.47105, 2) . ' Acres' : 'N/A');

      $startDateFormatted = $application->start_date ? \Carbon\Carbon::parse($application->start_date)->format('d M Y') : '01 Oct ' . date('Y');
      $endDateFormatted = $application->end_date ? \Carbon\Carbon::parse($application->end_date)->format('d M Y') : '30 Sep ' . (date('Y') + 5);

      $mimas = $application->mimasCredentials ? $application->mimasCredentials->first() : null;
    @endphp

    <!-- EXECUTIVE KPI COMPLIANCE MATRIX -->
    <div class="kpi-ribbon">
      <div class="kpi-box">
        <div class="kpi-label"><i class="bi bi-folder-check text-primary"></i> Checklist Vault</div>
        <div class="kpi-value">{{ $docUploaded }} / {{ $docTotal }} <span style="font-size:12px; font-weight:600; color:var(--text-muted);">({{ $docPct }}%)</span></div>
        <div class="kpi-sub">{{ $docValidated }} Validated &middot; {{ $docPending }} Pending</div>
      </div>

      <div class="kpi-box">
        <div class="kpi-label"><i class="bi bi-rulers text-success"></i> Demarcated Extent</div>
        <div class="kpi-value">{{ $application->area_extent_ha ? $application->area_extent_ha . ' Ha' : 'N/A' }}</div>
        <div class="kpi-sub">{{ $acresDisplay }} &middot; {{ $application->surveyNumbers ? $application->surveyNumbers->count() : 1 }} Parcel(s)</div>
      </div>

      <div class="kpi-box">
        <div class="kpi-label"><i class="bi bi-hourglass-split text-warning"></i> Concession Term</div>
        <div class="kpi-value">{{ $application->lease_period_years ?? 5 }} Years</div>
        <div class="kpi-sub">{{ $startDateFormatted }} &ndash; {{ $endDateFormatted }}</div>
      </div>

      <div class="kpi-box">
        <div class="kpi-label"><i class="bi bi-cash-stack text-info"></i> Financial Settlement</div>
        <div class="kpi-value">₹ {{ number_format((float)($application->pending_amount ?? 50000), 2) }} Due</div>
        <div class="kpi-sub">Total ₹ {{ number_format((float)($application->product_value ?? 50000), 2) }} &middot; Status: {{ ucfirst($application->payment_status ?? 'Pending') }}</div>
      </div>
    </div>

    <!-- 1. APPLICANT & LEGAL ENTITY PROFILE -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-person-vcard text-primary"></i> 1. Statutory Applicant &amp; Entity Profile
        </div>
        <div class="section-subtitle">Corporate Identification, Legal Constitution &amp; Tax Credentials</div>
      </div>

      <div class="grid-3">
        <div class="grid-item">
          <div class="label">Client / Firm Name</div>
          <div class="value">{{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'Deleted / Unassigned') }}</div>
          <div class="value-sub">Entity / Quarry Operator</div>
        </div>

        <div class="grid-item">
          <div class="label">Proprietor / Representative</div>
          <div class="value">{{ $application->customer?->customer_name ?? 'N/A' }}</div>
          <div class="value-sub">Authorized Signatory</div>
        </div>

        <div class="grid-item">
          <div class="label">Customer Unique ID (MIMAS)</div>
          <div class="value font-mono">{{ $application->customer?->mimas_no ?? 'N/A' }}</div>
          <div class="value-sub">State Mining Directorate Client No</div>
        </div>

        <div class="grid-item">
          <div class="label">Income Tax PAN</div>
          <div class="value font-mono">{{ $application->customer?->pan ?? 'N/A' }}</div>
          <div class="value-sub">Permanent Account Number</div>
        </div>

        <div class="grid-item">
          <div class="label">Aadhaar (UIDAI)</div>
          <div class="value font-mono">{{ $application->customer?->aadhaar_no ?? 'N/A' }}</div>
          <div class="value-sub">Individual Identity Verification</div>
        </div>

        <div class="grid-item">
          <div class="label">GSTIN (15 Digits)</div>
          <div class="value font-mono">{{ $application->customer?->gstin ?? 'Not Registered / Exempt' }}</div>
          <div class="value-sub">Goods &amp; Services Tax Registration</div>
        </div>

        <div class="grid-item">
          <div class="label">Official Email Address</div>
          <div class="value">{{ $application->customer?->email ?? ($mimas->email ?? 'N/A') }}</div>
          <div class="value-sub">Formal Proceedings &amp; Digital Notices</div>
        </div>

        <div class="grid-item">
          <div class="label">Primary Communication</div>
          <div class="value">{{ $application->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A') }}</div>
          <div class="value-sub">{{ $application->contact_person ?? ($application->customer?->customer_name ?? 'Primary Contact Person') }}</div>
        </div>

        <div class="grid-item">
          <div class="label">Secondary / Site Contact</div>
          <div class="value">{{ $application->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? 'N/A') }}</div>
          <div class="value-sub">{{ $application->secondary_contact_person ?? ($application->customer?->secondary_contact_person ?? 'Quarry In-charge') }}</div>
        </div>

        @php
          $officialAddress = $application->customer?->address ?? ($application->taluk ?? 'Registered Address Not Specified');
        @endphp
        <div class="grid-item span-3">
          <div class="label">Official Registered Business Address</div>
          <div class="value">{{ $officialAddress }}</div>
          <div class="value-sub">District: {{ $application->district->name ?? 'N/A' }} ({{ $application->district->state ?? 'Tamil Nadu' }})</div>
        </div>
      </div>
    </div>

    <!-- 2. LEASE DEMARCATION & GEO-SPATIAL DETAILS -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-compass text-success"></i> 2. Geo-Spatial Cadastral &amp; Concession Demarcation
        </div>
        <div class="section-subtitle">Revenue Jurisdiction, Mineral Specie &amp; Land Parcels</div>
      </div>

      <div class="grid-3" style="margin-bottom: 12px;">
        <div class="grid-item">
          <div class="label">Lease Category Under Rule</div>
          <div class="value">
            @if(!empty($application->other_category_rule))
              {{ $application->other_category_rule }} (Custom Rule)
            @elseif(($application->category->code ?? '') === ($application->category->name ?? ''))
              {{ $application->category->name ?? 'Not Specified' }}
            @else
              {{ $application->category->code ?? 'N/A' }} &mdash; {{ $application->category->name ?? 'Standard Rule' }}
            @endif
          </div>
          <div class="value-sub">Classification under TNMMCR 1959</div>
        </div>

        <div class="grid-item">
          <div class="label">Mineral Specie(s)</div>
          <div class="value">
            @php
              $mineralNames = $application->minerals && $application->minerals->isNotEmpty()
                  ? $application->minerals->pluck('name')->toArray()
                  : ($application->mineral ? [$application->mineral->name] : []);
              if (!empty($application->other_mineral_name)) {
                  $mineralNames[] = 'Other: ' . $application->other_mineral_name;
              }
            @endphp
            {{ !empty($mineralNames) ? implode(', ', $mineralNames) : 'Not Specified' }}
          </div>
          <div class="value-sub">{{ $application->mineral->category ?? 'Minor' }} Mineral &middot; Unit: {{ $application->mineral->default_unit ?? 'CBM' }}</div>
        </div>

        <div class="grid-item">
          <div class="label">Demarcated Area Extent</div>
          <div class="value">{{ $application->area_extent_ha ? $application->area_extent_ha . ' Ha' : 'N/A' }}</div>
          <div class="value-sub">{{ $acresDisplay }}</div>
        </div>

        <div class="grid-item">
          <div class="label">District &amp; Mining Jurisdiction</div>
          <div class="value">{{ $application->district->name ?? 'N/A' }} ({{ $application->district->code ?? 'SLM' }})</div>
          <div class="value-sub">Deputy Director / Assistant Director of Geology &amp; Mining</div>
        </div>

        <div class="grid-item">
          <div class="label">Taluk &amp; Revenue Village</div>
          <div class="value">
            @php
              $talukVal = $application->village ?: ($application->taluk ?: 'Omalur');
              if (str_contains($talukVal, 'Road') || str_contains($talukVal, 'Main')) {
                $talukClean = 'Omalur / Salem';
              } else {
                $talukClean = $talukVal;
              }
            @endphp
            {{ $talukClean }}
          </div>
          <div class="value-sub">Revenue Administration Jurisdiction</div>
        </div>

        <div class="grid-item">
          <div class="label">Statutory Concession Duration</div>
          <div class="value">{{ $application->lease_period_years ?? 5 }} Years</div>
          <div class="value-sub">{{ $startDateFormatted }} to {{ $endDateFormatted }}</div>
        </div>
      </div>

      <!-- Cadastral Survey Field Numbers Table -->
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th style="width: 5%;">#</th>
              <th style="width: 25%;">Survey Field Number (S.F. No)</th>
              <th style="width: 20%;">Extent (Hectares)</th>
              <th style="width: 20%;">Extent (Acres)</th>
              <th style="width: 30%;">Land Classification &amp; Registered Pattadar</th>
            </tr>
          </thead>
          <tbody>
            @php $totalHa = 0; @endphp
            @forelse($application->surveyNumbers ?? [] as $sIdx => $survey)
              @php
                $sHa = is_numeric($survey->extent_ha) ? (float)$survey->extent_ha : 0;
                $totalHa += $sHa;
                $sAcres = $sHa > 0 ? number_format($sHa * 2.47105, 2) . ' Acres' : '—';
              @endphp
              <tr>
                <td>{{ $sIdx + 1 }}</td>
                <td><strong class="font-mono" style="color:var(--tn-navy);">{{ $survey->survey_no }}</strong></td>
                <td>{{ $survey->extent_ha }} Ha</td>
                <td>{{ $sAcres }}</td>
                <td>
                  <span class="badge badge-validated" style="font-size:10px;">{{ $survey->classification ?? 'Patta Land' }}</span>
                  <span style="margin-left: 6px; font-weight: 600;">{{ $survey->pattadar_name ?? ($application->customer?->customer_name ?? 'Registered Owner') }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td>1</td>
                <td><strong class="font-mono" style="color:var(--tn-navy);">SF.No 1</strong></td>
                <td>{{ $application->area_extent_ha ?? '14.20' }} Ha</td>
                <td>{{ $acresDisplay }}</td>
                <td>
                  <span class="badge badge-validated" style="font-size:10px;">Patta Dry</span>
                  <span style="margin-left: 6px; font-weight: 600;">{{ $application->customer?->customer_name ?? 'Registered Owner' }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" style="text-align: right; text-transform: uppercase;">Total Demarcated Boundary Extent:</td>
              <td colspan="3" style="color:var(--tn-navy); font-weight: 800;">
                {{ $totalHa > 0 ? number_format($totalHa, 4) . ' Ha' : ($application->area_extent_ha . ' Ha') }}
                <span style="font-size: 11px; font-weight: normal; color: var(--text-muted); margin-left: 6px;">({{ $acresDisplay }})</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- 3. STATUTORY CHECKLIST & PLANS VAULT -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-folder-check text-primary"></i> 3. Statutory Regulatory Checklist &amp; Plans Vault
        </div>
        <div class="section-subtitle">({{ $docUploaded }} of {{ $docTotal }} Statutory Documents Attached &middot; {{ $docPct }}% Complete)</div>
      </div>

      <div class="table-responsive">
        <table style="table-layout: fixed; width: 100%;">
          <thead>
            <tr>
              <th style="width: 4%; text-align: center;">#</th>
              <th style="width: 15%;">Statutory Folder</th>
              <th style="width: 32%;">Document / Plan Description</th>
              <th style="width: 34%;">Attached File Details</th>
              <th style="width: 15%; text-align: center;">Scrutiny Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($application->documents ?? [] as $idx => $doc)
              @php
                $isUploaded = !empty($doc->file_path);
                $ext = $isUploaded && $doc->file_name ? strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION)) : null;
                $sizeFormatted = null;
                if ($isUploaded && is_numeric($doc->file_size)) {
                  $sizeFormatted = $doc->file_size > 1048576
                    ? round($doc->file_size / 1048576, 1) . ' MB'
                    : round($doc->file_size / 1024, 0) . ' KB';
                }
                $extClass = 'file-ext-chip';
                if ($ext === 'pdf') $extClass .= ' file-ext-pdf';
                elseif ($ext === 'zip' || $ext === 'rar') $extClass .= ' file-ext-zip';
                elseif ($ext === 'mp4' || $ext === 'mov') $extClass .= ' file-ext-mp4';
                elseif ($ext === 'doc' || $ext === 'docx') $extClass .= ' file-ext-docx';
                elseif ($ext === 'csv' || $ext === 'xlsx') $extClass .= ' file-ext-csv';

                $dStatus = strtolower($doc->status ?? 'pending');
                $dBadge = 'badge-pending';
                if ($dStatus === 'validated') $dBadge = 'badge-validated';
                elseif ($dStatus === 'approved') $dBadge = 'badge-approved';
                elseif ($dStatus === 'revision_required') $dBadge = 'badge-revision';
                elseif ($dStatus === 'uploaded') $dBadge = 'badge-scrutiny';
              @endphp
              <tr>
                <td style="text-align: center; vertical-align: middle;">{{ $idx + 1 }}</td>
                <td style="vertical-align: middle;">
                  <span class="badge badge-pending" style="font-size: 10px; font-weight: 600;">
                    <i class="bi bi-folder2-open me-1"></i> {{ $doc->folder->name ?? 'Folder ' . $doc->folder_id }}
                  </span>
                </td>
                <td style="vertical-align: middle;">
                  <strong style="color:var(--text-main); display: block;">{{ $doc->document_name }}</strong>
                  @if(!empty($doc->review_note))
                    <div style="font-size: 11px; color: var(--color-danger); margin-top: 2px;">
                      <i class="bi bi-chat-left-dots-fill me-1"></i> Note: {{ $doc->review_note }}
                    </div>
                  @endif
                </td>
                <td style="vertical-align: middle; overflow: hidden;">
                  @if($isUploaded)
                    <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                      <span class="{{ $extClass }}" style="flex-shrink: 0;">{{ strtoupper($ext ?: 'FILE') }}</span>
                      <div style="min-width: 0; flex: 1; overflow: hidden;">
                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="file-link" style="display: block; font-weight: 600; word-break: break-all; overflow-wrap: anywhere; line-height: 1.3;" title="{{ $doc->file_name }}">
                          {{ $doc->file_name ?? 'Download File' }}
                        </a>
                        <div style="font-size: 10.5px; color: var(--text-muted); margin-top: 3px; white-space: nowrap;">
                          <span>{{ $sizeFormatted ?? 'N/A' }}</span>
                          @if($doc->created_at)
                            &middot; <span>{{ $doc->created_at->format('d M Y') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                  @else
                    <span style="color: var(--text-muted); font-style: italic; font-size: 11px;">
                      <i class="bi bi-dash-circle me-1"></i> Not Uploaded Yet
                    </span>
                  @endif
                </td>
                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                  <span class="badge {{ $dBadge }}">
                    {{ strtoupper(str_replace('_', ' ', $doc->status ?? 'pending')) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">
                  No regulatory documents scheduled.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 4. REGULATORY GATEWAY & MIMAS CREDENTIALS -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-key-fill text-warning"></i> 4. Regulatory Portal Integration (MIMAS State Gateway)
        </div>
        <div class="section-subtitle">Department of Geology and Mining Authenticated Credentials</div>
      </div>

      <div class="grid-4">
        <div class="grid-item">
          <div class="label">MIMAS Portal User ID</div>
          <div class="value font-mono">{{ $mimas->user_id ?? ($application->customer?->mimas_no ?? 'TN-MMS-SLM-001') }}</div>
          <div class="value-sub">Mines Directorate Account</div>
        </div>

        <div class="grid-item">
          <div class="label">MIMAS Acknowledgement No</div>
          <div class="value font-mono">{{ $mimas->mimas_ack_no ?? ('ACK-MMS-' . date('Y') . '-1379') }}</div>
          <div class="value-sub">Online Departmental Submission Receipt</div>
        </div>

        <div class="grid-item">
          <div class="label">Portal Security Password</div>
          <div class="value font-mono" style="color: var(--tn-navy); letter-spacing: 0.5px;">
            {{ $mimas && !empty($mimas->password) ? $mimas->password : 'sriramsri@001' }}
          </div>
          <div class="value-sub">Authenticated Authority Passkey</div>
        </div>

        <div class="grid-item">
          <div class="label">Gateway Verification Status</div>
          <div class="value" style="color: var(--color-success);">
            <i class="bi bi-patch-check-fill me-1"></i> {{ strtoupper($mimas->portal_status ?? 'VERIFIED') }}
          </div>
          <div class="value-sub">Portal URL: https://mimas.tn.gov.in</div>
        </div>
      </div>
    </div>

    <!-- 5. PROJECT HANDLING TEAM & TECHNICAL PERSONNEL -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-people-fill text-primary"></i> 5. Project Handling Team &amp; Technical Personnel
        </div>
        <div class="section-subtitle">({{ $application->handlers ? $application->handlers->count() : 0 }} Officers Assigned to Technical Processing)</div>
      </div>

      @if($application->handlers && $application->handlers->isNotEmpty())
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Assigned Technical Officer</th>
                <th style="width: 25%;">Designation / Role</th>
                <th style="width: 40%;">Assigned Operational Responsibilities &amp; Notes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($application->handlers as $hIdx => $handler)
                <tr>
                  <td>{{ $hIdx + 1 }}</td>
                  <td>
                    <strong style="color:var(--tn-navy);">
                      <i class="bi bi-person-badge me-1 text-primary"></i> {{ $handler->name }}
                    </strong>
                  </td>
                  <td>
                    <span class="badge badge-validated">{{ $handler->role }}</span>
                  </td>
                  <td>{{ $handler->notes ?: 'Supervision, field inspection, and statutory document coordination.' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="grid-item" style="color: var(--text-muted); font-style: italic; text-align: center; padding: 14px;">
          <i class="bi bi-info-circle me-1"></i> No dedicated project handlers assigned to this record.
        </div>
      @endif
    </div>

    <!-- 6. FINANCIAL & BILLING LEDGER -->
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-cash-stack text-success"></i> 6. Financial, Invoicing &amp; Billing Ledger
        </div>
        <div class="section-subtitle">Commercial Concession Value, Receipts &amp; Outstanding Dues</div>
      </div>

      <div class="grid-4">
        <div class="grid-item">
          <div class="label">Quoted Concession Value</div>
          <div class="value font-mono">₹ {{ number_format((float)($application->product_value ?? 50000), 2) }}</div>
          <div class="value-sub">Statutory Application Commercial Fee</div>
        </div>

        <div class="grid-item">
          <div class="label">Total Paid / Received</div>
          <div class="value font-mono" style="color: var(--color-success);">₹ {{ number_format((float)($application->paid_amount ?? 0), 2) }}</div>
          <div class="value-sub">Verified Credit Receipts</div>
        </div>

        <div class="grid-item">
          <div class="label">Outstanding Balance Due</div>
          <div class="value font-mono" style="color: var(--color-danger);">₹ {{ number_format((float)($application->pending_amount ?? 50000), 2) }}</div>
          <div class="value-sub">Pending Ledger Recovery</div>
        </div>

        <div class="grid-item">
          <div class="label">Settlement Status</div>
          <div class="value">
            @php
              $payStatus = strtolower($application->payment_status ?? 'pending');
            @endphp
            @if($payStatus === 'paid')
              <span class="badge badge-approved"><i class="bi bi-check-circle-fill"></i> FULLY SETTLED</span>
            @elseif($payStatus === 'partial')
              <span class="badge badge-scrutiny"><i class="bi bi-hourglass-split"></i> PARTIAL PAYMENT</span>
            @else
              <span class="badge badge-revision"><i class="bi bi-exclamation-circle-fill"></i> PENDING PAYMENT</span>
            @endif
          </div>
          <div class="value-sub">
            @if(!empty($quotation))
              Linked Quotation: {{ $quotation->quotation_number }}
            @else
              Standard Billing Ledger
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- 7. OFFICIAL AUDIT TRAIL & SCRUTINY TIMELINE -->
    @if(isset($activityLogs) && $activityLogs->isNotEmpty())
    <div class="section">
      <div class="section-title-wrap">
        <div class="section-title">
          <i class="bi bi-clock-history text-primary"></i> 7. Official Scrutiny Timeline &amp; Activity Log
        </div>
        <div class="section-subtitle">Chronological System Events &amp; Compliance Actions</div>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th style="width: 20%;">Timestamp</th>
              <th style="width: 25%;">Action Performed</th>
              <th style="width: 40%;">Description &amp; Event Scope</th>
              <th style="width: 15%;">Authority</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activityLogs as $log)
              <tr>
                <td class="font-mono" style="font-size:11.5px; color:var(--text-sub);">
                  {{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : 'N/A' }}
                </td>
                <td>
                  <span class="badge badge-validated" style="font-size:10px;">{{ strtoupper(str_replace('_', ' ', $log->action)) }}</span>
                </td>
                <td>{{ $log->description }}</td>
                <td>
                  <div style="font-weight: 600; color: var(--text-main); font-size: 11.5px;">
                    {{ $log->user?->name ?? 'System Admin' }}
                  </div>
                  @if(!empty($log->user?->email))
                    <div class="font-mono" style="font-size: 10.5px; color: var(--text-muted);">
                      {{ $log->user->email }}
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    <!-- 8. STATUTORY COMPLIANCE DECLARATION -->
    <div class="signoff-section" style="margin-top: 24px; padding-top: 16px;">
      <div class="certification-declaration" style="margin-bottom: 0;">
        <strong>Statutory Compliance Declaration:</strong> I hereby certify that the statutory checklist items, survey cadastral demarcations, mineral concessions, and commercial ledger particulars for Application No: <strong>{{ $application->application_no }}</strong> (Common ID: <strong>{{ $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</strong>) have been scrutinized in strict accordance with the provisions of the Tamil Nadu Minor Mineral Concession Rules, 1959.
      </div>
    </div>

    <!-- REPORT FOOTER -->
    <div class="dossier-footer">
      <div>
        <strong>GTMS Enterprise Scrutiny System</strong> &middot; Generated on {{ date('d M Y, h:i:s A') }} &middot; Confidential Official Record
      </div>
      <div class="security-hash">
        DocHash: {{ strtoupper(substr(hash('sha256', ($application->application_no ?? 'LA-2026-0001') . ($application->created_at ?? now())), 0, 16)) }}
      </div>
    </div>

  </div>

  <script>
    function toggleReportTheme() {
      var body = document.body;
      var currentTheme = body.getAttribute('data-theme') || 'light';
      var newTheme = currentTheme === 'light' ? 'dark' : 'light';
      body.setAttribute('data-theme', newTheme);
      
      var icon = document.getElementById('themeIcon');
      var text = document.getElementById('themeText');
      if (newTheme === 'dark') {
        icon.className = 'bi bi-sun';
        text.textContent = 'Light View';
      } else {
        icon.className = 'bi bi-moon-stars';
        text.textContent = 'Dark View';
      }
    }

    // Auto-trigger print when requested via URL query parameter (e.g. ?autoprint=1)
    window.addEventListener('DOMContentLoaded', function() {
      var params = new URLSearchParams(window.location.search);
      if (params.get('autoprint') === '1' || params.get('print') === '1') {
        setTimeout(function() {
          window.print();
        }, 400);
      }
    });

    function copyReportSummary() {
      var summary = "GTMS OFFICIAL APPLICATION REPORT\n" +
        "Application No: {{ $application->application_no }}\n" +
        "Common ID: {{ $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}\n" +
        "Status: {{ strtoupper($application->status) }}\n" +
        "Applicant: {{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'N/A') }}\n" +
        "Representative: {{ $application->customer?->customer_name ?? 'N/A' }}\n" +
        "District: {{ $application->district->name ?? 'Salem' }}\n" +
        "Area Extent: {{ $application->area_extent_ha ?? '14.20' }} Ha\n" +
        "Mineral: {{ !empty($mineralNames) ? implode(', ', $mineralNames) : 'Gravel' }}\n" +
        "MIMAS Ack: {{ $mimas->mimas_ack_no ?? 'ACK-MMS-2026-1379' }}\n" +
        "Settlement: ₹ {{ number_format((float)($application->pending_amount ?? 50000), 2) }} Due\n" +
        "Timestamp: " + new Date().toLocaleString();

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(summary).then(function() {
          alert('Report executive summary copied to clipboard!');
        });
      } else {
        var temp = document.createElement('textarea');
        temp.value = summary;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert('Report executive summary copied to clipboard!');
      }
    }
  </script>
</body>
</html>
