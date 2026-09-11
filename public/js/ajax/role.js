$(document).ready(function() {
    var table = $('#example10').DataTable();

    // Select All / Deselect All for Add Modal
    $('.select-all-add').on('click', function() {
        $('.perm-add-checkbox').prop('checked', true);
        $('.module-check-all-add').prop('checked', true);
    });

    $('.deselect-all-add').on('click', function() {
        $('.perm-add-checkbox').prop('checked', false);
        $('.module-check-all-add').prop('checked', false);
    });

    $('.module-check-all-add').on('change', function() {
        var targetClass = $(this).data('target');
        $('.' + targetClass).prop('checked', $(this).is(':checked'));
    });

    // Select All / Deselect All for Edit Modal
    $('.select-all-edit').on('click', function() {
        $('.perm-edit-checkbox').prop('checked', true);
        $('.module-check-all-edit').prop('checked', true);
    });

    $('.deselect-all-edit').on('click', function() {
        $('.perm-edit-checkbox').prop('checked', false);
        $('.module-check-all-edit').prop('checked', false);
    });

    $('.module-check-all-edit').on('change', function() {
        var targetClass = $(this).data('target');
        $('.' + targetClass).prop('checked', $(this).is(':checked'));
    });

    // Add Role Submit
    $('#rolesadd').submit(function (e) {
        e.preventDefault();
        $('.error-text').text('');

        $.ajax({
            url: 'roleadd',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);
                    $('#rolesadd')[0].reset();
                    $('.module-check-all-add').prop('checked', false);
                    $('#roleModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 800);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong.");
                }
            }
        });
    });

    // Edit Role button click -> fetch role permissions
    $(document).on('click', '.editBtn', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#role_id').val(id);
        $('#role_name').val(name);
        $('.perm-edit-checkbox').prop('checked', false);
        $('.module-check-all-edit').prop('checked', false);

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
        $('.error-text').text('');

        $.ajax({
            url: 'roleupdate',
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status == 0) {
                    $.each(response.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.success(response.message);
                    $('#roleeditModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 800);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong.");
                }
            }
        });
    });

    // Delete Role
    $(document).on('click', '.deleteBtn', function () {
        var id = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to recover this role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'Cancel'
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
                            table.row(button.parents('tr')).remove().draw();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                    }
                });
            }
        });
    });
});

