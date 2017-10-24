$(document).ready(function () {
    $('.share-store').click(function (event) {
        var shareurl = 'https://www.facebook.com/sharer/sharer.php';
        var url = shareurl + '?u=' + window.location.toString().split('?')[0];
        url = url + '&display=popup&ref=plugin&src=like&kid_directed_site=0';
        var width = 575,
            height = 400,
            left = ($(window).width() - width) / 2,
            top = ($(window).height() - height) / 2,
            opts = 'status=1' +
                ',width=' + width +
                ',height=' + height +
                ',top=' + top +
                ',left=' + left;
        window.open(url, 'facebook', opts);
        return false;
    });
});