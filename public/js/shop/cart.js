$(document).ready(function () {
    // Change Button Save - Edit
    $('.cart-quantity-input').change(function () {
        var cid = $(this).attr('cid');
        $('#cart-btn-edit-' + cid).hide();
        $('#cart-btn-save-' + cid).show();
    });

    $('.qty-minus').bind("click", function () {
        var qtyval = parseInt($(this).next().val());
        if (qtyval > 1) {
            qtyval = qtyval - 1;
            $(this).next().val(qtyval).trigger('change');
        }
    });

    $('.qty-plus').bind("click", function () {
        var qtyval = parseInt($(this).prev().val());
        qtyval = qtyval + 1;
        $(this).prev().val(qtyval).trigger('change');
    });

    $('.cart-a-remove').click(function () {
        $(this).parents('.cart-line').remove();
        $('#form-submit-edit-cart').submit();
    });

    $('.cart-btn-save').click(function () {
        $('#form-submit-edit-cart').submit();
    });

    $('.cart-btn-edit').click(function (event) {
        event.preventDefault();
        $(this).parents('.cart-line').find('.change-select-cart').toggle(400);
    });
});