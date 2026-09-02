$(document).ready(function() {
      var table = $('#example10').DataTable();

     $('#brancheadd').on('submit', function (e) {

    e.preventDefault();

    $('.error-text').text('');

    $.ajax({
        url: 'branchadd',
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

                var sno = table.rows().count() + 1;

                // Status Badge
                var status = response.data.status == 1
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';

                table.row.add([
                    sno,
                    response.data.branch_name,
                    response.data.contact_person,
                    response.data.mobile,
                    response.data.address,

                    status,
                    '<button type="button" class="btn btn-primary editbranchBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-name="' + response.data.branch_name + '" data-contact="' + response.data.contact_person + '" data-mobile="' + response.data.mobile + '" data-address="' + response.data.address + '" data-city="' + response.data.city + '" data-state="' + response.data.state + '" data-pincode="' + response.data.pincode + '" data-status="' + response.data.status + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deletebranchBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#brancheadd')[0].reset();
                $('#branchModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });

});

$('.editbranchBtn').on('click', function () {
    var id = $(this).data('id');
    var branch_name = $(this).data('name');
    var contact_person = $(this).data('contact');
    var mobile = $(this).data('mobile');
    var address = $(this).data('address');
    var city = $(this).data('city');
    var state = $(this).data('state');
    var pincode = $(this).data('pincode');
    var status = $(this).data('status');

    $('#brancheeditModal').modal('show');
    $('#editid').val(id);
    $('#editbranch_name').val(branch_name);
    $('#editcontact_person').val(contact_person);
    $('#editmobile').val(mobile);
    $('#editaddress').val(address);
    $('#editcity').val(city);
    $('#editstate').val(state);
    $('#editpincode').val(pincode);
    $('#editstatus').val(status);
});

$('#brancheedit').on('submit', function (e) {
    e.preventDefault();

    $('.error-text').text('');

    $.ajax({
        url: 'branchedit',
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

                // Update the row in the DataTable
                var row = table.row($('button.editbranchBtn[data-id="' + response.data.id + '"]').closest('tr'));
                var status = response.data.status == 1
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';

                row.data([
                    row.data()[0], // Keep the same serial number
                    response.data.branch_name,
                    response.data.contact_person,
                    response.data.mobile,
                    response.data.address,

                    status,
                    '<button type="button" class="btn btn-primary editbranchBtn" data-id="' + response.data.id + '" data-name="' + response.data.branch_name + '" data-contact="' + response.data.contact_person + '" data-mobile="' + response.data.mobile + '" data-address="' + response.data.address + '" data-city="' + response.data.city + '" data-state="' + response.data.state + '" data-pincode="' + response.data.pincode + '" data-status="' + response.data.status + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deletebranchBtn" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#brancheedit')[0].reset();
                $('#brancheeditModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });
});

$(document).on('click', '.deletebranchBtn', function () {

    var id = $(this).data('id');
    var button = $(this);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to recover this branch!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
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
