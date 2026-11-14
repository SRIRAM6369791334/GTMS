{{--
  Fee waiver note for a FREE (fee waived) lease application.
  Shown in Step 8 review, the dossier (viewapplication) and the PDF report.
  Inline styles on purpose: the PDF/print view does not load the app CSS.

  @param float|null       $waivedAmount  quoted value that was waived
  @param string|null      $waiverReason  why the fee was waived
  @param string|null      $waivedByName  user who waived it
  @param \Carbon\Carbon|string|null $waivedAt
--}}
@php
  $wAmt = (float)($waivedAmount ?? 0);
  $wAt = !empty($waivedAt) ? \Illuminate\Support\Carbon::parse($waivedAt) : null;
@endphp
<div class="gtms-fee-waiver" role="note"
     style="display:flex;gap:12px;align-items:flex-start;padding:14px 16px;margin:14px 0 0;border-radius:12px;background:#F5F3FF;border:1px solid #DDD6FE;border-left:4px solid #6D28D9;color:#1E1B4B;font-size:13px;line-height:1.55;page-break-inside:avoid;">
  <div aria-hidden="true"
       style="width:34px;height:34px;flex-shrink:0;border-radius:10px;background:#6D28D9;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;">
    <i class="bi bi-gift-fill"></i>
  </div>
  <div style="min-width:0;flex:1;">
    <div style="font-weight:700;color:#5B21B6;font-size:14px;">
      Free of charge — application fee waived
      @if($wAmt > 0)
        <span style="font-weight:600;color:#6D28D9;"> · ₹ {{ number_format($wAmt, 2) }} waived</span>
      @endif
    </div>
    <div style="margin-top:4px;color:#312E81;overflow-wrap:anywhere;">
      <strong>Reason:</strong> {{ $waiverReason ?: 'Not recorded' }}
    </div>
    @if(!empty($waivedByName) || $wAt)
      <div style="margin-top:4px;font-size:12px;color:#6B7280;">
        Waived
        @if(!empty($waivedByName)) by <strong style="color:#4B5563;">{{ $waivedByName }}</strong>@endif
        @if($wAt) on {{ $wAt->format('d M Y, h:i A') }}@endif
        · No amount is due or invoiced.
      </div>
    @endif
  </div>
</div>
