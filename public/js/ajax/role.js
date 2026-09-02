$(document).ready(function() {
   var table = $('#example10').DataTable();

    // Add Role
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

                    var sno = table.rows().count() + 1;

                    table.row.add([
                        sno,
                        response.data.name,

                        '<button type="button" class="btn btn-primary editBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-name="' + response.data.name + '">' +
                        '<i class="fa fa-pencil"></i>' +
                        '</button> ' +

                        '<button type="button" class="btn btn-danger deleteBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                        '</button>'

                    ]).draw(false);

                    $('#rolesadd')[0].reset();

                    $('#roleModal').modal('hide');

                }

            },

            error: function () {

                toastr.error("Something went wrong.");

            }

        });

    });

    $('.editBtn').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#roleeditModal').modal('show');
        $('#role_id').val(id);
        $('#role_name').val(name);
    });


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

                    var row = table.row($('button[data-id="' + response.data.id + '"]').parents('tr'));
                    row.data([
                        row.data()[0],
                        response.data.name,

                        '<button type="button" class="btn btn-primary editBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-name="' + response.data.name + '">' +
                        '<i class="fa fa-pencil"></i>' +
                        '</button> ' +

                        '<button type="button" class="btn btn-danger deleteBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                        '</button>'

                    ]).draw(false);

                    $('#rolesedit')[0].reset();

                    $('#roleeditModal').modal('hide');

                }

            },

            error: function () {

                toastr.error("Something went wrong.");

            }

        });

    });
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
