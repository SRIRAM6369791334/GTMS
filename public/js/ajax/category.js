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

                    '<div class="table-action-group">' +
                        '<button type="button" class="btn-action-icon btn-action-edit editcategoryBtn" data-id="' + response.data.id + '" data-code="' + response.data.cat_code + '" data-name="' + response.data.cat_name + '" title="Edit">' +
                            '<i class="fa fa-pencil"></i>' +
                        '</button> ' +
                        '<button type="button" class="btn-action-icon btn-action-delete deletecategoryBtn" data-id="' + response.data.id + '" title="Delete">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>' +
                    '</div>'
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
                    '<div class="table-action-group">' +
                        '<button type="button" class="btn-action-icon btn-action-edit editcategoryBtn" data-id="' + response.data.id + '" data-code="' + response.data.cat_code + '" data-name="' + response.data.cat_name + '" title="Edit">' +
                            '<i class="fa fa-pencil"></i>' +
                        '</button> ' +
                        '<button type="button" class="btn-action-icon btn-action-delete deletecategoryBtn" data-id="' + response.data.id + '" title="Delete">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>' +
                    '</div>'
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
        text: "You won't be able to recover this category!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#0F1E4D',
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
