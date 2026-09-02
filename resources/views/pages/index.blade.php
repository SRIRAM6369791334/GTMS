@extends('layouts.app')
@section('title', 'Dashboard')
@section('main_content')


<div class="content-body default-height">
            <!-- row -->
            <div class="container-fluid">
                <main class="page">
      <div class="page-head">
        <div>
          <span class="eyebrow"><i class="bi bi-geo-alt"></i> Coimbatore Region</span>
          <h1>Good morning, Kannan.</h1>
          <p>7 applications need action today across 4 districts.</p>
        </div>
        {{-- <a href="new-application.html" class="btn btn-gold px-3"><i class="bi bi-plus-lg me-1"></i>New Application</a> --}}
      </div>

      <!-- Stat cards -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--navy-100); color:var(--navy-800);"><i class="fa fa-folder"></i></div>
            <div><div class="val">128</div><div class="lbl">Active applications</div><div class="delta text-success">▲ 6 this week</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--warn-bg); color:var(--warn);"><i class="fa fa-hourglass-half"></i></div>
            <div><div class="val">23</div><div class="lbl">Pending validation</div><div class="delta text-warning-emphasis">4 overdue</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--ok-bg); color:var(--ok);"><i class="fa fa-check-circle"></i></div>
            <div><div class="val">86</div><div class="lbl">Approved &amp; verified</div><div class="delta text-success">▲ 12 this month</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--f-others-bg); color:var(--f-others);"><i class="fa fa-cloud-upload"></i></div>
            <div><div class="val">19</div><div class="lbl">Archived &amp; backed up</div><div class="delta" style="color:var(--ink-500);">Last run 6:00 AM</div></div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <!-- Recent applications -->
        <div class="col-lg-8">
          <div class="surface p-3 p-lg-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h2 class="h6 fw-bold mb-0">Recent applications</h2>
              <a href="#" class="small text-decoration-none">View all →</a>
            </div>
            <div class="table-responsive">
              <table class="table table-clean align-middle mb-0">
                <thead><tr><th>Client</th><th>District</th><th>Mineral</th><th>Plan type</th><th>Stage</th><th></th></tr></thead>
                <tbody>
                  <tr>
                    <td><span class="fw-semibold">Sri Bala Traders</span></td>
                    <td>Salem</td>
                    <td>Granite</td>
                    <td>Mining Plan</td>
                    <td><span class="chip chip-warn"><i class="bi bi-hourglass-split"></i> Validating</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                  <tr>
                    <td><span class="fw-semibold">M. Elumalai &amp; Sons</span></td>
                    <td>Namakkal</td>
                    <td>Rough Stone</td>
                    <td>Revised Mining Plan</td>
                    <td><span class="chip chip-ok"><i class="bi bi-check2"></i> Approved</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                  <tr>
                    <td><span class="fw-semibold">Kaveri Minerals Pvt Ltd</span></td>
                    <td>Erode</td>
                    <td>Lime Stone</td>
                    <td>Scheme of Mining Plan</td>
                    <td><span class="chip chip-navy"><i class="bi bi-cloud-arrow-up"></i> Uploading</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                  <tr>
                    <td><span class="fw-semibold">Velan Fire Clay Works</span></td>
                    <td>Coimbatore</td>
                    <td>Fire Clay</td>
                    <td>Modified Mining Plan</td>
                    <td><span class="chip chip-danger"><i class="bi bi-exclamation-triangle"></i> Correction needed</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                  <tr>
                    <td><span class="fw-semibold">SR Gravel Suppliers</span></td>
                    <td>Tiruppur</td>
                    <td>Gravel</td>
                    <td>Mining Plan</td>
                    <td><span class="chip chip-ok"><i class="bi bi-cloud-check"></i> Archived</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pipeline snapshot -->
        <div class="col-lg-4">
          <div class="surface p-3 p-lg-4 h-100">
            <h2 class="h6 fw-bold mb-3">Process pipeline</h2>
            <div class="d-flex flex-column gap-3">
              <div>
                <div class="d-flex justify-content-between small mb-1"><span>Upload &amp; store</span><span class="text-mono fw-semibold">44</span></div>
                <div class="prog" style="height:6px;border-radius:5px;background:var(--ink-100);overflow:hidden;"><span style="display:block;height:100%;width:88%;background:var(--navy-700);"></span></div>
              </div>
              <div>
                <div class="d-flex justify-content-between small mb-1"><span>Validate data</span><span class="text-mono fw-semibold">23</span></div>
                <div class="prog" style="height:6px;border-radius:5px;background:var(--ink-100);overflow:hidden;"><span style="display:block;height:100%;width:46%;background:var(--gold-500);"></span></div>
              </div>
              <div>
                <div class="d-flex justify-content-between small mb-1"><span>Approve data</span><span class="text-mono fw-semibold">86</span></div>
                <div class="prog" style="height:6px;border-radius:5px;background:var(--ink-100);overflow:hidden;"><span style="display:block;height:100%;width:70%;background:var(--f-plan);"></span></div>
              </div>
              <div>
                <div class="d-flex justify-content-between small mb-1"><span>Archive &amp; backup</span><span class="text-mono fw-semibold">19</span></div>
                <div class="prog" style="height:6px;border-radius:5px;background:var(--ink-100);overflow:hidden;"><span style="display:block;height:100%;width:30%;background:var(--f-others);"></span></div>
              </div>
            </div>
            <hr class="my-3">
            <a href="#" class="btn btn-outline-navy w-100"><i class="bi bi-diagram-3 me-1"></i>Open process flow</a>
          </div>
        </div>
      </div>

      <!-- District snapshot -->
      <div class="surface p-3 p-lg-4 mt-3">
        <h2 class="h6 fw-bold mb-3">Applications by district</h2>
        <div class="row g-3 text-center">
          <div class="col-6 col-md-3"><div class="p-3 rounded-3" style="background:var(--paper);"><div class="fw-bold fs-5" style="font-family:'Sora';">34</div><div class="small text-muted">Salem</div></div></div>
          <div class="col-6 col-md-3"><div class="p-3 rounded-3" style="background:var(--paper);"><div class="fw-bold fs-5" style="font-family:'Sora';">28</div><div class="small text-muted">Coimbatore</div></div></div>
          <div class="col-6 col-md-3"><div class="p-3 rounded-3" style="background:var(--paper);"><div class="fw-bold fs-5" style="font-family:'Sora';">21</div><div class="small text-muted">Namakkal</div></div></div>
          <div class="col-6 col-md-3"><div class="p-3 rounded-3" style="background:var(--paper);"><div class="fw-bold fs-5" style="font-family:'Sora';">17</div><div class="small text-muted">Erode</div></div></div>
        </div>
      </div>
    </main>
            </div>
        </div>


@endsection
