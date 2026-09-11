@can('unit.create')
<div class="modal fade bd-example-modal-lg" id="unitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="unitsadd">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Unit Name</label>
                            <input type="text" class="form-control" name="units" placeholder="Unit Name">


                        </div>


                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endcan


{{-- edit --}}
@can('unit.edit')
<div class="modal fade bd-edit-modal-lg" id="uniteditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="unitsedit">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Unit Name</label>
                            <input type="text" class="form-control" name="units" placeholder="Unit Name" id="unit_name">
                            <input type="hidden" name="id" id="unit_id">


                        </div>


                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endcan

