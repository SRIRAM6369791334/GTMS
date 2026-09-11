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
                <input type="hidden" name="id" id="id">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="User Name" required>
                        <span class="text-danger name_error error-text"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach ($role as $roles)
                                <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger role_id_error error-text"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Branch</label>
                        <select class="form-control" name="branch_id">
                            <option value="">Select Branch</option>
                            @foreach ($branch as $branches)
                                <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="Phone Number">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Profile Image</label>
                        <div class="input-group mb-3">
                            <div class="form-file">
                                <input type="file" class="form-file-input form-control" name="image">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                        <span class="text-danger email_error error-text"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <span class="text-danger password_error error-text"></span>
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
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="User Name" id="editname">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-control" name="role_id" id="editrole">
                            <option value="">Select Role</option>
                            @foreach ($role as $roles)
                                <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Branch</label>
                        <select class="form-control" name="branch_id" id="editbranch">
                            <option value="">Select Branch</option>
                            @foreach ($branch as $branches)
                                <option value="{{ $branches->id }}">{{ $branches->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="Phone Number" id="editmobile_num">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="editemail">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Profile</label>
                        <input type="file" class="form-control" name="image" id="editimage">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password" placeholder="New Password" id="editpassword">
                    </div>
                    <div class="mb-3 col-md-6">
                        <img width="80" height="80" class="rounded-circle border" src="" alt="" id="profileImage">
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



