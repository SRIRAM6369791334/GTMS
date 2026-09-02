$(document).ready(function() {
  var table = $('#example10').DataTable();

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


        success: function (response) {

            if (response.status == 0) {

                $.each(response.errors, function (key, value) {
                    $('.' + key + '_error').text(value[0]);
                });

            } else {

                toastr.success(response.message);

                var sno = table.rows().count() + 1;

                  var image = '';

                if(response.data.image != null){

                    image = '<img src="/uploads/users/'+response.data.image+'" width="40" height="40" class="rounded-circle">';

                }else{

                    image = '<img src="/uploads/users/default.png" width="40" height="40" class="rounded-circle">';

                }

                // Status Badge
                var status = response.data.status == 1
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';

                table.row.add([
                    sno,
                    image,
                    response.data.user_code,
                    response.data.name,
                    response.data.role.name,
                    response.data.branch.branch_name,
                    response.data.mobile_num,
                    response.data.email,
                    status,
                    '<button type="button" class="btn btn-primary edituserBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-name="' + response.data.name + '" data-email="' + response.data.email + '" data-role="' + response.data.role_id + '" data-branch="' + response.data.branch_id + '" data-status="' + response.data.status + '" data-image="' + response.data.image + '"  data-mobile="' + response.data.mobile_num + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deleteuserBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#useradd')[0].reset();
                $('#userModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });

  });

  $('.edituserBtn').on('click', function () {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var email = $(this).data('email');
    var role = $(this).data('role');
    var branch = $(this).data('branch');
    var status = $(this).data('status');
    var image = $(this).data('image');
    var mobile = $(this).data('mobile');
    var password = $(this).data('password');

    $('#editid').val(id);
    $('#editname').val(name);
    $('#editemail').val(email);
  $('#editrole').val(role).trigger('change');
$('#editbranch').val(branch).trigger('change');
    $('#editstatus').val(status);
    $('#editmobile_num').val(mobile);
    $('#editpassword').val(password);
    if(image != null){
        $('#profileImage').attr('src', '/uploads/users/' + image);
    }else{
        $('#profileImage').attr('src', '/uploads/users/default.png');
    }

    $('#usereditModal').modal('show');

  });

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

        success: function (response) {

            if (response.status == 0) {

                $.each(response.errors, function (key, value) {
                    $('.' + key + '_error').text(value[0]);
                });

            } else {

                toastr.success(response.message);

                // Update the row in the DataTable
                var row = table.row($('button[data-id="' + response.data.id + '"]').closest('tr'));
                var image = response.data.image ? '<img src="/uploads/users/' + response.data.image + '" width="40" height="40" class="rounded-circle">' : '<img src="/uploads/users/default.png" width="40" height="40" class="rounded-circle">';
                var status = response.data.status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';

                row.data([
                    row.data()[0], // Keep the same serial number
                    image,
                    response.data.user_code,
                    response.data.name,
                    response.data.role.name,
                    response.data.branch.branch_name,
                    response.data.mobile_num,
                    response.data.email,
                    status,
                    '<button type="button" class="btn btn-primary edituserBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-name="' + response.data.name + '" data-email="' + response.data.email + '" data-role="' + response.data.role_id + '" data-branch="' + response.data.branch_id + '" data-status="' + response.data.status + '" data-image="' + response.data.image + '"  data-mobile="' + response.data.mobile_num + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deleteuserBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#useredit')[0].reset();
                $('#usereditModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });

  });

  $(document).on('click', '.deleteuserBtn', function () {

    var id = $(this).data('id');

    alert(id);
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
