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
            <div><div class="val">{{ $activeCount }}</div><div class="lbl">Active applications</div><div class="delta text-success">Updated just now</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--warn-bg); color:var(--warn);"><i class="fa fa-hourglass-half"></i></div>
            <div><div class="val">{{ $pendingCount }}</div><div class="lbl">Pending validation</div><div class="delta text-warning-emphasis">Awaiting review</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--ok-bg); color:var(--ok);"><i class="fa fa-check-circle"></i></div>
            <div><div class="val">{{ $approvedCount }}</div><div class="lbl">Approved &amp; verified</div><div class="delta text-success">All time</div></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="surface stat-card">
            <div class="ic" style="background:var(--f-others-bg); color:var(--f-others);"><i class="fa fa-cloud-upload"></i></div>
            <div><div class="val">{{ $archivedCount }}</div><div class="lbl">Archived &amp; backed up</div><div class="delta" style="color:var(--ink-500);">Safely stored</div></div>
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
                  @forelse($recentApplications as $app)
                  <tr>
                    <td><span class="fw-semibold">{{ $app->client }}</span></td>
                    <td>{{ $app->district }}</td>
                    <td>{{ $app->mineral }}</td>
                    <td>{{ $app->plan_type }}</td>
                    <td><span class="chip chip-{{ $app->status_class }}"><i class="bi bi-{{ $app->icon }}"></i> {{ $app->stage }}</span></td>
                    <td class="text-end"><a href="#" class="btn btn-sm btn-outline-navy">Open</a></td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">No recent applications found.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Applications Breakdown Chart -->
        <div class="col-lg-4">
          <div class="surface p-3 p-lg-4 h-100 d-flex flex-column">
            <h2 class="h6 fw-bold mb-3">Applications Breakdown</h2>
            <div class="flex-grow-1" style="position: relative; min-height: 250px;">
                <canvas id="appBreakdownChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- District snapshot -->
      <div class="surface p-3 p-lg-4 mt-3">
        <h2 class="h6 fw-bold mb-3">Applications by district</h2>
        <div class="row g-3 text-center">
          @forelse($districtStats as $stat)
          <div class="col-6 col-md-3">
            <div class="p-3 rounded-3" style="background:var(--paper);">
              <div class="fw-bold fs-5" style="font-family:'Sora';">{{ $stat->total }}</div>
              <div class="small text-muted">{{ $stat->name }}</div>
            </div>
          </div>
          @empty
          <div class="col-12"><div class="p-3 rounded-3 text-muted text-center">No data available</div></div>
          @endforelse
        </div>
      </div>

      <!-- Financial Snapshot -->
      <div class="row g-3 mt-1">
        <div class="col-12 col-lg-6">
          <div class="surface p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="ic me-3" style="background:var(--ok-bg); color:var(--ok); font-size: 1.5rem; width: 48px; height: 48px; display:flex; align-items:center; justify-content:center; border-radius: 8px;"><i class="bi bi-cash-stack"></i></div>
              <div>
                <div class="small text-muted">Total Paid Amount</div>
                <div class="fw-bold fs-4" style="font-family:'Sora';">₹{{ number_format($totalPaid, 2) }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="surface p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="ic me-3" style="background:var(--warn-bg); color:var(--warn); font-size: 1.5rem; width: 48px; height: 48px; display:flex; align-items:center; justify-content:center; border-radius: 8px;"><i class="bi bi-hourglass-split"></i></div>
              <div>
                <div class="small text-muted">Total Pending Amount</div>
                <div class="fw-bold fs-4" style="font-family:'Sora';">₹{{ number_format($totalPending, 2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('appBreakdownChart').getContext('2d');
    var chartData = {!! json_encode($chartData) !!};
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    '#0B2B5E', // navy
                    '#D4AF37', // gold
                    '#2E7D32', // ok
                    '#1976D2', // light blue
                    '#7B1FA2'  // purple
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });
});
</script>

@endsection
