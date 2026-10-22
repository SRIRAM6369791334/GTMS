$(document).ready(function() {
    // CSRF Header Setup for all AJAX calls
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Suppress intrusive DataTables popup alerts in favor of console log
    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }

    // Initialize DataTable with clean dom (toolbar handles search, filters & length)
    var dataTable;
    if (!$.fn.DataTable.isDataTable('#customersTable')) {
        dataTable = $('#customersTable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            searching: true,
            dom: '<"cust-dt-table-wrap"t><"cust-dt-footer d-flex flex-wrap align-items-center justify-content-between gap-3"ip>',
            language: {
                emptyTable: '<div class="text-center py-5 text-muted"><i class="fa fa-folder-open fa-3x mb-3 d-block opacity-50"></i><p class="mb-0 fw-semibold">No customers found.</p><span class="fs-12 text-muted">Click "Add Customer" to create the first record.</span></div>',
                info: 'Showing <span class="fw-bold">_START_</span> to <span class="fw-bold">_END_</span> of <span class="fw-bold">_TOTAL_</span> entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            },
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '44px', className: 'text-center' },
                { targets: 1, width: '33%' },
                { targets: 2, width: '11%' },
                { targets: 3, width: '15%' },
                { targets: 4, width: '16%' },
                { targets: 5, width: '11%', className: 'text-center' },
                { targets: 6, width: '11%', orderable: false, className: 'text-end pe-3' }
            ]
        });
    } else {
        dataTable = $('#customersTable').DataTable();
    }

    // Connect Custom Live Search
    $('#cust_table_search').on('keyup input', function () {
        var query = $(this).val();
        if (query.length > 0) {
            $('#cust_search_clear').show();
        } else {
            $('#cust_search_clear').hide();
        }
        dataTable.search(query).draw();
    });

    // Clear Search Input
    $('#cust_search_clear').on('click', function () {
        $('#cust_table_search').val('');
        $(this).hide();
        dataTable.search('').draw();
    });

    // Connect Custom Page Length
    $('#cust_page_length').on('change', function () {
        var len = parseInt($(this).val(), 10);
        dataTable.page.len(len).draw();
    });

    // Quick Filter by District (Column index 2)
    $('#filter_district').on('change', function () {
        var val = $(this).val();
        dataTable.column(2).search(val ? val : '').draw();
    });

    // Quick Filter by Status (Column index 5)
    $('#filter_status').on('change', function () {
        var val = $(this).val();
        dataTable.column(5).search(val ? val : '').draw();
    });

    // Quick Status Filter Tabs (Segmented Tabs in Panel Header)
    $(document).on('click', '.cust-tab-pill', function () {
        $('.cust-tab-pill').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status') || '';
        $('#filter_status').val(status).trigger('change');
    });

    // Reset All Filters
    $('#cust_reset_filters').on('click', function () {
        $('#cust_table_search').val('');
        $('#cust_search_clear').hide();
        $('#filter_district').val('');
        $('#filter_status').val('');
        $('.cust-tab-pill').removeClass('active');
        $('.cust-tab-pill[data-status=""]').addClass('active');
        dataTable.search('').columns().search('').draw();
    });

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
        $('#customeradd').find('.error-text').text('');
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
                        $('#customeradd .' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);
                    $('#customeradd')[0].reset();
                    $('#customerModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#customeradd .' + key + '_error').text(value[0]);
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
    function populateEditCustomerFields(c) {
        if (!c) return;
        $('#edit_id').val(c.id || '');
        $('#edit_mimas_no').val(c.mimas_no || '');
        $('#edit_mimas_number').val(c.mimas_number || '');
        $('#edit_mimas_status').val(c.mimas_status || '');
        $('#edit_customer_name').val(c.customer_name || '');
        $('#edit_secondary_contact_person').val(c.secondary_contact_person || '');
        $('#edit_company_name').val(c.company_name || '');
        $('#edit_mobile_num').val(c.mobile_num || '');
        $('#edit_secondary_mobile_num').val(c.secondary_mobile_num || '');
        $('#edit_email').val(c.email || '');
        $('#edit_district_id').val(c.district_id || '');
        if ($('#edit_mineral_id').length) {
            $('#edit_mineral_id').val(c.mineral_id || '');
        }
        if ($('#edit_area').length) {
            $('#edit_area').val(c.area || '');
        }
        $('#edit_pan').val(c.pan || '');
        $('#edit_aadhaar_no').val(c.aadhaar_no || '');
        $('#edit_gstin').val(c.gstin || '');
        $('#edit_status').val(c.status !== undefined ? c.status : 1);
        $('#edit_address').val(c.address || '');

        $('#customeredit').find('.error-text').text('');
        $('#editCustomerModal').modal('show');
    }

    function openCustomerEditById(id) {
        if (!id) return;

        // 1. Check if button is in current DOM page
        var btn = $('.editCustomerBtn[data-id="' + id + '"]').first();
        if (btn.length > 0) {
            btn.trigger('click');
            return;
        }

        // 2. Fallback: If record is on another DataTables page or navigated directly via #edit-{id}
        $.ajax({
            url: '/customers/' + id,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status == 1 && response.data) {
                    populateEditCustomerFields(response.data);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Customer record not found.');
                    }
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load customer details for editing.');
                }
            }
        });
    }

    $(document).on('click', '.editCustomerBtn', function () {
        var btn = $(this);
        var data = btn.data('customer');

        if (data) {
            populateEditCustomerFields(data);
        } else {
            // Fallback to individual data attributes
            var c = {
                id: btn.data('id'),
                mimas_no: btn.data('mimas'),
                mimas_number: btn.data('mimas-number'),
                mimas_status: btn.data('mimas-status'),
                customer_name: btn.data('name'),
                secondary_contact_person: btn.data('secondary-contact'),
                company_name: btn.data('company'),
                mobile_num: btn.data('mobile'),
                secondary_mobile_num: btn.data('secondary-mobile'),
                email: btn.data('email'),
                district_id: btn.data('district-id'),
                mineral_id: btn.data('mineral-id'),
                area: btn.data('area'),
                pan: btn.data('pan'),
                aadhaar_no: btn.data('aadhaar'),
                gstin: btn.data('gstin'),
                status: btn.data('status'),
                address: btn.data('address')
            };
            populateEditCustomerFields(c);
        }
    });

    // Clear validation errors on modal dismissal and clean hash fragment
    $('#customerModal, #editCustomerModal').on('hidden.bs.modal', function () {
        $(this).find('.error-text').text('');
        if (this.id === 'editCustomerModal' && window.location.hash && window.location.hash.indexOf('#edit-') === 0) {
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
            }
        }
    });

    // =========================================================================
    // 2B. AUTO-OPEN EDIT MODAL VIA URL HASH (#edit-123 or #edit-slug)
    // =========================================================================
    function checkUrlHashForEdit() {
        var hash = window.location.hash;
        if (!hash) return;
        var match = hash.match(/^#edit-([a-zA-Z0-9_\-]+)$/);
        if (match && match[1]) {
            openCustomerEditById(match[1]);
        }
    }

    // Trigger on initial page load
    setTimeout(checkUrlHashForEdit, 250);

    // Also trigger on subsequent hash changes
    $(window).on('hashchange', function() {
        checkUrlHashForEdit();
    });

    // =========================================================================
    // 3. SUBMIT EDIT CUSTOMER AJAX
    // =========================================================================
    $('#customeredit').on('submit', function (e) {
        e.preventDefault();
        $('#customeredit').find('.error-text').text('');
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
                        $('#customeredit .edit_' + key + '_error').text(value[0]);
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
                        $('#customeredit .edit_' + key + '_error').text(value[0]);
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
                        $('#view_status_badge').html('<span class="cust-status-pill cust-status-active"><i class="fa fa-check-circle me-1"></i> Active</span>');
                    } else {
                        $('#view_status_badge').html('<span class="cust-status-pill cust-status-pending"><i class="fa fa-clock me-1"></i> Under Validation / Inactive</span>');
                    }

                    $('#view_mimas_no').text(c.mimas_no || 'N/A');
                    $('#view_mimas_number').text(c.mimas_number || 'N/A');
                    $('#view_mimas_status').text(c.mimas_status || 'N/A');
                    $('#view_rep').text(c.customer_name || 'N/A');
                    $('#view_mobile').text(c.mobile_num || 'N/A');
                    $('#view_secondary_contact').text(c.secondary_contact_person || 'Not specified');
                    $('#view_secondary_mobile').text(c.secondary_mobile_num || 'Not specified');
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete Customer',
                                text: response.message || 'Could not delete customer.'
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong during deletion.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot Delete Customer',
                            text: msg
                        });
                    }
                });
            }
        });
    });
});
