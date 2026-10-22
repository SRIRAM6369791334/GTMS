$(document).ready(function() {
    // 1. Destroy any existing instance created by datatables.init.js
    if ($.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable().destroy();
    }

    // 2. Initialize DataTables with custom DOM and explicit searching enabled
    var table = $('#example10').DataTable({
        dom: '<"ro-dt-table-wrap"t><"ro-dt-footer"ip>',
        searching: true,
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        ordering: true,
        order: [[0, 'asc']],
        language: {
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            },
            info: 'Showing _START_ to _END_ of _TOTAL_ security roles',
            infoEmpty: 'No roles found',
            emptyTable: 'No security roles configured in system'
        }
    });

    // 3. Connect Live Instant Search
    $('#role_table_search').on('input', function() {
        var val = $(this).val();
        table.search(val).draw();
        if (val.length > 0) {
            $('#role_search_clear').show();
        } else {
            $('#role_search_clear').hide();
        }
    });

    $('#role_search_clear').on('click', function() {
        $('#role_table_search').val('');
        table.search('').draw();
        $(this).hide();
        $('#role_table_search').focus();
    });

    // 4. Role Tier Filter Dropdown
    $('#filter_tier').on('change', function() {
        var val = $(this).val();
        table.column(1).search(val ? val : '').draw();

        // Sync quick tabs
        $('.ro-tab-pill').removeClass('active');
        if (val === 'Super Admin') {
            $('.ro-tab-pill[data-filter="system"]').addClass('active');
        } else if (val === 'Custom Role') {
            $('.ro-tab-pill[data-filter="custom"]').addClass('active');
        } else {
            $('.ro-tab-pill[data-filter="all"]').addClass('active');
        }
    });

    // 5. Permission Coverage Filter Dropdown
    $('#filter_coverage').on('change', function() {
        var val = $(this).val();
        table.column(2).search(val ? val : '').draw();
    });

    // 6. Page Length Selector
    $('#role_page_length').on('change', function() {
        var len = parseInt($(this).val(), 10);
        table.page.len(len).draw();
    });

    // 7. Quick Segmented Filter Tabs
    $('.ro-tab-pill').on('click', function() {
        $('.ro-tab-pill').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).data('filter');
        if (filter === 'system') {
            $('#filter_tier').val('Super Admin');
            table.column(1).search('Super Admin').draw();
        } else if (filter === 'custom') {
            $('#filter_tier').val('Custom Role');
            table.column(1).search('Custom Role').draw();
        } else {
            $('#filter_tier').val('');
            table.column(1).search('').draw();
        }
    });

    // 8. Reset All Filters Button
    $('#role_reset_filters').on('click', function() {
        $('#role_table_search').val('');
        $('#role_search_clear').hide();
        $('#filter_tier').val('');
        $('#filter_coverage').val('');
        $('#role_page_length').val('10');
        $('.ro-tab-pill').removeClass('active');
        $('.ro-tab-pill[data-filter="all"]').addClass('active');
        table.search('').columns().search('').page.len(10).draw();
    });

    // ==========================================================================
    // MODAL CHECKBOX SELECT / DESELECT ALL HANDLERS
    // ==========================================================================

    // Add Modal Select All / Deselect All
    $('.select-all-add').on('click', function() {
        $('.perm-add-checkbox:visible').prop('checked', true);
        $('.module-check-all-add:visible').prop('checked', true);
    });

    $('.deselect-all-add').on('click', function() {
        $('.perm-add-checkbox:visible').prop('checked', false);
        $('.module-check-all-add:visible').prop('checked', false);
    });

    $('.module-check-all-add').on('change', function() {
        var targetClass = $(this).data('target');
        $('.' + targetClass).prop('checked', $(this).is(':checked'));
    });

    // Edit Modal Select All / Deselect All
    $('.select-all-edit').on('click', function() {
        $('.perm-edit-checkbox:visible').prop('checked', true);
        $('.module-check-all-edit:visible').prop('checked', true);
    });

    $('.deselect-all-edit').on('click', function() {
        $('.perm-edit-checkbox:visible').prop('checked', false);
        $('.module-check-all-edit:visible').prop('checked', false);
    });

    $('.module-check-all-edit').on('change', function() {
        var targetClass = $(this).data('target');
        $('.' + targetClass).prop('checked', $(this).is(':checked'));
    });

    // In-Modal Search Filtering for Add Modal
    $('.perm-modal-filter').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('.perm-module-col').show();
            $('.perm-item-wrapper').show();
            return;
        }
        $('.perm-module-col').each(function() {
            var moduleName = $(this).data('module') || '';
            var hasMatchingPerm = false;
            $(this).find('.perm-item-wrapper').each(function() {
                var permName = $(this).data('perm-name') || '';
                if (permName.indexOf(q) !== -1 || moduleName.indexOf(q) !== -1) {
                    $(this).show();
                    hasMatchingPerm = true;
                } else {
                    $(this).hide();
                }
            });
            $(this).toggle(hasMatchingPerm);
        });
    });

    // In-Modal Search Filtering for Edit Modal
    $('.perm-modal-filter-edit').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('.perm-module-col-edit').show();
            $('.perm-item-wrapper-edit').show();
            return;
        }
        $('.perm-module-col-edit').each(function() {
            var moduleName = $(this).data('module') || '';
            var hasMatchingPerm = false;
            $(this).find('.perm-item-wrapper-edit').each(function() {
                var permName = $(this).data('perm-name') || '';
                if (permName.indexOf(q) !== -1 || moduleName.indexOf(q) !== -1) {
                    $(this).show();
                    hasMatchingPerm = true;
                } else {
                    $(this).hide();
                }
            });
            $(this).toggle(hasMatchingPerm);
        });
    });

    // ==========================================================================
    // CRUD OPERATIONS (AJAX)
    // ==========================================================================

    // Add Role Submit
    $('#rolesadd').submit(function (e) {
        e.preventDefault();
        var form = $(this);
        form.find('.error-text').text('');

        $.ajax({
            url: 'roleadd',
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status == 0) {
                    if (response.message) {
                        toastr.error(response.message);
                    }
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            form.find('.' + key + '_error').text(value[0]);
                        });
                    }
                } else {
                    toastr.success(response.message);
                    form[0].reset();
                    $('.module-check-all-add').prop('checked', false);
                    $('#roleModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        form.find('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong while creating the role.");
                }
            }
        });
    });

    // Edit Role Button Click -> fetch role permissions
    $(document).on('click', '.editBtn', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#role_id').val(id);
        $('#role_name').val(name);
        $('.perm-edit-checkbox').prop('checked', false);
        $('.module-check-all-edit').prop('checked', false);
        $('.perm-modal-filter-edit').val('').trigger('input');

        // Fetch assigned permissions
        $.ajax({
            url: 'roles/' + id + '/permissions',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    var assignedPerms = response.permissions;
                    $('.perm-edit-checkbox').each(function() {
                        if (assignedPerms.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });

                    // Check if all checkboxes in a module are selected
                    $('.module-check-all-edit').each(function() {
                        var target = $(this).data('target');
                        var total = $('.' + target).length;
                        var checked = $('.' + target + ':checked').length;
                        if (total > 0 && total === checked) {
                            $(this).prop('checked', true);
                        }
                    });
                }
                $('#roleeditModal').modal('show');
            },
            error: function() {
                $('#roleeditModal').modal('show');
            }
        });
    });

    // Update Role Submit
    $('#rolesedit').submit(function (e) {
        e.preventDefault();
        var form = $(this);
        form.find('.error-text').text('');

        $.ajax({
            url: 'roleupdate',
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status == 0) {
                    if (response.message) {
                        toastr.error(response.message);
                    }
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            form.find('.' + key + '_error').text(value[0]);
                        });
                    }
                } else {
                    toastr.success(response.message);
                    $('#roleeditModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        form.find('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong while updating the role.");
                }
            }
        });
    });

    // Delete Role
    $(document).on('click', '.deleteBtn', function () {
        var id = $(this).data('id');
        var roleName = $(this).data('name') || 'this role';
        var button = $(this);

        Swal.fire({
            title: 'Delete Security Role?',
            text: "Are you sure you want to permanently delete '" + roleName + "'? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fa fa-trash me-1"></i> Yes, Delete Role',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'roledelete',
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
                                title: 'Cannot Delete Role',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong!';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    }
                });
            }
        });
    });
});
