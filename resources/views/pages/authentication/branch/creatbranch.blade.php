<style>
/* =========================================================================
   BRANCH MODALS — UI/UX PRO MAX POLISH
   ========================================================================= */
.branch-modal-content {
    border-radius: 18px !important;
    border: 1px solid var(--br-border) !important;
    background: var(--br-surface) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}
body[data-theme-version="dark"] .branch-modal-content {
    background: #202020 !important;
    border-color: #333333 !important;
}

.branch-modal-header {
    background: var(--br-surface-subtle) !important;
    border-bottom: 1px solid var(--br-border) !important;
    padding: 1.15rem 1.5rem;
}
body[data-theme-version="dark"] .branch-modal-header {
    background: #252525 !important;
    border-color: #333333 !important;
}

.branch-modal-icon-badge {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
body[data-theme-version="dark"] .branch-modal-icon-badge {
    background: rgba(59, 130, 246, 0.16);
    color: #60a5fa;
}

.branch-modal-body {
    padding: 1.4rem 1.5rem;
}

.branch-modal-footer {
    background: var(--br-surface-subtle) !important;
    border-top: 1px solid var(--br-border) !important;
    padding: 0.95rem 1.5rem;
}
body[data-theme-version="dark"] .branch-modal-footer {
    background: #252525 !important;
    border-color: #333333 !important;
}

.branch-field-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--br-text-primary);
    margin-bottom: 0.35rem;
}
.branch-field-input {
    border-radius: 9px !important;
    border: 1px solid var(--br-border) !important;
    background: var(--br-surface) !important;
    color: var(--br-text-primary) !important;
    font-size: 0.84rem;
    height: 40px;
}
.branch-field-input:focus {
    border-color: var(--br-accent) !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
body[data-theme-version="dark"] .branch-field-input {
    background: #161616 !important;
    border-color: #333333 !important;
    color: #f8fafc !important;
}

.branch-field-textarea {
    border-radius: 9px !important;
    border: 1px solid var(--br-border) !important;
    background: var(--br-surface) !important;
    color: var(--br-text-primary) !important;
    font-size: 0.84rem;
    line-height: 1.45;
}
.branch-field-textarea:focus {
    border-color: var(--br-accent) !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
body[data-theme-version="dark"] .branch-field-textarea {
    background: #161616 !important;
    border-color: #333333 !important;
    color: #f8fafc !important;
}

.branch-input-addon {
    border-radius: 9px 0 0 9px !important;
    border: 1px solid var(--br-border) !important;
    border-right: none !important;
    background: var(--br-surface-subtle) !important;
    color: var(--br-text-muted) !important;
}
body[data-theme-version="dark"] .branch-input-addon {
    background: #1f1f1f !important;
    border-color: #333333 !important;
    color: #94a3b8 !important;
}

.btn-branch-modal-close {
    height: 38px;
    border-radius: 9px;
    border: 1px solid var(--br-border);
    background: var(--br-surface);
    color: var(--br-text-muted);
    font-weight: 600;
    font-size: 0.82rem;
    padding: 0 1.1rem;
    transition: all 0.16s ease;
}
.btn-branch-modal-close:hover {
    background: var(--br-hover);
    color: var(--br-text-primary);
}
body[data-theme-version="dark"] .btn-branch-modal-close {
    background: #181818;
    border-color: #333333;
    color: #e2e8f0;
}
body[data-theme-version="dark"] .btn-branch-modal-close:hover {
    background: #222222;
}

body[data-theme-version="dark"] .modal-header .btn-close {
    filter: invert(1);
    opacity: 0.7;
}
</style>

<!-- ========================================================================= -->
<!-- MODAL: ADD DEPARTMENT                                                    -->
<!-- ========================================================================= -->
@can('branch.create')
<div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="brancheadd" class="modal-content branch-modal-content">
            @csrf
            <div class="modal-header branch-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="branch-modal-icon-badge">
                        <i class="fa fa-building"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--br-text-primary);">Add New Department / Regional Branch</h5>
                        <div class="text-muted" style="font-size: 0.76rem;">Register a regional office for statutory mining administration</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body branch-modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Department / Branch Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-building text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="branch_name" placeholder="e.g. Tiruchirappalli Regional Office" required>
                        </div>
                        <span class="text-danger error-text branch_name_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Contact Person (In-Charge) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-user-tie text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="contact_person" placeholder="e.g. Er. K. Sundaramoorthy" required>
                        </div>
                        <span class="text-danger error-text contact_person_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Phone / Mobile Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-phone text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="mobile" placeholder="10-15 digit phone number" required maxlength="15">
                        </div>
                        <span class="text-danger error-text mobile_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">City / Town</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-city text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="city" placeholder="e.g. Tiruchirappalli">
                        </div>
                        <span class="text-danger error-text city_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label branch-field-label">Office Street Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-map-pin text-primary"></i></span>
                            <textarea class="form-control branch-field-textarea" name="address" rows="2" placeholder="Full street address, building name, revenue district..." required></textarea>
                        </div>
                        <span class="text-danger error-text address_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">State</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-flag text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="state" placeholder="e.g. Tamil Nadu" value="Tamil Nadu">
                        </div>
                        <span class="text-danger error-text state_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Postal Pincode</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-envelope text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="pincode" placeholder="6-digit pincode" maxlength="6">
                        </div>
                        <span class="text-danger error-text pincode_error fs-12 mt-1 d-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer branch-modal-footer d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-branch-modal-close" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-branch-primary">
                    <i class="fa fa-check-circle me-1"></i> Save Department
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- ========================================================================= -->
<!-- MODAL: EDIT DEPARTMENT                                                    -->
<!-- ========================================================================= -->
@can('branch.edit')
<div class="modal fade" id="brancheeditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="brancheedit" class="modal-content branch-modal-content">
            @csrf
            <input type="hidden" name="id" id="editid">
            <div class="modal-header branch-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="branch-modal-icon-badge" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--br-text-primary);">Edit Department Details</h5>
                        <div class="text-muted" style="font-size: 0.76rem;">Update regional office jurisdiction, contact, or operational status</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body branch-modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Department / Branch Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-building text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="branch_name" id="editbranch_name" required>
                        </div>
                        <span class="text-danger error-text edit_branch_name_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Contact Person (In-Charge) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-user-tie text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="contact_person" id="editcontact_person" required>
                        </div>
                        <span class="text-danger error-text edit_contact_person_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Phone / Mobile Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-phone text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="mobile" id="editmobile" required maxlength="15">
                        </div>
                        <span class="text-danger error-text edit_mobile_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label branch-field-label">Operational Status <span class="text-danger">*</span></label>
                        <select class="form-control branch-field-input" name="status" id="editstatus" required>
                            <option value="1">Active / Operational</option>
                            <option value="0">Inactive / Closed</option>
                        </select>
                        <span class="text-danger error-text edit_status_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label branch-field-label">Office Street Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-map-pin text-primary"></i></span>
                            <textarea class="form-control branch-field-textarea" name="address" id="editaddress" rows="2" required></textarea>
                        </div>
                        <span class="text-danger error-text edit_address_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label branch-field-label">City / Town</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-city text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="city" id="editcity">
                        </div>
                        <span class="text-danger error-text edit_city_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label branch-field-label">State</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-flag text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="state" id="editstate">
                        </div>
                        <span class="text-danger error-text edit_state_error fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label branch-field-label">Postal Pincode</label>
                        <div class="input-group">
                            <span class="input-group-text branch-input-addon"><i class="fa fa-envelope text-primary"></i></span>
                            <input type="text" class="form-control branch-field-input" name="pincode" id="editpincode" maxlength="6">
                        </div>
                        <span class="text-danger error-text edit_pincode_error fs-12 mt-1 d-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer branch-modal-footer d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-branch-modal-close" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-branch-primary">
                    <i class="fa fa-check-circle me-1"></i> Update Department
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
