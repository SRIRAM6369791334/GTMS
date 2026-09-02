<div class="modal fade bd-example-modal-lg" id="catModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="cateadd">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Category Code</label>
                            <input type="text" class="form-control" name="cat_code" placeholder="Category Code">


                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="cat_name" placeholder="Category Name">


                        </div>


                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- edit cat --}}

<div class="modal fade bd-editcat-modal-lg" id="editcatModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="catedit">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Category Code</label>
                            <input type="text" class="form-control" name="cat_code" placeholder="Category Code" id="editcat_code">
                            <input type="hidden" class="form-control" name="cat_id" id="editcat_id">


                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="cat_name" placeholder="Category Name" id="editcat_name">


                        </div>


                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</div>



