$(document).ready(function () {
    setflip();

    $('.home-banner').slick({
        autoplay: true,
        arrows: false,
        dots: true,
        listHeight: '400px',
        customPaging: function () {
            return '<a class="slide-a" href="javascript:void(0)" tabindex="0"><span class="slide-dot"></span><span class="slide-dot-active"></span></a>';
        }
    });

    $('.slide-product-home').slick({
        arrows: true,
        dots: false,
        slidesToShow: 4,
        slidesToScroll: 4,
        prevArrow: '<a href="javascript:void(0)" class="slide-prev" aria-label="Previous" tabindex="0"><i class="icon ion-chevron-left"></i></a>',
        nextArrow: '<a href="javascript:void(0)" class="slide-next" aria-label="Next" tabindex="0"><i class="icon ion-chevron-right"></i></a>',
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 3,
                    slidesToScroll: 3
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
});