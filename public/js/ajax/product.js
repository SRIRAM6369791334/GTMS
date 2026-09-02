$(document).ready(function() {


     var table = $('#example3').DataTable();




$('#productadd').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: '/productadd',
        data: formData,
        contentType: false,
        processData: false,
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
    response.data.branch.branch_name,
    response.data.bar_code,
    response.data.pro_name,
    response.data.gst,
    response.data.cast_per,
    response.data.mrp,
    response.data.unit,
    response.data.qty,
    response.data.discount_1,
    response.data.discount_2,
    response.data.discount_3,
    response.data.category.cat_code,
    response.data.category.cat_name,


    '<a href="#" class="btn btn-primary shadow btn-xs sharp me-1 editProductBtn" ' +
    'data-id="' + response.data.id + '" ' +
    'data-branch="' + response.data.branch_id + '" ' +
    'data-cat="' + response.data.cat_id + '" ' +
    'data-name="' + response.data.pro_name + '" ' +
    'data-gst="' + response.data.gst + '" ' +
    'data-cast-per="' + response.data.cast_per + '" ' +
    'data-mrp="' + response.data.mrp + '" ' +
    'data-unit="' + response.data.unit + '" ' +
    'data-qty="' + response.data.qty + '" ' +
    'data-discount-1="' + response.data.discount_1 + '" ' +
    'data-discount-2="' + response.data.discount_2 + '" ' +
    'data-discount-3="' + response.data.discount_3 + '">' +
    '<i class="fas fa-pencil-alt"></i>' +
    '</a> ' +

    '<a href="#" class="btn btn-danger shadow btn-xs sharp deleteProductBtn" ' +
    'data-id="' + response.data.id + '">' +
    '<i class="fa fa-trash"></i>' +
    '</a>'
]).draw(false);

                $('#productadd')[0].reset();
                $('#productModal').modal('hide');
            }

        },
        error: function () {
            toastr.error("Something went wrong.");
        }

    });
});


});
