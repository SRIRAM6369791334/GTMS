$(document).ready(function() {
    var table = $('#example10').DataTable();

    // User Add Submit
    $('#useradd').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');
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
                if (response.status == 0) {
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                } else {
                    toastr.success(response.message);
                    $('#useradd')[0].reset();
                    $('.error-text').text('');
                    $('#userModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                    toastr.error("Please correct the form errors indicated in red.");
                } else {
                    toastr.error("Something went wrong while adding user.");
                }
            }
        });
    });

    // Edit User Button
    $(document).on('click', '.edituserBtn', function () {
        $('.error-text').text('');

        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var role = $(this).data('role');
        var branch = $(this).data('branch');
        var status = $(this).data('status');
        var image = $(this).data('image');
        var mobile = $(this).data('mobile');

        $('#editid').val(id);
        $('#editname').val(name);
        $('#editemail').val(email);
        $('#editrole').val(role).trigger('change');
        $('#editbranch').val(branch).trigger('change');
        $('#editstatus').val(status !== undefined ? status : 1).trigger('change');
        $('#editmobile_num').val(mobile);
        $('#editpassword').val('');

        if (image) {
            $('#profileImage').attr('src', '/uploads/users/' + image);
        } else {
            $('#profileImage').attr('src', '/images/user.jpg');
        }

        $('#usereditModal').modal('show');
    });

    // User Edit Submit
    $('#useredit').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');
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
                    $('#useredit')[0].reset();
                    $('.error-text').text('');
                    $('#usereditModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.edit_' + key + '_error').text(value[0]);
                    });
                    toastr.error("Please correct the form errors indicated in red.");
                } else {
                    toastr.error("Something went wrong while updating user.");
                }
            }
        });
    });

    // Delete User
    $(document).on('click', '.deleteuserBtn', function () {
        var id = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to recover this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete user!',
            cancelButtonText: 'Cancel'
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

