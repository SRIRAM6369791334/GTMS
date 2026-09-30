{{-- Reusable Actionable Empty State Partial (P1.3) --}}
@php
    $icon = $icon ?? 'fas fa-inbox';
    $title = $title ?? 'No Records Found';
    $desc = $desc ?? 'There are no records matching your search or filter parameters.';
    $resetRoute = $resetRoute ?? null;
    $createRoute = $createRoute ?? null;
    $createLabel = $createLabel ?? 'Create New';
    $createPermission = $createPermission ?? null;
    
    // Check if any query filters are active
    $hasFilters = request()->hasAny([
        'search', 'q', 'customer_id', 'status', 'payment_mode',
        'application_type', 'district_id', 'date_from', 'date_to', 'start_date', 'end_date'
    ]);
@endphp

<div class="empty-state-box">
    <div class="empty-state-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <h5 class="empty-state-title">{{ $title }}</h5>
    <p class="empty-state-desc">
        @if($hasFilters)
            No records matched your active filter settings. Try adjusting your search query, date range, or removing selected filters.
        @else
            {{ $desc }}
        @endif
    </p>

    <div class="d-flex justify-content-center gap-2 flex-wrap">
        @if($hasFilters && $resetRoute)
            <a href="{{ $resetRoute }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-redo me-1"></i> Clear All Filters
            </a>
        @endif

        @if($createRoute && (!$createPermission || auth()->user()?->can($createPermission)))
            <a href="{{ $createRoute }}" class="btn btn-navy btn-sm px-3">
                <i class="fas fa-plus me-1"></i> {{ $createLabel }}
            </a>
        @endif
    </div>
</div>
