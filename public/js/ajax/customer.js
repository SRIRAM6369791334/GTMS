$(document).ready(function() {
    // CSRF Header Setup for all AJAX calls
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable if not already initialized
    if (!$.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            }
        });
    }

    // =========================================================================
    // AUTO-CONVERT / MASK AADHAAR NUMBER: 987654321001 -> 9876-5432-1001
    // =========================================================================
    function maskAadhaar(value) {
        if (!value) return '';
        var digits = value.replace(/\D/g, '').substring(0, 12);
        var parts = [];
        for (var i = 0; i < digits.length; i += 4) {
            parts.push(digits.substring(i, i + 4));
        }
        return parts.join('-');
    }

    $(document).on('input paste keyup', 'input[name="aadhaar_no"], #edit_aadhaar_no, .aadhaar-format', function() {
        var input = this;
        var current = $(input).val();
        var formatted = maskAadhaar(current);
        if (current !== formatted) {
            $(input).val(formatted);
        }
    });

    // =========================================================================
    // 1. ADD CUSTOMER AJAX
    // =========================================================================
    $('#customeradd').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');
        var formData = new FormData(this);

        $.ajax({
            url: '/customeradd',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $('#customeradd button[type="submit"]').prop('disabled', true).text('Saving...');
            },
            complete: function () {
                $('#customeradd button[type="submit"]').prop('disabled', false).text('Save Customer');
            },
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);
                    $('#customeradd')[0].reset();
                    $('.bd-customer-modal-lg').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Failed to add customer. Please check the inputs.");
                }
            }
        });
    });

    // =========================================================================
    // 2. POPULATE EDIT CUSTOMER MODAL
    // =========================================================================
    $(document).on('click', '.editCustomerBtn', function () {
        var btn = $(this);
        $('#edit_id').val(btn.data('id'));
        $('#edit_mimas_no').val(btn.data('mimas'));
        $('#edit_customer_name').val(btn.data('name'));
        $('#edit_company_name').val(btn.data('company'));
        $('#edit_mobile_num').val(btn.data('mobile'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_district_id').val(btn.data('district-id'));
        $('#edit_pan').val(btn.data('pan'));
        $('#edit_aadhaar_no').val(btn.data('aadhaar'));
        $('#edit_gstin').val(btn.data('gstin'));
        $('#edit_status').val(btn.data('status'));
        $('#edit_address').val(btn.data('address'));

        $('.edit-error-text').text('');
        $('#editCustomerModal').modal('show');
    });

    // =========================================================================
    // 3. SUBMIT EDIT CUSTOMER AJAX
    // =========================================================================
    $('#customeredit').on('submit', function (e) {
        e.preventDefault();
        $('.edit-error-text').text('');
        var formData = new FormData(this);

        $.ajax({
            url: '/customeredit',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $('#customeredit button[type="submit"]').prop('disabled', true).text('Updating...');
            },
            complete: function () {
                $('#customeredit button[type="submit"]').prop('disabled', false).text('Update Customer');
            },
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('.edit_' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);
                    $('#editCustomerModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.edit_' + key + '_error').text(value[0]);
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Failed to update customer.");
                }
            }
        });
    });

    // =========================================================================
    // 4. VIEW CUSTOMER DETAILS
    // =========================================================================
    $(document).on('click', '.viewCustomerBtn', function () {
        var id = $(this).data('id');

        $.ajax({
            url: '/customers/' + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.status == 1) {
                    var c = response.data;
                    var initials = (c.company_name || c.customer_name || 'CU').substring(0, 2).toUpperCase();
                    
                    $('#view_avatar_initials').text(initials);
                    $('#view_company_title').text(c.company_name || c.customer_name);
                    $('#view_rep_subtitle').text((c.customer_name || '') + ' (CUST-' + String(c.id).padStart(3, '0') + ')');
                    
                    if (c.status == 1) {
                        $('#view_status_badge').html('<span class="badge bg-success-subtle text-success border border-success"><i class="fa fa-check-circle me-1"></i> Active</span>');
                    } else {
                        $('#view_status_badge').html('<span class="badge bg-warning-subtle text-warning border border-warning"><i class="fa fa-clock me-1"></i> Under Validation / Inactive</span>');
                    }

                    $('#view_mimas_no').text(c.mimas_no || 'N/A');
                    $('#view_mobile').text(c.mobile_num || 'N/A');
                    $('#view_email').text(c.email || 'N/A');
                    $('#view_district').text(c.district ? c.district.name : 'N/A');
                    $('#view_pan').text(c.pan || 'N/A');
                    $('#view_aadhaar_no').text(c.aadhaar_no || 'N/A');
                    $('#view_gstin').text(c.gstin || 'N/A');
                    $('#view_address').text(c.address || 'No address provided');

                    $('#viewCustomerModal').modal('show');
                } else {
                    toastr.error(response.message || "Customer not found.");
                }
            },
            error: function() {
                toastr.error("Error loading customer profile.");
            }
        });
    });

    // =========================================================================
    // 5. DELETE CUSTOMER SWEETALERT + AJAX
    // =========================================================================
    $(document).on('click', '.deleteCustomerBtn', function () {
        var id = $(this).data('id');
        var name = $(this).data('name') || 'this customer';

        Swal.fire({
            title: 'Delete Customer?',
            text: 'Are you sure you want to delete "' + name + '"? This will soft-delete the record.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/customerdelete',
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 1) {
                            toastr.success(response.message);
                            setTimeout(function() { location.reload(); }, 600);
                        } else {
                            toastr.error(response.message || 'Could not delete customer.');
                        }
                    },
                    error: function () {
                        toastr.error('Something went wrong during deletion.');
                    }
                });
            }
        });
    });
});
