 @can('branch.create')
 <div class="modal fade bd-example-modal-lg" tabindex="-1" id="branchModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Add Department</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="brancheadd">
                     @csrf
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Department Name <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="branch_name" placeholder="Branch / Department Name">
                             <span class="text-danger error-text branch_name_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="contact_person" placeholder="Contact Person Name">
                             <span class="text-danger error-text contact_person_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="mobile" placeholder="10-15 digit phone number">
                             <span class="text-danger error-text mobile_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Address <span class="text-danger">*</span></label>
                             <textarea class="form-control" name="address" rows="1" placeholder="Department street address"></textarea>
                             <span class="text-danger error-text address_error fs-12"></span>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">City</label>
                             <input type="text" class="form-control" name="city" placeholder="City">
                             <span class="text-danger error-text city_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">State</label>
                             <input type="text" class="form-control" name="state" placeholder="State">
                             <span class="text-danger error-text state_error fs-12"></span>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">Pincode</label>
                             <input type="text" class="form-control" name="pincode" placeholder="6-digit pincode">
                             <span class="text-danger error-text pincode_error fs-12"></span>
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
 @endcan

 {{-- edit branch model --}}

 @can('branch.edit')
 <div class="modal fade bd-editbranch-modal-lg" tabindex="-1" id="brancheeditModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Edit Department</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="brancheedit">
                     @csrf
                     <input type="hidden" name="id" id="editid">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Department Name <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="branch_name" placeholder="Branch Name" id="editbranch_name">
                             <span class="text-danger error-text edit_branch_name_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="contact_person" id="editcontact_person" placeholder="Contact Person">
                             <span class="text-danger error-text edit_contact_person_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" name="mobile" placeholder="Phone Number" id="editmobile">
                             <span class="text-danger error-text edit_mobile_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Address <span class="text-danger">*</span></label>
                             <textarea class="form-control" name="address" id="editaddress" rows="1"></textarea>
                             <span class="text-danger error-text edit_address_error fs-12"></span>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">City</label>
                             <input type="text" class="form-control" name="city" id="editcity">
                             <span class="text-danger error-text edit_city_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">State</label>
                             <input type="text" class="form-control" name="state" id="editstate">
                             <span class="text-danger error-text edit_state_error fs-12"></span>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">Pincode</label>
                             <input type="text" class="form-control" name="pincode" id="editpincode">
                             <span class="text-danger error-text edit_pincode_error fs-12"></span>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Status <span class="text-danger">*</span></label>
                             <select class="form-control" name="status" id="editstatus">
                                 <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                             </select>
                             <span class="text-danger error-text edit_status_error fs-12"></span>
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
 @endcan


