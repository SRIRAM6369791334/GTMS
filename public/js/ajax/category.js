$(document).ready(function() {
  var table = $('#example10').DataTable();

  $('#cateadd').on('submit', function (e) {

    e.preventDefault();

    $('.error-text').text('');

    $.ajax({
        url: 'categoryadd',
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
                    response.data.cat_code,
                    response.data.cat_name,

                    '<button type="button" class="btn btn-primary editcategoryBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-code="' + response.data.cat_code + '" data-name="' + response.data.cat_name + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deletecategoryBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#cateadd')[0].reset();
                $('#catModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });
  });

  $('.editcategoryBtn').on('click', function () {
    var id = $(this).data('id');
    var code = $(this).data('code');
    var name = $(this).data('name');

    $('#editcatModal').modal('show');
    $('#editcat_id').val(id);
    $('#editcat_code').val(code);
    $('#editcat_name').val(name);
  });


  $('#catedit').on('submit', function (e) {
    e.preventDefault();

    $('.error-text').text('');

    $.ajax({
        url: 'categoryedit',
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
                    response.data.cat_code,
                    response.data.cat_name,
                    '<button type="button" class="btn btn-primary editcategoryBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '" data-code="' + response.data.cat_code + '" data-name="' + response.data.cat_name + '">' +
                        '<i class="fa fa-pencil"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger deletecategoryBtn shadow btn-xs sharp me-1" data-id="' + response.data.id + '">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>'
                ]).draw(false);

                $('#catedit')[0].reset();
                $('#editcatModal').modal('hide');
            }

        },

        error: function () {
            toastr.error("Something went wrong.");
        }

    });
  });

  $(document).on('click', '.deletecategoryBtn', function () {

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
                url: 'categorydelete',
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
