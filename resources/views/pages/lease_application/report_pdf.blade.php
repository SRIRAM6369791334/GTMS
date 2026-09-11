<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Report - {{ $application->application_no }}</title>
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5; padding: 30px; font-size: 13px; }
    .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-start; }
    .title { font-size: 20px; font-weight: bold; color: #1e3a8a; }
    .subtitle { font-size: 12px; color: #64748b; margin-top: 4px; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase; }
    .badge-approved { background: #dcfce7; color: #15803d; }
    .badge-scrutiny { background: #fef9c3; color: #854d0e; }
    .badge-validated { background: #dbeafe; color: #1d4ed8; }
    .badge-revision { background: #fee2e2; color: #b91c1c; }
    .section { margin-bottom: 25px; }
    .section-title { font-size: 14px; font-weight: bold; color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
    .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .grid-item { background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; }
    .label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; }
    .value { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
    th { background: #1e3a8a; color: white; text-align: left; padding: 8px 10px; font-weight: 600; }
    td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
    tr:nth-child(even) td { background: #f8fafc; }
    .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8; display: flex; justify-content: space-between; }
    @media print {
      body { padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 20px; text-align: right;">
    <button onclick="window.print()" style="background: #1e3a8a; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer;">
      🖨️ Print / Save as PDF
    </button>
  </div>

  <div class="header">
    <div>
      <div class="title">GOVERNMENT OF TAMIL NADU</div>
      <div class="subtitle">Department of Geology and Mining &middot; Official Scrutiny &amp; Compliance Dossier</div>
    </div>
    <div style="text-align: right;">
      <div style="font-size: 16px; font-weight: bold; color: #1e3a8a;">{{ $application->application_no }}</div>
      <span class="badge {{ $application->status === 'approved' ? 'badge-approved' : ($application->status === 'validated' ? 'badge-validated' : 'badge-scrutiny') }}">
        {{ strtoupper(str_replace('_', ' ', $application->status)) }}
      </span>
    </div>
  </div>

  <!-- 1. APPLICANT & BASIC INFORMATION -->
  <div class="section">
    <div class="section-title">1. Applicant &amp; Entity Details</div>
    <div class="grid">
      <div class="grid-item">
        <div class="label">Applicant / Entity</div>
        <div class="value">{{ $application->customer->company_name ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Representative Name</div>
        <div class="value">{{ $application->customer->customer_name ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">MIMAS Identifier</div>
        <div class="value">{{ $application->customer->mimas_no ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Aadhaar Number</div>
        <div class="value">{{ $application->customer->aadhaar_no ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">PAN Number</div>
        <div class="value">{{ $application->customer->pan ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">GSTIN</div>
        <div class="value">{{ $application->customer->gstin ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">District</div>
        <div class="value">{{ $application->district->name ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Contact Person</div>
        <div class="value">{{ $application->contact_person }} ({{ $application->contact_mobile }})</div>
      </div>
      <div class="grid-item">
        <div class="label">Submission Date</div>
        <div class="value">{{ $application->created_at ? $application->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
      </div>
    </div>
  </div>

  <!-- 2. LEASE & LOCATION DETAILS -->
  <div class="section">
    <div class="section-title">2. Lease &amp; Demarcation Details</div>
    <div class="grid">
      <div class="grid-item">
        <div class="label">Lease Category Under Rule</div>
        <div class="value">{{ $application->category->code ?? 'Rule 44' }} &mdash; {{ $application->category->name ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Mineral Specie</div>
        <div class="value">{{ $application->mineral->name ?? 'Rough Stone & Gravel' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Area Extent</div>
        <div class="value">{{ number_format($application->area_extent_ha, 2) }} Ha ({{ $application->area_extent_acres ?? '21.00 Acres' }})</div>
      </div>
      <div class="grid-item">
        <div class="label">Taluk &amp; Village</div>
        <div class="value">{{ $application->taluk ?? 'Kinathukadavu' }}, {{ $application->village ?? 'Vadakkipalayam' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Lease Term</div>
        <div class="value">{{ $application->lease_period_years }} Years ({{ $application->start_date }} to {{ $application->end_date }})</div>
      </div>
      <div class="grid-item">
        <div class="label">Registered S.F. Numbers</div>
        <div class="value">
          @foreach($application->surveyNumbers as $s)
            SF.No {{ $s->survey_no }} ({{ $s->extent_ha }} Ha, {{ $s->classification }})@if(!$loop->last), @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- 3. DOCUMENT VERIFICATION CHECKLIST -->
  <div class="section">
    <div class="section-title">3. Document Verification Checklist ({{ $application->documents->count() }} Attached Files)</div>
    <table>
      <thead>
        <tr>
          <th style="width: 5%;">#</th>
          <th style="width: 20%;">Folder</th>
          <th style="width: 45%;">Document / Checklist Item</th>
          <th style="width: 15%;">File Type</th>
          <th style="width: 15%;">Scrutiny Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($application->documents as $idx => $doc)
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td><b>{{ $doc->folder->name ?? 'Folder ' . $doc->folder_id }}</b></td>
          <td>{{ $doc->document_name }}</td>
          <td><code>{{ strtoupper(pathinfo($doc->file_name, PATHINFO_EXTENSION)) }}</code> ({{ round($doc->file_size / 1024, 0) }} KB)</td>
          <td>
            <span class="badge {{ $doc->status === 'validated' ? 'badge-validated' : ($doc->status === 'approved' ? 'badge-approved' : 'badge-scrutiny') }}">
              {{ strtoupper($doc->status) }}
            </span>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align: center; color: #94a3b8;">No documents uploaded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- 4. MIMAS CREDENTIALS (REDACTED FOR SECURITY) -->
  <div class="section">
    <div class="section-title">4. MIMAS Registration Identifier</div>
    <div class="grid">
      <div class="grid-item">
        <div class="label">MIMAS Portal ID</div>
        <div class="value">{{ $application->mimasCredentials->first()->user_id ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Registered Email</div>
        <div class="value">{{ $application->mimasCredentials->first()->email ?? 'N/A' }}</div>
      </div>
      <div class="grid-item">
        <div class="label">Verification Status</div>
        <div class="value" style="color: #15803d;">● {{ strtoupper($application->mimasCredentials->first()->portal_status ?? 'VERIFIED') }}</div>
      </div>
    </div>
  </div>

  <div class="footer">
    <div>GTMS System Generated Compliance Report &middot; Confidential</div>
    <div>Timestamp: {{ date('Y-m-d H:i:s') }}</div>
  </div>

</body>
</html>
