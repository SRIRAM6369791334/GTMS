<!-- ==========================================================================
     GTMS ROLES & RBAC MODALS (UI/UX PRO MAX - ENHANCED ALIGNMENT & ERGONOMICS)
     ========================================================================== -->
<style>
.ro-modal-content {
    background: var(--ro-surface) !important;
    border: 1px solid var(--ro-border) !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}

.ro-modal-header {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%) !important;
    color: #ffffff !important;
    padding: 1.15rem 1.5rem !important;
    border-bottom: none !important;
}

body[data-theme-version="dark"] .ro-modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
}

.ro-modal-title {
    font-size: 1.15rem !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.ro-modal-body {
    background: var(--ro-surface) !important;
    padding: 1.5rem !important;
    color: var(--ro-text-primary) !important;
}

.ro-modal-footer {
    background: var(--ro-surface-subtle) !important;
    border-top: 1px solid var(--ro-border) !important;
    padding: 1rem 1.5rem !important;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Custom Cancel Button (Neutral Theme-Aware - Eliminates Dexignlabs Pink Trap) */
.ro-btn-modal-cancel {
    height: 40px;
    padding: 0 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 9px;
    border: 1px solid var(--ro-border);
    background: var(--ro-surface);
    color: var(--ro-text-secondary) !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.ro-btn-modal-cancel:hover {
    background: var(--ro-surface-subtle);
    color: var(--ro-text-primary) !important;
    border-color: var(--ro-text-muted);
}

body[data-theme-version="dark"] .ro-btn-modal-cancel {
    background: #242424;
    border-color: #383838;
    color: #cbd5e1 !important;
}

body[data-theme-version="dark"] .ro-btn-modal-cancel:hover {
    background: #2a2a2a;
    color: #ffffff !important;
    border-color: #4b5563;
}

/* Role Name Input */
.ro-input-group {
    display: flex;
    align-items: stretch;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--ro-input-border);
    transition: all 0.15s ease;
    background: var(--ro-input-bg);
}

.ro-input-group:focus-within {
    border-color: var(--ro-accent);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.ro-input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 0.95rem;
    background: var(--ro-surface-subtle);
    color: var(--ro-text-muted);
    border-right: 1px solid var(--ro-border);
    font-size: 0.95rem;
}

.ro-form-input {
    flex: 1;
    border: none !important;
    padding: 0.65rem 1rem !important;
    font-size: 0.9rem !important;
    background: transparent !important;
    color: var(--ro-text-primary) !important;
    outline: none !important;
}

.ro-form-label {
    font-size: 0.825rem;
    font-weight: 600;
    color: var(--ro-text-secondary);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Module Permission Card Styling */
.ro-perm-module-card {
    background: var(--ro-surface-subtle) !important;
    border: 1px solid var(--ro-border) !important;
    border-radius: 12px !important;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.ro-perm-module-card:hover {
    border-color: rgba(59, 130, 246, 0.35) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.ro-perm-module-header {
    background: var(--ro-surface-elevated) !important;
    border-bottom: 1px solid var(--ro-border) !important;
    padding: 0.75rem 1rem !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ro-perm-module-title {
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    color: var(--ro-accent) !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ro-perm-module-body {
    padding: 0.75rem 0.85rem !important;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

/* ==========================================================================
   PERFECT CHECKBOX ROW ALIGNMENT (ELIMINATES BOOTSTRAP OVERFLOW)
   ========================================================================== */
.ro-perm-tile {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    padding: 0.45rem 0.75rem !important;
    border-radius: 8px !important;
    background: var(--ro-surface) !important;
    border: 1px solid var(--ro-border-subtle) !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    position: relative;
}

body[data-theme-version="dark"] .ro-perm-tile {
    background: var(--ro-surface-elevated) !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
}

.ro-perm-tile:hover {
    background: var(--ro-surface-subtle) !important;
    border-color: var(--ro-accent) !important;
    transform: translateX(2px);
}

body[data-theme-version="dark"] .ro-perm-tile:hover {
    background: #2a2a2a !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
}

/* Neutralize and strictly align checkbox input */
.ro-perm-tile .form-check-input {
    float: none !important;
    margin: 0 !important;
    position: static !important;
    width: 18px !important;
    height: 18px !important;
    flex-shrink: 0 !important;
    cursor: pointer !important;
    border-radius: 5px !important;
    border: 1.5px solid var(--ro-input-border) !important;
    background-color: var(--ro-input-bg) !important;
    box-shadow: none !important;
}

.ro-perm-tile .form-check-input:checked {
    background-color: var(--ro-accent) !important;
    border-color: var(--ro-accent) !important;
}

.ro-perm-tile-label {
    margin: 0 !important;
    font-size: 0.825rem !important;
    color: var(--ro-text-primary) !important;
    cursor: pointer !important;
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 0.5rem !important;
    user-select: none !important;
    line-height: 1.3 !important;
}

.ro-perm-name-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ro-text-primary);
    letter-spacing: -0.01em;
}

/* Action Tag Pills */
.ro-perm-action-tag {
    font-size: 0.675rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 5px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
}

.tag-create { background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); }
.tag-edit   { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); }
.tag-delete { background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }
.tag-view   { background: rgba(37, 99, 235, 0.12); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.25); }
.tag-upload { background: rgba(14, 165, 233, 0.12); color: #0ea5e9; border: 1px solid rgba(14, 165, 233, 0.25); }
.tag-other  { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.25); }

/* In-Modal Filter Input */
.ro-perm-filter-wrap {
    position: relative;
    margin-bottom: 1rem;
}

.ro-perm-filter-input {
    width: 100%;
    height: 38px;
    padding: 0.4rem 1rem 0.4rem 2.25rem;
    font-size: 0.84rem;
    border-radius: 9px;
    border: 1px solid var(--ro-input-border);
    background: var(--ro-input-bg);
    color: var(--ro-text-primary);
    outline: none;
    transition: all 0.15s ease;
}

.ro-perm-filter-input:focus {
    border-color: var(--ro-accent);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.ro-perm-filter-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ro-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

/* Quick Select Buttons */
.ro-btn-xs-outline {
    font-size: 0.775rem;
    font-weight: 600;
    padding: 0.3rem 0.75rem;
    border-radius: 7px;
    border: 1px solid var(--ro-border);
    background: var(--ro-surface);
    color: var(--ro-text-secondary);
    transition: all 0.15s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.ro-btn-xs-outline:hover {
    background: var(--ro-accent);
    color: #ffffff !important;
    border-color: var(--ro-accent);
}

body[data-theme-version="dark"] .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>

<!-- ==========================================================================
     ADD ROLE MODAL
     ========================================================================== -->
@can('roles.create')
<div class="modal fade bd-example-modal-lg" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="rolesadd" class="modal-content ro-modal-content">
            @csrf
            <div class="modal-header ro-modal-header">
                <h5 class="modal-title ro-modal-title">
                    <i class="fa fa-shield-alt"></i> Add New Security Role & Assign Permissions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ro-modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <!-- Role Name Input -->
                <div class="mb-4">
                    <label class="ro-form-label">
                        <i class="fa fa-id-badge text-primary"></i> Role Name <span class="text-danger">*</span>
                    </label>
                    <div class="ro-input-group">
                        <div class="ro-input-icon"><i class="fa fa-user-shield"></i></div>
                        <input type="text" class="ro-form-input" name="name" placeholder="e.g. Operations Manager, Mining Inspector, Auditor" required>
                    </div>
                    <span class="text-danger name_error error-text mt-1 d-block small"></span>
                </div>

                <!-- Module Permissions Section Header -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: var(--ro-border) !important;">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--ro-text-primary);">
                            <i class="fa fa-key me-1 text-primary"></i> Granular Module Permissions
                        </h6>
                        <small class="text-muted">Select statutory permissions allowed for this role profile</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="ro-btn-xs-outline select-all-add">
                            <i class="fa fa-check-double me-1"></i> Select All
                        </button>
                        <button type="button" class="ro-btn-xs-outline deselect-all-add">
                            <i class="fa fa-times me-1"></i> Deselect All
                        </button>
                    </div>
                </div>

                <!-- Quick Permission Filter Input -->
                <div class="ro-perm-filter-wrap">
                    <i class="fa fa-search ro-perm-filter-icon"></i>
                    <input type="text" class="ro-perm-filter-input perm-modal-filter" placeholder="Filter permissions by module name, keyword (e.g. mining, users, accounts)...">
                </div>

                <!-- Permissions Grid -->
                <div class="row g-3 modal-perm-grid">
                    @foreach ($groupedPermissions as $moduleKey => $permissions)
                        <div class="col-md-6 perm-module-col" data-module="{{ strtolower($modules[$moduleKey] ?? $moduleKey) }}">
                            <div class="ro-perm-module-card">
                                <div class="ro-perm-module-header">
                                    <span class="ro-perm-module-title">
                                        <i class="fa fa-folder-open text-primary"></i>
                                        {{ $modules[$moduleKey] ?? ucfirst($moduleKey) }}
                                    </span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input module-check-all-add" type="checkbox" data-target="module-add-{{ $moduleKey }}" id="chk_add_{{ $moduleKey }}">
                                        <label class="form-check-label small text-muted" for="chk_add_{{ $moduleKey }}">All</label>
                                    </div>
                                </div>
                                <div class="ro-perm-module-body">
                                    @foreach ($permissions as $perm)
                                        @php
                                            $parts = explode('.', $perm->name);
                                            $action = end($parts);
                                            $actionTagClass = match($action) {
                                                'create' => 'tag-create',
                                                'edit' => 'tag-edit',
                                                'delete' => 'tag-delete',
                                                'view' => 'tag-view',
                                                'upload' => 'tag-upload',
                                                default => 'tag-other',
                                            };
                                            $actionIcon = match($action) {
                                                'create' => 'fa-plus',
                                                'edit' => 'fa-pencil',
                                                'delete' => 'fa-trash',
                                                'view' => 'fa-eye',
                                                'upload' => 'fa-cloud-arrow-up',
                                                default => 'fa-key',
                                            };
                                        @endphp
                                        <div class="perm-item-wrapper" data-perm-name="{{ strtolower($perm->name) }}">
                                            <div class="ro-perm-tile">
                                                <input class="form-check-input perm-add-checkbox module-add-{{ $moduleKey }}" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->name }}" 
                                                       id="add_perm_{{ $perm->id }}">
                                                <label class="ro-perm-tile-label" for="add_perm_{{ $perm->id }}">
                                                    <span class="ro-perm-name-code">{{ $perm->name }}</span>
                                                    <span class="ro-perm-action-tag {{ $actionTagClass }}">
                                                        <i class="fa {{ $actionIcon }} me-1"></i> {{ ucfirst($action) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer ro-modal-footer">
                <button type="button" class="ro-btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-ro-primary">
                    <i class="fa fa-save"></i> Save Security Role
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- ==========================================================================
     EDIT ROLE MODAL
     ========================================================================== -->
@can('roles.edit')
<div class="modal fade bd-edit-modal-lg" id="roleeditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="rolesedit" class="modal-content ro-modal-content">
            @csrf
            <input type="hidden" name="id" id="role_id">
            <div class="modal-header ro-modal-header">
                <h5 class="modal-title ro-modal-title">
                    <i class="fa fa-edit"></i> Edit Role & Access Permissions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ro-modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <!-- Role Name Input -->
                <div class="mb-4">
                    <label class="ro-form-label">
                        <i class="fa fa-id-badge text-primary"></i> Role Name <span class="text-danger">*</span>
                    </label>
                    <div class="ro-input-group">
                        <div class="ro-input-icon"><i class="fa fa-user-shield"></i></div>
                        <input type="text" class="ro-form-input" name="name" id="role_name" placeholder="Role Name" required>
                    </div>
                    <span class="text-danger name_error error-text mt-1 d-block small"></span>
                </div>

                <!-- Module Permissions Section Header -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: var(--ro-border) !important;">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--ro-text-primary);">
                            <i class="fa fa-key me-1 text-primary"></i> Granular Module Permissions
                        </h6>
                        <small class="text-muted">Modify permitted statutory actions for this role</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="ro-btn-xs-outline select-all-edit">
                            <i class="fa fa-check-double me-1"></i> Select All
                        </button>
                        <button type="button" class="ro-btn-xs-outline deselect-all-edit">
                            <i class="fa fa-times me-1"></i> Deselect All
                        </button>
                    </div>
                </div>

                <!-- Quick Permission Filter Input -->
                <div class="ro-perm-filter-wrap">
                    <i class="fa fa-search ro-perm-filter-icon"></i>
                    <input type="text" class="ro-perm-filter-input perm-modal-filter-edit" placeholder="Filter permissions by module name, keyword (e.g. mining, users, accounts)...">
                </div>

                <!-- Permissions Grid -->
                <div class="row g-3 modal-perm-grid-edit">
                    @foreach ($groupedPermissions as $moduleKey => $permissions)
                        <div class="col-md-6 perm-module-col-edit" data-module="{{ strtolower($modules[$moduleKey] ?? $moduleKey) }}">
                            <div class="ro-perm-module-card">
                                <div class="ro-perm-module-header">
                                    <span class="ro-perm-module-title">
                                        <i class="fa fa-folder-open text-primary"></i>
                                        {{ $modules[$moduleKey] ?? ucfirst($moduleKey) }}
                                    </span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input module-check-all-edit" type="checkbox" data-target="module-edit-{{ $moduleKey }}" id="chk_edit_{{ $moduleKey }}">
                                        <label class="form-check-label small text-muted" for="chk_edit_{{ $moduleKey }}">All</label>
                                    </div>
                                </div>
                                <div class="ro-perm-module-body">
                                    @foreach ($permissions as $perm)
                                        @php
                                            $parts = explode('.', $perm->name);
                                            $action = end($parts);
                                            $actionTagClass = match($action) {
                                                'create' => 'tag-create',
                                                'edit' => 'tag-edit',
                                                'delete' => 'tag-delete',
                                                'view' => 'tag-view',
                                                'upload' => 'tag-upload',
                                                default => 'tag-other',
                                            };
                                            $actionIcon = match($action) {
                                                'create' => 'fa-plus',
                                                'edit' => 'fa-pencil',
                                                'delete' => 'fa-trash',
                                                'view' => 'fa-eye',
                                                'upload' => 'fa-cloud-arrow-up',
                                                default => 'fa-key',
                                            };
                                        @endphp
                                        <div class="perm-item-wrapper-edit" data-perm-name="{{ strtolower($perm->name) }}">
                                            <div class="ro-perm-tile">
                                                <input class="form-check-input perm-edit-checkbox module-edit-{{ $moduleKey }}" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->name }}" 
                                                       id="edit_perm_{{ $perm->id }}">
                                                <label class="ro-perm-tile-label" for="edit_perm_{{ $perm->id }}">
                                                    <span class="ro-perm-name-code">{{ $perm->name }}</span>
                                                    <span class="ro-perm-action-tag {{ $actionTagClass }}">
                                                        <i class="fa {{ $actionIcon }} me-1"></i> {{ ucfirst($action) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer ro-modal-footer">
                <button type="button" class="ro-btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-ro-primary">
                    <i class="fa fa-save"></i> Update Security Role
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
