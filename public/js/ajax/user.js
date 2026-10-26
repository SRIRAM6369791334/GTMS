$(document).ready(function() {
    // ==========================================================================
    // 1. SAFE DATATABLE INITIALIZATION (CLEAN DOM & FULL RESPONSIVENESS)
    // ==========================================================================
    if ($.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable().destroy();
    }

    var table = $('#example10').DataTable({
        dom: '<"usr-dt-table-wrap"t><"usr-dt-footer"ip>',
        searching: true,
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        ordering: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting on action dock
        ],
        language: {
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            },
            info: 'Showing _START_ to _END_ of _TOTAL_ personnel',
            infoEmpty: 'No users found',
            emptyTable: 'No user accounts available in the directory'
        }
    });

    // ==========================================================================
    // 2. LIVE INSTANT SEARCH WITH CLEAR BUTTON
    // ==========================================================================
    $('#user_table_search').on('input', function() {
        var val = $(this).val();
        table.search(val).draw();
        if (val.length > 0) {
            $('#user_search_clear').show();
        } else {
            $('#user_search_clear').hide();
        }
    });

    $('#user_search_clear').on('click', function() {
        $('#user_table_search').val('');
        table.search('').draw();
        $(this).hide();
        $('#user_table_search').focus();
    });

    // ==========================================================================
    // 3. MULTI-FILTER CONTROLS (ROLE, BRANCH, STATUS, PAGE LENGTH)
    // ==========================================================================

    // Quick Filter Tabs (All / Active / Inactive)
    $('.usr-tab-pill').on('click', function() {
        $('.usr-tab-pill').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).data('filter');
        if (filter === 'active') {
            table.column(5).search('Active').draw();
            $('#filter_status').val('Active');
        } else if (filter === 'inactive') {
            table.column(5).search('Inactive').draw();
            $('#filter_status').val('Inactive');
        } else {
            table.column(5).search('').draw();
            $('#filter_status').val('');
        }
    });

    // Role Filter Dropdown (Column 2)
    $('#filter_role').on('change', function() {
        var val = $(this).val();
        table.column(2).search(val ? val : '').draw();
    });

    // Branch / Department Filter Dropdown (Column 3)
    $('#filter_branch').on('change', function() {
        var val = $(this).val();
        table.column(3).search(val ? val : '').draw();
    });

    // Status Filter Dropdown (Column 5)
    $('#filter_status').on('change', function() {
        var val = $(this).val();
        table.column(5).search(val ? val : '').draw();

        // Sync with quick tabs
        $('.usr-tab-pill').removeClass('active');
        if (val === 'Active') {
            $('.usr-tab-pill[data-filter="active"]').addClass('active');
        } else if (val === 'Inactive') {
            $('.usr-tab-pill[data-filter="inactive"]').addClass('active');
        } else {
            $('.usr-tab-pill[data-filter="all"]').addClass('active');
        }
    });

    // Page Length Selector
    $('#filter_page_length').on('change', function() {
        var len = parseInt($(this).val(), 10);
        table.page.len(len).draw();
    });

    // Reset All Filters Button
    $('#btn_reset_filters').on('click', function() {
        $('#user_table_search').val('');
        $('#user_search_clear').hide();
        $('#filter_role').val('');
        $('#filter_branch').val('');
        $('#filter_status').val('');
        $('#filter_page_length').val('10');

        $('.usr-tab-pill').removeClass('active');
        $('.usr-tab-pill[data-filter="all"]').addClass('active');

        table.search('').columns().search('').page.len(10).draw();
    });

    // ==========================================================================
    // 4. PASSWORD VISIBILITY TOGGLE
    // ==========================================================================
    $(document).on('click', '.usr-password-toggle', function() {
        var targetSelector = $(this).data('target');
        var input = $(targetSelector);
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ==========================================================================
    // 5. USER ADD SUBMISSION VIA AJAX
    // ==========================================================================
    $('#useradd').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');

        var submitBtn = $('#btn_save_user');
        var originalBtnHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var formData = new FormData(this);

        $.ajax({
            url: 'useradd',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (response.status == 0) {
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                } else {
                    toastr.success(response.message || 'User created successfully');
                    $('#useradd')[0].reset();
                    $('.error-text').text('');
                    $('#userModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                    toastr.error("Please correct the form errors indicated in red.");
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Something went wrong while adding user.";
                    toastr.error(msg);
                }
            }
        });
    });

    // ==========================================================================
    // 6. EDIT USER MODAL OPEN & POPULATE
    // ==========================================================================
    $(document).on('click', '.edituserBtn', function () {
        $('.error-text').text('');

        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var userCode = $(this).data('user-code');
        var role = $(this).data('role');
        var branch = $(this).data('branch');
        var status = $(this).data('status');
        var image = $(this).data('image');
        var mobile = $(this).data('mobile');
        var isSelf = $(this).data('is-self') == '1';

        $('#editid').val(id);
        $('#editname').val(name);
        $('#editemail').val(email);
        $('#edituser_code').val(userCode || '');
        $('#editrole').val(role).trigger('change');
        $('#editbranch').val(branch).trigger('change');
        $('#editstatus').val(status !== undefined ? status : 1).trigger('change');
        $('#editmobile_num').val(mobile);
        $('#editpassword').val('');
        $('#editimage').val('');

        if (isSelf) {
            $('#editrole').css('pointer-events', 'none').css('background-color', '#e2e8f0').attr('tabindex', '-1');
            $('#self_role_warning').removeClass('d-none');
        } else {
            $('#editrole').css('pointer-events', '').css('background-color', '').removeAttr('tabindex');
            $('#self_role_warning').addClass('d-none');
        }

        if (image && image !== 'undefined' && image !== 'null') {
            $('#profileImage').attr('src', '/uploads/users/' + image);
        } else {
            $('#profileImage').attr('src', '/images/user.jpg');
        }

        $('#usereditModal').modal('show');
    });

    // ==========================================================================
    // 7. USER EDIT SUBMISSION VIA AJAX
    // ==========================================================================
    $('#useredit').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');

        var submitBtn = $('#btn_update_user');
        var originalBtnHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        var formData = new FormData(this);

        $.ajax({
            url: 'useredit',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (response.status == 0) {
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            $('.edit_' + key + '_error').text(value[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                } else {
                    toastr.success(response.message || 'User updated successfully');
                    $('#useredit')[0].reset();
                    $('.error-text').text('');
                    $('#usereditModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.edit_' + key + '_error').text(value[0]);
                    });
                    toastr.error("Please correct the form errors indicated in red.");
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Something went wrong while updating user.";
                    toastr.error(msg);
                }
            }
        });
    });

    // ==========================================================================
    // 8. DELETE USER WITH CONFIRMATION DIALOG
    // ==========================================================================
    $(document).on('click', '.deleteuserBtn', function () {
        var id = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Deactivate / Delete User?',
            text: "Are you sure you want to delete this user account? This action cannot be reversed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0F1E4D',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, Delete Account',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-primary px-4',
                cancelButton: 'btn btn-secondary px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'userdelete',
                    type: 'POST',
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 1) {
                            toastr.success(response.message || 'User deleted successfully');
                            table.row(button.closest('tr')).remove().draw();

                            // Update Total KPI count on page
                            var totalElem = $('#kpi_total_users');
                            if (totalElem.length) {
                                var cur = parseInt(totalElem.text(), 10) || 0;
                                if (cur > 0) totalElem.text(cur - 1);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete User',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred while deleting the user.';
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
