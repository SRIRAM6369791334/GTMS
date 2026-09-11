 @can('product.create')
 <div class="modal fade bd-product-modal-lg" tabindex="-1" id="productModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Add Product</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="productadd">
                     @csrf
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                            <label class="form-label">Branch</label>
                            <select class="form-control" name="branch_id" id="branch_id">
                                <option value="">Select Branch</option>
                                @foreach ($branch as $br)
                                    <option value="{{ $br->id }}">{{ $br->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="cat_id" id="cat_id">
                                <option value="">Select Category</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->cat_name }}</option>
                                @endforeach
                            </select>
                        </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">Product Name</label>
                             <input type="text" class="form-control" name="pro_name" placeholder="Product Name">

                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">GST</label>
                             <input type="text" class="form-control" name="gst"
                                 placeholder="GST">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Cast %</label>
                             <input type="text" class="form-control" name="cast_per" placeholder="Cast %">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label class="form-label">MRP</label>
                                <input type="text" class="form-control" name="mrp" placeholder="MRP">
                         </div>

                         <div class="mb-3 col-md-6">
                             <label class="form-label">Quantity</label>
                             <input type="text" class="form-control" name="qty" placeholder="Quantity">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Discount R</label>
                             <input type="text" class="form-control" name="discount_1" placeholder="Discount R">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Discount G</label>
                             <input type="text" class="form-control" name="discount_2" placeholder="Discount G">
                         </div>
                         <div class="mb-3 col-md-6">
                             <label>Discount Y</label>
                             <input type="text" class="form-control" name="discount_3" placeholder="Discount Y">
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


 @can('product.edit')
 <div class="modal fade bd-editbranch-modal-lg" tabindex="-1" id="brancheeditModal" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Edit Branch</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal">
                 </button>
             </div>
             <div class="modal-body">
                 <form id="brancheedit">
                     @csrf
                     <input type="hidden" name="id" id="id">
                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label class="form-label">Branch Name</label>
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
 @endcan


