<!-- ==========================================================================
     GTMS USER MANAGEMENT MODALS (UI/UX PRO MAX - ADD & EDIT USER)
     ========================================================================== -->
<style>
.usr-modal-content {
    background: var(--usr-surface) !important;
    border: 1px solid var(--usr-border) !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}

.usr-modal-header {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%) !important;
    color: #ffffff !important;
    padding: 1.15rem 1.5rem !important;
    border-bottom: none !important;
}

body[data-theme-version="dark"] .usr-modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
}

.usr-modal-title {
    font-size: 1.15rem !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.usr-modal-body {
    background: var(--usr-surface) !important;
    padding: 1.5rem !important;
    color: var(--usr-text-primary) !important;
}

.usr-modal-footer {
    background: var(--usr-surface-subtle) !important;
    border-top: 1px solid var(--usr-border) !important;
    padding: 1rem 1.5rem !important;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Custom Cancel Button (Neutral Theme-Aware - Eliminates Dexignlabs Pink Trap) */
.usr-btn-modal-cancel {
    height: 40px;
    padding: 0 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 9px;
    border: 1px solid var(--usr-border);
    background: var(--usr-surface);
    color: var(--usr-text-secondary) !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}

.usr-btn-modal-cancel:hover {
    background: var(--usr-surface-subtle);
    border-color: var(--usr-text-muted);
    color: var(--usr-text-primary) !important;
}

/* Modal Form Groups & Floating/Icon Ergonomics */
.usr-input-group {
    position: relative;
    display: flex;
    align-items: stretch;
    width: 100%;
}

.usr-input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    background: var(--usr-surface-subtle);
    border: 1px solid var(--usr-input-border);
    border-right: none;
    border-radius: 9px 0 0 9px;
    color: var(--usr-text-muted);
    font-size: 0.875rem;
}

.usr-input-group .form-control,
.usr-input-group .form-select {
    border-radius: 0 9px 9px 0 !important;
    border-left: 1px solid var(--usr-input-border) !important;
    height: 42px;
    font-size: 0.875rem;
    background: var(--usr-input-bg) !important;
    color: var(--usr-text-primary) !important;
    border-color: var(--usr-input-border) !important;
}

.usr-input-group .form-control:focus,
.usr-input-group .form-select:focus {
    border-color: var(--usr-accent) !important;
    box-shadow: 0 0 0 3px var(--usr-accent-subtle) !important;
}

.usr-form-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--usr-text-primary);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Password Toggle Icon */
.usr-password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--usr-text-muted);
    cursor: pointer;
    font-size: 0.875rem;
    z-index: 5;
    padding: 4px;
}

.usr-password-toggle:hover {
    color: var(--usr-text-primary);
}

/* Avatar Preview Container */
.usr-avatar-preview-box {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
    background: var(--usr-surface-subtle);
    border: 1px dashed var(--usr-border);
    border-radius: 12px;
}

.usr-avatar-preview-img {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid var(--usr-surface);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

{{-- ==========================================================================
     1. ADD USER MODAL
     ========================================================================== --}}
@can('users.create')
<div class="modal fade bd-user-modal-lg" tabindex="-1" id="userModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="useradd" class="modal-content usr-modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header usr-modal-header">
                <h5 class="modal-title usr-modal-title">
                    <i class="fa fa-user-plus"></i> Add New Personnel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body usr-modal-body">
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" name="name" placeholder="e.g. John Doe" required>
                        </div>
                        <span class="text-danger name_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Corporate Email <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="name@gtms.com" required>
                        </div>
                        <span class="text-danger email_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Role -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Assigned Role <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-shield-alt"></i></span>
                            <select class="form-select form-control" name="role_id" required>
                                <option value="">Select Security Role</option>
                                @foreach ($role as $roles)
                                    <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="text-danger role_id_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Branch / Department -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Branch / Department <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-building"></i></span>
                            <select class="form-select form-control" name="branch_id" required>
                                <option value="">Select Branch Office</option>
                                @foreach ($branch as $branches)
                                    <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="text-danger branch_id_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Contact Number</label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-phone"></i></span>
                            <input type="text" class="form-control" name="mobile_num" placeholder="10-15 digit phone number">
                        </div>
                        <span class="text-danger mobile_num_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Password with Toggle -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Password <span class="text-danger">*</span></label>
                        <div class="usr-input-group position-relative">
                            <span class="usr-input-icon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="add_password" placeholder="Min 6 characters" required>
                            <button type="button" class="usr-password-toggle" data-target="#add_password" title="Toggle visibility">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <span class="text-danger password_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Profile Image -->
                    <div class="col-md-12">
                        <label class="usr-form-label">Profile Image (Optional)</label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-image"></i></span>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <span class="text-danger image_error error-text fs-12 mt-1 d-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer usr-modal-footer">
                <button type="button" class="usr-btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="usr-btn-primary" id="btn_save_user">
                    <i class="fa fa-check"></i> Save User
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ==========================================================================
     2. EDIT USER MODAL
     ========================================================================== --}}
