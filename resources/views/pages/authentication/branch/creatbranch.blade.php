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
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Department Name</label>
                             <input type="text" class="form-control" name="branch_name" placeholder="Branch Name">

                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Contact Person</label>
                             <input type="text" class="form-control" name="contact_person"
                                 placeholder="Contact Person">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Phone Number</label>
                             <input type="text" class="form-control" name="mobile" placeholder="Phone Number">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Address</label>
                             <textarea type="text" class="form-control" name="address"></textarea>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label>City</label>
                             <input type="text" class="form-control" name="city">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>State</label>
                             <input type="text" class="form-control" name="state">
                         </div>

                         <div class="mb-3 col-md-6">
                             <label>Pincode</label>
                             <input type="text" class="form-control" name="pincode">
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

 {{-- edit branch model --}}

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
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Department Name</label>
                             <input type="text" class="form-control" name="branch_name" placeholder="Branch Name" id="editbranch_name">
                             <input type="hidden" name="id" id="editid">

                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Contact Person</label>
                             <input type="text" class="form-control" name="contact_person" id="editcontact_person">
                                 placeholder="Contact Person">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Phone Number</label>
                             <input type="text" class="form-control" name="mobile" placeholder="Phone Number" id="editmobile">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Address</label>
                             <textarea type="text" class="form-control" name="address" id="editaddress"></textarea>
                         </div>

                         <div class="mb-3 col-md-6">
                             <label>City</label>
                             <input type="text" class="form-control" name="city" id="editcity">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>State</label>
                             <input type="text" class="form-control" name="state" id="editstate">
                         </div>

                         <div class="mb-3 col-md-6">
                             <label>Pincode</label>
                             <input type="text" class="form-control" name="pincode" id="editpincode">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Status</label>
                             <select class="form-control" name="status" id="editstatus">
                                 <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                             </select>
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

