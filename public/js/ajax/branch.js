$(document).ready(function() {
    var table = $('#example10').DataTable();

    // Helper to format address cell identical to Blade SSR
    function formatAddress(data) {
        var addr = data.address || '';
        var loc = [];
        if (data.city) loc.push(data.city);
        if (data.state) loc.push(data.state);
        if (data.pincode) loc.push(data.pincode);
        if (loc.length > 0) {
            addr += '<br>' + loc.join(', ');
        }
        return addr;
    }

    // --- ADD DEPARTMENT ---
    $('#brancheadd').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');

        $.ajax({
            url: 'branchadd',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);

                    var sno = table.rows().count() + 1;
                    var statusBadge = response.data.status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';

                    var actionButtons =
                        '<button type="button" class="btn btn-primary editbranchBtn shadow btn-xs sharp me-1" ' +
                        'data-bs-toggle="modal" data-bs-target=".bd-editbranch-modal-lg" ' +
                        'data-id="' + response.data.id + '" ' +
                        'data-name="' + (response.data.branch_name || '') + '" ' +
                        'data-contact="' + (response.data.contact_person || '') + '" ' +
                        'data-mobile="' + (response.data.mobile || '') + '" ' +
                        'data-address="' + (response.data.address || '') + '" ' +
                        'data-city="' + (response.data.city || '') + '" ' +
                        'data-state="' + (response.data.state || '') + '" ' +
                        'data-pincode="' + (response.data.pincode || '') + '" ' +
                        'data-status="' + response.data.status + '">' +
                            '<i class="fas fa-pencil-alt"></i>' +
                        '</button> ' +
                        '<button type="button" class="btn btn-danger deletebranchBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>';

                    var addedRow = table.row.add([
                        sno,
                        response.data.branch_name,
                        response.data.contact_person,
                        response.data.mobile,
                        formatAddress(response.data),
                        statusBadge,
                        actionButtons
                    ]).draw(false).node();

                    $(addedRow).attr('id', 'row' + response.data.id);

                    $('#brancheadd')[0].reset();
                    $('.error-text').text('');
                    $('#branchModal').modal('hide');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong. Please check your inputs.");
                }
            }
        });
    });

    // --- OPEN EDIT MODAL (Delegated click for dynamic rows & pagination) ---
    $(document).on('click', '.editbranchBtn', function () {
        $('.error-text').text('');

        var id = $(this).data('id');
        var branch_name = $(this).data('name');
        var contact_person = $(this).data('contact');
        var mobile = $(this).data('mobile');
        var address = $(this).data('address');
        var city = $(this).data('city');
        var state = $(this).data('state');
        var pincode = $(this).data('pincode');
        var status = $(this).data('status');

        $('#editid').val(id);
        $('#editbranch_name').val(branch_name);
        $('#editcontact_person').val(contact_person);
        $('#editmobile').val(mobile);
        $('#editaddress').val(address);
        $('#editcity').val(city);
        $('#editstate').val(state);
        $('#editpincode').val(pincode);
        $('#editstatus').val(status);

        $('#brancheeditModal').modal('show');
    });

    // --- SUBMIT EDIT MODAL ---
    $('#brancheedit').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');

        $.ajax({
            url: 'branchedit',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status == 0) {
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            $('.edit_' + key + '_error').text(value[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                } else {
                    toastr.success(response.message);

                    // Update row in DataTable
                    var editBtn = $('button.editbranchBtn[data-id="' + response.data.id + '"]');
                    var targetRow = table.row(editBtn.closest('tr'));
                    var currentSno = targetRow.data() ? targetRow.data()[0] : 1;

                    var statusBadge = response.data.status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';

                    var updatedButtons =
                        '<button type="button" class="btn btn-primary editbranchBtn shadow btn-xs sharp me-1" ' +
                        'data-bs-toggle="modal" data-bs-target=".bd-editbranch-modal-lg" ' +
                        'data-id="' + response.data.id + '" ' +
                        'data-name="' + (response.data.branch_name || '') + '" ' +
                        'data-contact="' + (response.data.contact_person || '') + '" ' +
                        'data-mobile="' + (response.data.mobile || '') + '" ' +
                        'data-address="' + (response.data.address || '') + '" ' +
                        'data-city="' + (response.data.city || '') + '" ' +
                        'data-state="' + (response.data.state || '') + '" ' +
                        'data-pincode="' + (response.data.pincode || '') + '" ' +
                        'data-status="' + response.data.status + '">' +
                            '<i class="fas fa-pencil-alt"></i>' +
                        '</button> ' +
                        '<button type="button" class="btn btn-danger deletebranchBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>';

                    targetRow.data([
                        currentSno,
                        response.data.branch_name,
                        response.data.contact_person,
                        response.data.mobile,
                        formatAddress(response.data),
                        statusBadge,
                        updatedButtons
                    ]).draw(false);

                    $('#brancheedit')[0].reset();
                    $('.error-text').text('');
                    $('#brancheeditModal').modal('hide');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.edit_' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong. Please check your inputs.");
                }
            }
        });
    });

    // --- DELETE DEPARTMENT (Safe SweetAlert confirmation with cascade warning) ---
    $(document).on('click', '.deletebranchBtn', function () {
        var id = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "Deleting this department will also permanently delete all user accounts assigned to it!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'branchdelete',
                    type: 'POST',
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 1) {
                            toastr.success(response.message);
                            table.row(button.closest('tr')).remove().draw();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred while deleting the department.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errMsg
                        });
                    }
                });
            }
        });
    });
});