@can('users.edit')
<div class="modal fade bd-useredit-modal-lg" tabindex="-1" id="usereditModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="useredit" class="modal-content usr-modal-content" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="editid">
            <div class="modal-header usr-modal-header">
                <h5 class="modal-title usr-modal-title">
                    <i class="fa fa-user-edit"></i> Edit Personnel Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body usr-modal-body">
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" name="name" id="editname" placeholder="User Name" required>
                        </div>
                        <span class="text-danger edit_name_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Corporate Email <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" id="editemail" placeholder="Email Address" required>
                        </div>
                        <span class="text-danger edit_email_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Role -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Assigned Role <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-shield-alt"></i></span>
                            <select class="form-select form-control" name="role_id" id="editrole" required>
                                <option value="">Select Role</option>
                                @foreach ($role as $roles)
                                    <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="text-danger edit_role_id_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Branch / Department -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Branch / Department <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-building"></i></span>
                            <select class="form-select form-control" name="branch_id" id="editbranch" required>
                                <option value="">Select Branch</option>
                                @foreach ($branch as $branches)
                                    <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="text-danger edit_branch_id_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Contact Number</label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-phone"></i></span>
                            <input type="text" class="form-control" name="mobile_num" id="editmobile_num" placeholder="Phone Number">
                        </div>
                        <span class="text-danger edit_mobile_num_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Account Status <span class="text-danger">*</span></label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-toggle-on"></i></span>
                            <select class="form-select form-control" name="status" id="editstatus" required>
                                <option value="1">Active (Normal Access)</option>
                                <option value="0">Inactive (Suspended)</option>
                            </select>
                        </div>
                        <span class="text-danger edit_status_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Password with Toggle -->
                    <div class="col-md-6">
                        <label class="usr-form-label">
                            <span>Password</span>
                            <small class="text-muted fw-normal">(Leave blank to keep current)</small>
                        </label>
                        <div class="usr-input-group position-relative">
                            <span class="usr-input-icon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="editpassword" placeholder="Enter new password">
                            <button type="button" class="usr-password-toggle" data-target="#editpassword" title="Toggle visibility">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <span class="text-danger edit_password_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <!-- Profile Image Upload & Current Avatar Preview -->
                    <div class="col-md-6">
                        <label class="usr-form-label">Replace Avatar</label>
                        <div class="usr-input-group">
                            <span class="usr-input-icon"><i class="fa fa-image"></i></span>
                            <input type="file" class="form-control" name="image" id="editimage" accept="image/*">
                        </div>
                        <span class="text-danger edit_image_error error-text fs-12 mt-1 d-block"></span>
                    </div>

                    <div class="col-md-12">
                        <div class="usr-avatar-preview-box">
                            <img width="54" height="54" class="usr-avatar-preview-img" src="{{ asset('images/user.jpg') }}" alt="User Avatar" id="profileImage">
                            <div>
                                <div class="fw-bold" style="font-size: 0.85rem; color: var(--usr-text-primary);">Current Profile Picture</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Supported formats: JPEG, PNG, WEBP (Max 2MB)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer usr-modal-footer">
                <button type="button" class="usr-btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="usr-btn-primary" id="btn_update_user">
                    <i class="fa fa-check"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
