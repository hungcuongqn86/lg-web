function setupSlide() {
    var widthselect2 = $('#categories-select').outerWidth() + 75;
    $('#sub-category').css({'width': 'calc(100% - ' + widthselect2 + 'px)'});

    $('.sub-category-list').slick({
        arrows: true,
        dots: false,
        initialSlide: slideindex,
        slidesToShow: 4,
        slidesToScroll: 4,
        prevArrow: '',
        nextArrow: '<a href="#" class="slide-next" aria-label="Next" tabindex="0"><i class="icon ion-chevron-right"></i></a>',
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            }
        ]
    });

    $('.sub-category-list-xs').slick({
        arrows: false,
        dots: false,
        initialSlide: slideindex,
        slidesToShow: 2,
        slidesToScroll: 2,
        prevArrow: '',
        nextArrow: '<a href="#" class="slide-next" aria-label="Next" tabindex="0"><i class="icon ion-chevron-right"></i></a>'
    });
}

$(document).ready(function () {
    setflip();
    /**
     * list
     */

    $('#select-category').val(categorySel).selectctr({
        arrow: '<span class="input-group-addon" style="font-size: 30px;" data-toggle="dropdown"><i class="ion-ios-close-empty" aria-hidden="true"></i></span>'
    }).bind("change", function (e) {
        location.href = '/shop' + $(this).val();
    });

    $('.sub-category-item').removeClass('active').each(function () {
        if ($(this).attr('id') === subcategorySel) {
            $(this).addClass('active');
        }
    }).bind("click", function () {
        if ($(this).attr('id')) {
            location.href = '/shop' + $(this).attr('id');
        }
    });

    $('.unselect').bind("click", function (e) {
        e.stopPropagation();
        location.href = '/shop' + categorySel;
    });

    setupSlide();

    $('#select-sort').val(sortval).selectctr().bind("change", function (e) {
        location.href = '/shop' + categorySel + '?sort=' + $(this).val();
    });
});