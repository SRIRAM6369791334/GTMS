<!-- DYNAMIC BANK ACCOUNT REMITTANCE SELECTOR (INSIDE QUOTATION FORM) -->
<style>
    .bank-account-option-card {
        border: 1.5px solid #CBD5E1;
        background: #FFFFFF;
        border-radius: 8px;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    .bank-account-option-card:hover {
        border-color: #94A3B8;
        background: #F8FAFC;
    }
    .bank-account-option-card.selected-bank-card {
        border-color: #1E3A8A !important;
        background: #EFF6FF !important;
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.15);
    }
    .bank-account-radio {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }
</style>

@php
    $selectedBankId = old('bank_account_id', isset($quotation) && $quotation->bank_account_id ? $quotation->bank_account_id : ($bankAccounts->first()?->id ?? null));
@endphp

<div class="p-3 rounded border mb-3" style="background-color: #F8FAFC; border-color: #CBD5E1 !important;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="fw-bold text-dark small d-flex align-items-center gap-2">
            <i class="fas fa-university text-primary fs-6"></i>
            <span>Official Remittance Bank Account (Printed on Page 2)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-muted border small d-none d-sm-inline">Select account to print</span>
            <button type="button" class="btn btn-sm btn-primary fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" id="btnOpenBankModal">
                <i class="fas fa-plus-circle"></i> Add Bank Account
            </button>
        </div>
    </div>

    <!-- Selectable Bank Cards Grid -->
    <div class="row g-2" id="bankAccountsListRow">
        @forelse($bankAccounts as $acc)
            @php $isSelected = ($selectedBankId == $acc->id); @endphp
            <div class="col-md-6 bank-account-card-col" id="bank_card_col_{{ $acc->id }}" data-acc-num="{{ preg_replace('/\s+/', '', $acc->account_number) }}">
                <label class="bank-account-option-card d-flex align-items-start gap-3 p-3 w-100 mb-0 {{ $isSelected ? 'selected-bank-card' : '' }}" for="bank_acc_{{ $acc->id }}">
                    <div class="form-check mt-1 mb-0 ps-0">
                        <input class="form-check-input bank-account-radio ms-0" type="radio" name="bank_account_id" id="bank_acc_{{ $acc->id }}" value="{{ $acc->id }}" {{ $isSelected ? 'checked' : '' }}>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark bank-card-name" style="font-size: 0.88rem;">{{ $acc->bank_name }}</span>
                            @if($acc->is_default)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-bold" style="font-size: 0.68rem;">Default</span>
                            @endif
                        </div>
                        <div class="small text-muted mb-1" style="font-size: 0.78rem;">
                            <strong>A/C Name:</strong> {{ $acc->account_name }}
                        </div>
                        <div class="d-flex flex-wrap gap-2 text-dark font-monospace fw-bold" style="font-size: 0.82rem;">
                            <span>A/C: {{ $acc->account_number }}</span>
                            <span class="text-secondary">&bull;</span>
                            <span class="text-primary">IFSC: {{ $acc->ifsc_code }}</span>
                        </div>
                        @if($acc->branch_name)
                            <div class="small text-secondary mt-1" style="font-size: 0.74rem;">
                                Branch: {{ $acc->branch_name }}
                            </div>
                        @endif
                    </div>
                </label>
            </div>
        @empty
            <div class="col-12" id="emptyBankAccountsNotice">
                <div class="alert alert-warning mb-0 small">
                    <i class="fas fa-exclamation-triangle me-1"></i> No bank accounts configured yet. Please click <strong>Add Bank Account</strong> above.
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Radio Card visual highlighting and radio selection
    function bindCardEvents() {
        document.querySelectorAll('.bank-account-option-card').forEach(card => {
            card.onclick = function () {
                document.querySelectorAll('.bank-account-option-card').forEach(c => c.classList.remove('selected-bank-card'));
                this.classList.add('selected-bank-card');
                const radio = this.querySelector('.bank-account-radio');
                if (radio) {
                    radio.checked = true;
                }
            };
        });
    }
    bindCardEvents();
});
</script>
