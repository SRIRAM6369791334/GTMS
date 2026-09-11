<!-- Add Role Modal -->
@can('roles.create')
<div class="modal fade bd-example-modal-lg" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="rolesadd" class="modal-content shadow-lg border-0">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="fa fa-shield-alt me-2"></i>Add New Role & Assign Permissions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <div class="mb-4">
                    <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="e.g. Manager, Inspector, Staff" required>
                    <span class="text-danger name_error error-text"></span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-key me-1"></i> Module Permissions</h6>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-xs select-all-add">Select All</button>
                        <button type="button" class="btn btn-outline-secondary btn-xs deselect-all-add ms-1">Deselect All</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($groupedPermissions as $moduleKey => $permissions)
                        <div class="col-md-6">
                            <div class="card border mb-0 shadow-none h-100" style="background-color: #fcfdfe;">
                                <div class="card-header py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary" style="font-size: 0.88rem;">
                                        {{ $modules[$moduleKey] ?? ucfirst($moduleKey) }}
                                    </span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input module-check-all-add" type="checkbox" data-target="module-add-{{ $moduleKey }}" id="chk_add_{{ $moduleKey }}">
                                        <label class="form-check-label small text-muted" for="chk_add_{{ $moduleKey }}">All</label>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        @foreach ($permissions as $perm)
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input perm-add-checkbox module-add-{{ $moduleKey }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->name }}" 
                                                           id="add_perm_{{ $perm->id }}">
                                                    <label class="form-check-label text-dark" for="add_perm_{{ $perm->id }}" style="font-size: 0.82rem; cursor: pointer;">
                                                        {{ $perm->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save Role</button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- Edit Role Modal -->
@can('roles.edit')
<div class="modal fade bd-edit-modal-lg" id="roleeditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="rolesedit" class="modal-content shadow-lg border-0">
            @csrf
            <input type="hidden" name="id" id="role_id">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="fa fa-edit me-2"></i>Edit Role & Permissions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <div class="mb-4">
                    <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="role_name" placeholder="Role Name" required>
                    <span class="text-danger name_error error-text"></span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-key me-1"></i> Module Permissions</h6>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-xs select-all-edit">Select All</button>
                        <button type="button" class="btn btn-outline-secondary btn-xs deselect-all-edit ms-1">Deselect All</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($groupedPermissions as $moduleKey => $permissions)
                        <div class="col-md-6">
                            <div class="card border mb-0 shadow-none h-100" style="background-color: #fcfdfe;">
                                <div class="card-header py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary" style="font-size: 0.88rem;">
                                        {{ $modules[$moduleKey] ?? ucfirst($moduleKey) }}
                                    </span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input module-check-all-edit" type="checkbox" data-target="module-edit-{{ $moduleKey }}" id="chk_edit_{{ $moduleKey }}">
                                        <label class="form-check-label small text-muted" for="chk_edit_{{ $moduleKey }}">All</label>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        @foreach ($permissions as $perm)
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input perm-edit-checkbox module-edit-{{ $moduleKey }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->name }}" 
                                                           id="edit_perm_{{ $perm->id }}">
                                                    <label class="form-check-label text-dark" for="edit_perm_{{ $perm->id }}" style="font-size: 0.82rem; cursor: pointer;">
                                                        {{ $perm->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Update Role</button>
            </div>
        </form>
    </div>
</div>
@endcan



