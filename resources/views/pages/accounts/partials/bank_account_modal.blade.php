<!-- ADD NEW BANK ACCOUNT PROFILE MODAL (STANDALONE OUTSIDE QUOTATION FORM) -->
<div class="modal fade" id="addBankAccountModal" tabindex="-1" aria-labelledby="addBankAccountModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3 px-4" style="background: #0F1E4D; color: #ffffff;">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2 mb-0" id="addBankAccountModalLabel">
                    <i class="fas fa-university text-info"></i> Add New Bank Account Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBankAccountForm" autocomplete="off">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="alert alert-danger py-2 px-3 small d-none" id="bankModalAlert"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Beneficiary / Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" id="modal_acc_name" class="form-control" value="Geo Technical Mining Solutions" placeholder="e.g. Geo Technical Mining Solutions" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-dark">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="modal_bank_name" class="form-control" placeholder="e.g. State Bank of India" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-dark">IFSC Code <span class="text-danger">*</span></label>
                            <input type="text" name="ifsc_code" id="modal_ifsc_code" class="form-control text-uppercase font-monospace" placeholder="e.g. SBIN0001234" maxlength="20" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="modal_acc_num" class="form-control font-monospace" placeholder="Enter bank account number" required>
                        <div class="form-text small text-muted">Must be unique; duplicate accounts cannot be saved.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark">Branch / Location (Optional)</label>
                        <input type="text" name="branch_name" id="modal_branch_name" class="form-control" placeholder="e.g. Dharmapuri Main Branch">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold d-inline-flex align-items-center gap-1" id="btnSubmitBank">
                        <i class="fas fa-check-circle"></i> Save &amp; Select Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bankListRow = document.getElementById('bankAccountsListRow');
    const openModalBtn = document.getElementById('btnOpenBankModal');
    const modalEl = document.getElementById('addBankAccountModal');
    const formEl = document.getElementById('addBankAccountForm');
    const alertEl = document.getElementById('bankModalAlert');
    const submitBtn = document.getElementById('btnSubmitBank');

    // Function to rebind radio card selection clicks
    function bindCardEvents() {
        document.querySelectorAll('.bank-account-option-card').forEach(card => {
            card.onclick = function (e) {
                document.querySelectorAll('.bank-account-option-card').forEach(c => c.classList.remove('selected-bank-card'));
                this.classList.add('selected-bank-card');
                const radio = this.querySelector('.bank-account-radio');
                if (radio) {
                    radio.checked = true;
                }
            };
        });
    }

    // Open Modal
    if (openModalBtn && modalEl) {
        openModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (alertEl) {
                alertEl.classList.add('d-none');
                alertEl.textContent = '';
            }
            if (formEl) {
                formEl.reset();
                const accInput = document.getElementById('modal_acc_name');
                if (accInput) accInput.value = 'Geo Technical Mining Solutions';
            }
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else if (typeof $ !== 'undefined') {
                $(modalEl).modal('show');
            }
        });
    }

    // Submit New Bank Account with Duplicate Account Number Validation
    if (formEl) {
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            alertEl.classList.add('d-none');
            alertEl.textContent = '';

            const accName = document.getElementById('modal_acc_name').value.trim();
            const bankName = document.getElementById('modal_bank_name').value.trim();
            const ifscCode = document.getElementById('modal_ifsc_code').value.trim().toUpperCase();
            const accNum = document.getElementById('modal_acc_num').value.trim().replace(/\s+/g, '');
            const branchName = document.getElementById('modal_branch_name').value.trim();

            if (!accName || !bankName || !ifscCode || !accNum) {
                alertEl.textContent = 'Please fill all required fields.';
                alertEl.classList.remove('d-none');
                return;
            }

            // Client-side instant duplicate check across existing rendered accounts
            const existingCard = document.querySelector(`.bank-account-card-col[data-acc-num="${accNum}"]`);
            if (existingCard) {
                alertEl.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> <strong>Duplicate Account!</strong> Account Number <code>${accNum}</code> is already in your selection list. Duplicate accounts cannot be created.`;
                alertEl.classList.remove('d-none');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            fetch("{{ route('accounts.bank-accounts.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    account_name: accName,
                    bank_name: bankName,
                    ifsc_code: ifscCode,
                    account_number: accNum,
                    branch_name: branchName
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Save &amp; Select Account';

                if (status === 422 || !body.success) {
                    alertEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${body.message || 'Validation error occurred.'}`;
                    alertEl.classList.remove('d-none');
                    return;
                }

                // Remove empty notice if present
                const emptyNotice = document.getElementById('emptyBankAccountsNotice');
                if (emptyNotice) emptyNotice.remove();

                const acc = body.bank_account;
                const cleanNum = (acc.account_number || '').replace(/\s+/g, '');

                // Create new card and prepend to list
                const col = document.createElement('div');
                col.className = 'col-md-6 bank-account-card-col';
                col.id = `bank_card_col_${acc.id}`;
                col.setAttribute('data-acc-num', cleanNum);

                col.innerHTML = `
                    <label class="bank-account-option-card d-flex align-items-start gap-3 p-3 w-100 mb-0 selected-bank-card" for="bank_acc_${acc.id}">
                        <div class="form-check mt-1 mb-0 ps-0">
                            <input class="form-check-input bank-account-radio ms-0" type="radio" name="bank_account_id" id="bank_acc_${acc.id}" value="${acc.id}" checked>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark bank-card-name" style="font-size: 0.88rem;">${acc.bank_name}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle small fw-bold" style="font-size: 0.68rem;">New</span>
                            </div>
                            <div class="small text-muted mb-1" style="font-size: 0.78rem;">
                                <strong>A/C Name:</strong> ${acc.account_name}
                            </div>
                            <div class="d-flex flex-wrap gap-2 text-dark font-monospace fw-bold" style="font-size: 0.82rem;">
                                <span>A/C: ${acc.account_number}</span>
                                <span class="text-secondary">&bull;</span>
                                <span class="text-primary">IFSC: ${acc.ifsc_code}</span>
                            </div>
                            ${acc.branch_name ? `<div class="small text-secondary mt-1" style="font-size: 0.74rem;">Branch: ${acc.branch_name}</div>` : ''}
                        </div>
                    </label>
                `;

                // Unselect other cards
                document.querySelectorAll('.bank-account-option-card').forEach(c => c.classList.remove('selected-bank-card'));
                bankListRow.prepend(col);
                bindCardEvents();

                // Close modal
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $ !== 'undefined') {
                    $(modalEl).modal('hide');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Save &amp; Select Account';
                alertEl.textContent = 'Server error occurred while adding bank account. Please check network.';
                alertEl.classList.remove('d-none');
            });
        });
    }
});
</script>
