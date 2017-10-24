$(document).ready(function () {
    setflip();
    $('#select-sort').val(sortval).selectctr().bind("change", function (e) {
        location.href = '/shop/search?term=' + search + '&sort=' + $(this).val();
    });
});