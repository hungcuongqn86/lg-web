setflip = function () {
    $('.img-feature').bind("mouseenter", function () {
        var imgfront = $(this).find('img.img-front');
        var imgback = $(this).find('img.img-back');
        $(imgfront).stop(true).removeClass('img-flip-front').addClass('img-flip-front-back').css('opacity', 1);
        $(imgfront).fadeOut(250, function () {
            $(imgback).stop(true).hide().css('opacity', 1);
            $(imgback).removeClass('img-flip-back-front').addClass('img-flip-back').show();
            $(this).removeClass('img-flip-front-back').addClass('img-flip-front');
        });
    }).bind("mouseleave", function () {
        var imgback = $(this).find('img.img-back');
        var imgfront = $(this).find('img.img-front');
        $(imgback).stop(true).removeClass('img-flip-back').addClass('img-flip-back-front').css('opacity', 1);
        $(imgback).fadeOut(250, function () {
            $(imgfront).stop(true).css('opacity', 1).hide();
            $(imgfront).removeClass('img-flip-front-back').addClass('img-flip-front').show();
            $(this).removeClass('img-flip-back-front').addClass('img-flip-back');
        });
    });
};

getURLParams = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

reportAnalytics = function () {
    var getUrl = window.location;
    var url = getUrl.protocol + "//" + getUrl.host + '/reportAnalytics';
    $.ajax({
        url: url,
        type: "GET"
    });
};

//web js
$(document).ready(function () {
    //TRACK ORDER
    $('#form-submit-track-order').submit(function (event) {
        if (!$.trim($('#lookup_number').val())) {
            event.preventDefault();
        }
    });

    $(".ico-search-mobile").click(function () {
        $(".search-form-mobile").css({'left': '0', 'display': 'block'}).find('input').focus();
        $(".site-search-overlay").show();
    });
    $(".search-form-mobile").focusout(function () {
        $(".search-form-mobile").css({'left': '100vw', '': ''});
        $(".site-search-overlay").hide();
    });

    $('.btn-loading-progess').click(function () {
        $(this).find('.loading-progess').css('width', '100%');
    });

    $('.price-show-ci').each(function () {
        var value = $(this).attr('data-content');
        this.innerHTML = '$';
        var arrvalue = value.split('.');
        var fSpan = document.createElement('span');
        fSpan.innerHTML = arrvalue[0];
        this.appendChild(fSpan);
        var sSpan = document.createElement('span');
        if (arrvalue[1] && parseInt(arrvalue[1]) > 0) {
            sSpan.innerHTML = arrvalue[1];
        }
        this.appendChild(sSpan);
    });

    reportAnalytics();
});

