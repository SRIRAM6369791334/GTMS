@can('users.create')
<div class="modal fade bd-user-modal-lg" tabindex="-1" id="userModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="useradd" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="User Name" required>
                        <span class="text-danger name_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach ($role as $roles)
                                <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger role_id_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Branch / Department <span class="text-danger">*</span></label>
                        <select class="form-control" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach ($branch as $branches)
                                <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger branch_id_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="10-15 digit phone number">
                        <span class="text-danger mobile_num_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Profile Image</label>
                        <div class="input-group mb-3">
                            <div class="form-file">
                                <input type="file" class="form-file-input form-control" name="image" accept="image/*">
                            </div>
                        </div>
                        <span class="text-danger image_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        <span class="text-danger email_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Password (min 6 characters)" required>
                        <span class="text-danger password_error error-text fs-12"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- edit user --}}
@can('users.edit')
<div class="modal fade bd-useredit-modal-lg" tabindex="-1" id="usereditModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="useredit" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editid">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="User Name" id="editname" required>
                        <span class="text-danger edit_name_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role_id" id="editrole" required>
                            <option value="">Select Role</option>
                            @foreach ($role as $roles)
                                <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger edit_role_id_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Branch / Department <span class="text-danger">*</span></label>
                        <select class="form-control" name="branch_id" id="editbranch" required>
                            <option value="">Select Branch</option>
                            @foreach ($branch as $branches)
                                <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger edit_branch_id_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="Phone Number" id="editmobile_num">
                        <span class="text-danger edit_mobile_num_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="editemail" required>
                        <span class="text-danger edit_email_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="editstatus">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="text-danger edit_status_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="image" id="editimage" accept="image/*">
                        <span class="text-danger edit_image_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password" placeholder="New Password" id="editpassword">
                        <span class="text-danger edit_password_error error-text fs-12"></span>
                    </div>
                    <div class="mb-3 col-md-12 d-flex align-items-center gap-3">
                        <div>
                            <label class="form-label d-block">Current Avatar</label>
                            <img width="60" height="60" class="rounded-circle border" src="" alt="" id="profileImage">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
@endcan



