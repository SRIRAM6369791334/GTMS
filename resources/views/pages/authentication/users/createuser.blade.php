<div class="modal fade bd-user-modal-lg" tabindex="-1" id="userModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Add User</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="useradd" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Name</label>
                             <input type="text" class="form-control" name="name" placeholder="User Name">

                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Role</label>
                             <select class="form-control" name="role_id" >
                                 <option>Select Role</option>
                                 @foreach ($role as $roles)
                                     <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                                 @endforeach

                             </select>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Branch</label>
                            <select class="form-control" name="branch_id">
                                 <option>Select Branch</option>
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
                             <label>Email</label>
                             <input type="email" class="form-control" name="email">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Profile</label>
                             <input type="file" class="form-control" name="image">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Password</label>
                             <input type="text" class="form-control" name="password" placeholder="Password">
                         </div>


                     </div>

             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary">Save</button>
             </div>
         </div>
         </form>
     </div>
 </div>

 {{-- edit user --}}

 <div class="modal fade bd-useredit-modal-lg" tabindex="-1" id="usereditModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Edit User</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="useredit" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Name</label>
                             <input type="text" class="form-control" name="name" placeholder="User Name" id="editname">
                             <input type="hidden" class="form-control" name="id" id="editid">

                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Role</label>
                             <select class="form-control" name="role_id" id="editrole">
                                 <option>Select Role</option>
                                 @foreach ($role as $roles)
                                     <option value="{{ $roles->id }}">{{ $roles->name }}</option>
                                 @endforeach

                             </select>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Branch</label>
                            <select class="form-control" name="branch_id" id="editbranch">
                                 <option>Select Branch</option>
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
                             <label>Password</label>
                             <input type="text" class="form-control" name="password" placeholder="Password" id="editpassword">
                         </div>

                         <div class="mb-3 col-md-6">
                            <img  width="150" src="" alt="" id="profileImage">
                         </div>


                     </div>

             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary">Save</button>
             </div>
         </div>
         </form>
     </div>
 </div>

