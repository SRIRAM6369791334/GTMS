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

    // Destroy generic template initialization from datatables.init.js if present
    if ($.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable().destroy();
    }

    var table = $('#example10').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        searching: true,
        dom: '<"branch-dt-table-wrap"t><"branch-dt-footer d-flex flex-wrap align-items-center justify-content-between gap-3"ip>',
            language: {
                emptyTable: '<div class="text-center py-5 text-muted"><i class="fa fa-building fa-3x mb-3 d-block opacity-50"></i><p class="mb-0 fw-semibold">No departments found.</p><span class="fs-12 text-muted">Click "Add Department" to register the first regional office.</span></div>',
                info: 'Showing <span class="fw-bold">_START_</span> to <span class="fw-bold">_END_</span> of <span class="fw-bold">_TOTAL_</span> departments',
                infoEmpty: 'Showing 0 to 0 of 0 departments',
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            },
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '48px', className: 'text-center' },
                { targets: 1, width: '26%' },
                { targets: 2, width: '18%' },
                { targets: 3, width: '15%' },
                { targets: 4, width: '22%' },
                { targets: 5, width: '10%', className: 'text-center' },
                { targets: 6, width: '9%', orderable: false, className: 'text-end pe-3' }
            ]
        });

    // Connect Custom Live Search
    $('#branch_table_search').on('keyup input', function () {
        var query = $(this).val();
        if (query.length > 0) {
            $('#branch_search_clear').show();
        } else {
            $('#branch_search_clear').hide();
        }
        table.search(query).draw();
    });

    // Clear Search Input
    $('#branch_search_clear').on('click', function () {
        $('#branch_table_search').val('');
        $(this).hide();
        table.search('').draw();
    });

    // Connect Custom Page Length
    $('#branch_page_length').on('change', function () {
        var len = parseInt($(this).val(), 10);
        table.page.len(len).draw();
    });

    // Quick Filter by City (Column index 4 Address / Jurisdiction or 1)
    $('#filter_city').on('change', function () {
        var val = $(this).val();
        table.column(4).search(val ? val : '').draw();
    });

    // Quick Filter by Status (Column index 5)
    $('#filter_status').on('change', function () {
        var val = $(this).val();
        table.column(5).search(val ? val : '').draw();
    });

    // Quick Status Filter Tabs (Segmented Tabs in Panel Header)
    $(document).on('click', '.branch-tab-pill', function () {
        $('.branch-tab-pill').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status') || '';
        $('#filter_status').val(status).trigger('change');
    });

    // Reset All Filters
    $('#branch_reset_filters').on('click', function () {
        $('#branch_table_search').val('');
        $('#branch_search_clear').hide();
        $('#filter_city').val('');
        $('#filter_status').val('');
        $('.branch-tab-pill').removeClass('active');
        $('.branch-tab-pill[data-status=""]').addClass('active');
        table.search('').columns().search('').draw();
    });

    // Clear validation errors on modal dismissal
    $('#branchModal, #brancheeditModal').on('hidden.bs.modal', function () {
        $(this).find('.error-text').text('');
    });

    // =========================================================================
    // CELL FORMATTING HELPERS (SSR Parity for dynamic AJAX rows)
    // =========================================================================
    function formatDepartmentCell(data) {
        var initials = (data.branch_name || 'DP').substring(0, 2).toUpperCase();
        var cityBadge = data.city ? '<div class="d-flex align-items-center gap-1 mt-1"><span class="branch-city-pill"><i class="fa fa-location-dot me-1"></i>' + data.city + '</span></div>' : '';
        return '<div class="d-flex align-items-center gap-3">' +
            '<div class="branch-avatar-squircle">' + initials + '</div>' +
            '<div class="d-flex flex-column">' +
                '<span class="branch-name-text">' + (data.branch_name || '') + '</span>' +
                cityBadge +
            '</div>' +
        '</div>';
    }

    function formatContactCell(person) {
        if (!person) return '<span class="text-muted small">Unassigned</span>';
        return '<div class="d-flex align-items-center gap-2">' +
            '<i class="fa fa-user-tie text-muted"></i>' +
            '<span class="fw-semibold">' + person + '</span>' +
        '</div>';
    }

    function formatPhoneCell(mobile) {
        if (!mobile) return '<span class="text-muted small">N/A</span>';
        return '<a href="tel:' + mobile + '" class="branch-tel-link">' +
            '<i class="fa fa-phone text-primary me-1"></i> ' + mobile +
        '</a>';
    }

    function formatAddress(data) {
        var addr = data.address || 'No address specified';
        var loc = [];
        if (data.city) loc.push(data.city);
        if (data.state) loc.push(data.state);
        if (data.pincode) loc.push(data.pincode);
        var subText = loc.length > 0
            ? '<div class="branch-loc-sub"><i class="fa fa-map-pin text-primary me-1"></i> ' + loc.join(', ') + '</div>'
            : '';
        return '<div class="branch-address-wrap"><span class="branch-addr-line">' + addr + '</span>' + subText + '</div>';
    }

    function formatStatusBadge(status) {
        if (status == 1) {
            return '<span class="badge badge-success">Active</span>';
        } else {
            return '<span class="badge badge-danger">Inactive</span>';
        }
    }

    function formatActionButtons(data) {
        return '<div class="branch-action-dock">' +
            '<button type="button" class="dock-btn dock-edit editbranchBtn" ' +
                'data-bs-toggle="modal" data-bs-target="#brancheeditModal" ' +
                'data-id="' + data.id + '" ' +
                'data-name="' + (data.branch_name || '') + '" ' +
                'data-contact="' + (data.contact_person || '') + '" ' +
                'data-mobile="' + (data.mobile || '') + '" ' +
                'data-address="' + (data.address || '') + '" ' +
                'data-city="' + (data.city || '') + '" ' +
                'data-state="' + (data.state || '') + '" ' +
                'data-pincode="' + (data.pincode || '') + '" ' +
                'data-status="' + data.status + '" title="Edit Department">' +
                '<i class="fa fa-pencil"></i>' +
            '</button>' +
            '<button type="button" class="dock-btn dock-delete deletebranchBtn" ' +
                'data-id="' + data.id + '" title="Delete Department">' +
                '<i class="fa fa-trash"></i>' +
            '</button>' +
        '</div>';
    }

    // =========================================================================
    // 1. ADD DEPARTMENT AJAX
    // =========================================================================
    $('#brancheadd').on('submit', function (e) {
        e.preventDefault();
        $('#brancheadd').find('.error-text').text('');
        var submitBtn = $('#brancheadd button[type="submit"]');

        $.ajax({
            url: 'branchadd',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Save Department');
            },
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('#brancheadd .' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);

                    var sno = '<span class="branch-idx-pill">' + (table.rows().count() + 1) + '</span>';
                    var deptCell = formatDepartmentCell(response.data);
                    var contactCell = formatContactCell(response.data.contact_person);
                    var phoneCell = formatPhoneCell(response.data.mobile);
                    var addressCell = formatAddress(response.data);
                    var statusBadge = formatStatusBadge(response.data.status);
                    var actionButtons = formatActionButtons(response.data);

                    var addedRow = table.row.add([
                        sno,
                        deptCell,
                        contactCell,
                        phoneCell,
                        addressCell,
                        statusBadge,
                        actionButtons
                    ]).draw(false).node();

                    $(addedRow).attr('id', 'row' + response.data.id);

                    $('#brancheadd')[0].reset();
                    $('#brancheadd').find('.error-text').text('');
                    $('#branchModal').modal('hide');

                    // Increment Total KPI counter
                    var totalKpi = $('#kpi_total_branches');
                    if (totalKpi.length) {
                        totalKpi.text(parseInt(totalKpi.text() || '0', 10) + 1);
                    }
                    var activeKpi = $('#kpi_active_branches');
                    if (activeKpi.length && response.data.status == 1) {
                        activeKpi.text(parseInt(activeKpi.text() || '0', 10) + 1);
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('#brancheadd .' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong. Please check your inputs.");
                }
            }
        });
    });

    // =========================================================================
    // 2. OPEN EDIT MODAL (Delegated click for dynamic rows & pagination)
    // =========================================================================
    $(document).on('click', '.editbranchBtn', function () {
        $('#brancheedit').find('.error-text').text('');

        var btn = $(this);
        var id = btn.data('id');
        var branch_name = btn.data('name');
        var contact_person = btn.data('contact');
        var mobile = btn.data('mobile');
        var address = btn.data('address');
        var city = btn.data('city');
        var state = btn.data('state');
        var pincode = btn.data('pincode');
        var status = btn.data('status');

        $('#editid').val(id);
        $('#editbranch_name').val(branch_name || '');
        $('#editcontact_person').val(contact_person || '');
        $('#editmobile').val(mobile || '');
        $('#editaddress').val(address || '');
        $('#editcity').val(city || '');
        $('#editstate').val(state || '');
        $('#editpincode').val(pincode || '');
        $('#editstatus').val(status !== undefined ? status : 1);

        $('#brancheeditModal').modal('show');
    });

    // =========================================================================
    // 3. SUBMIT EDIT MODAL AJAX
    // =========================================================================
    $('#brancheedit').on('submit', function (e) {
        e.preventDefault();
        $('#brancheedit').find('.error-text').text('');
        var submitBtn = $('#brancheedit button[type="submit"]');

        $.ajax({
            url: 'branchedit',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Updating...');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Update Department');
            },
            success: function (response) {
                if (response.status == 0) {
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            $('#brancheedit .edit_' + key + '_error').text(value[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                } else {
                    toastr.success(response.message);

                    // Update row in DataTable
                    var editBtn = $('button.editbranchBtn[data-id="' + response.data.id + '"]');
                    var targetRow = table.row(editBtn.closest('tr'));
                    var currentSno = targetRow.data() ? targetRow.data()[0] : '<span class="branch-idx-pill">1</span>';

                    var deptCell = formatDepartmentCell(response.data);
                    var contactCell = formatContactCell(response.data.contact_person);
                    var phoneCell = formatPhoneCell(response.data.mobile);
                    var addressCell = formatAddress(response.data);
                    var statusBadge = formatStatusBadge(response.data.status);
                    var updatedButtons = formatActionButtons(response.data);

                    targetRow.data([
                        currentSno,
                        deptCell,
                        contactCell,
                        phoneCell,
                        addressCell,
                        statusBadge,
                        updatedButtons
                    ]).draw(false);

                    $('#brancheedit')[0].reset();
                    $('#brancheedit').find('.error-text').text('');
                    $('#brancheeditModal').modal('hide');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('#brancheedit .edit_' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong. Please check your inputs.");
                }
            }
        });
    });

    // =========================================================================
    // 4. DELETE DEPARTMENT (Safe SweetAlert confirmation with cascade warning)
    // =========================================================================
    $(document).on('click', '.deletebranchBtn', function () {
        var id = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Delete Department?',
            text: "Deleting this department will also permanently delete all user accounts assigned to it!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Department',
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

                            // Decrement Total KPI counter
                            var totalKpi = $('#kpi_total_branches');
                            if (totalKpi.length) {
                                var cur = parseInt(totalKpi.text() || '1', 10);
                                totalKpi.text(Math.max(0, cur - 1));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete Department',
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
