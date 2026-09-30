@php
    $activeChips = [];

    // 1. Text Search
    $searchVal = trim((string) (request('search') ?: request('q')));
    if ($searchVal !== '') {
        $activeChips[] = [
            'label' => 'Search: "' . (strlen($searchVal) > 20 ? substr($searchVal, 0, 18) . '...' : $searchVal) . '"',
            'keys'  => ['search', 'q'],
        ];
    }

    // 2. Customer Selection
    if (request()->filled('customer_id')) {
        $cid = request('customer_id');
        $custName = '#' . $cid;
        if (isset($customers) && $customers instanceof \Illuminate\Support\Collection) {
            $matched = $customers->firstWhere('id', $cid);
            if ($matched) {
                $custName = $matched->company_name ?: $matched->customer_name;
            }
        }
        $activeChips[] = [
            'label' => 'Client: ' . (strlen($custName) > 25 ? substr($custName, 0, 23) . '...' : $custName),
            'keys'  => ['customer_id'],
        ];
    }

    // 3. Status Filter
    if (request()->filled('status')) {
        $statusLabels = [
            'draft'     => 'Draft',
            'sent'      => 'Sent to Client',
            'accepted'  => 'Accepted',
            'converted' => 'Converted',
            'rejected'  => 'Rejected',
        ];
        $st = request('status');
        $activeChips[] = [
            'label' => 'Status: ' . ($statusLabels[$st] ?? ucfirst($st)),
            'keys'  => ['status'],
        ];
    }

    // 4. Payment Mode
    if (request()->filled('payment_mode')) {
        $activeChips[] = [
            'label' => 'Mode: ' . request('payment_mode'),
            'keys'  => ['payment_mode'],
        ];
    }

    // 5. Statutory Application Type
    if (request()->filled('application_type')) {
        $activeChips[] = [
            'label' => 'Module: ' . ucfirst(str_replace('_', ' ', request('application_type'))),
            'keys'  => ['application_type'],
        ];
    }

    // 6. District Filter
    if (request()->filled('district_id')) {
        $did = request('district_id');
        $distName = '#' . $did;
        if (isset($districts) && $districts instanceof \Illuminate\Support\Collection) {
            $matched = $districts->firstWhere('id', $did);
            if ($matched) {
                $distName = $matched->name ?? $matched->district_name;
            }
        }
        $activeChips[] = [
            'label' => 'District: ' . $distName,
            'keys'  => ['district_id'],
        ];
    }

    // 7. From Date
    $fromDateVal = request('date_from') ?: request('start_date');
    if ($fromDateVal) {
        $activeChips[] = [
            'label' => 'From: ' . date('d-M-Y', strtotime($fromDateVal)),
            'keys'  => ['date_from', 'start_date'],
        ];
    }

    // 8. To Date
    $toDateVal = request('date_to') ?: request('end_date');
    if ($toDateVal) {
        $activeChips[] = [
            'label' => 'To: ' . date('d-M-Y', strtotime($toDateVal)),
            'keys'  => ['date_to', 'end_date'],
        ];
    }

    $targetRoute = $route ?? Route::currentRouteName() ?? 'accounts.quotations.index';
@endphp

@if(count($activeChips) > 0)
    <div class="active-filter-bar">
        <span class="active-filter-label">
            <i class="fas fa-filter text-primary me-1" aria-hidden="true"></i> Active Filters:
        </span>
        @foreach($activeChips as $chip)
            @php
                $dropKeys = array_merge((array) $chip['keys'], ['page']);
                $removeUrl = route($targetRoute, request()->except($dropKeys));
            @endphp
            <span class="active-filter-chip">
                <span>{{ $chip['label'] }}</span>
                <a href="{{ $removeUrl }}" 
                   class="chip-remove-btn" 
                   aria-label="Remove {{ $chip['label'] }} filter" 
                   title="Remove this filter">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </a>
            </span>
        @endforeach
        <a href="{{ route($targetRoute) }}" class="btn-clear-filters" aria-label="Clear all active filters" title="Reset all filters">
            <i class="fas fa-redo-alt me-1" aria-hidden="true"></i> Reset All
        </a>
    </div>
@endif
