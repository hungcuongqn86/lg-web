function addOrderRow() {
    var row = '<tr class="row-cart-item" pid="' + pid + '" cid="' + cid + '">';
    row += '<td class="hidden-xs"><div class="hidden-xs">';
    row += '<img class="image-stack-nav-item-thumb display-desktop img-responsive" src="' + p_list[pid]['variants'][cid][1][0].replace(".png", "_s.png") + '" onerror="this.src=\'' + p_list[pid]['variants'][cid][1][0] + '\'" alt="' + p_list[pid]['variants'][cid][0] + '">';
    row += '</div></td>';

    row += '<td><div class="precheckout-items-table-item"><select style="padding: 0;" class="form-select-menu style-select form-control select-order-variant" name="order-style[]">';
    for (var key in p_list) {
        row += '<optgroup label="' + p_list[key]['name'] + '">';
        for (var key2 in p_list[key]['variants']) {
            if (key == pid && key2 == cid)
                row += '<option pid="' + key + '" cid="' + key2 + '" value="' + key + '#' + key2 + '#' + p_list[key]['variants'][key2][0] + '" selected="selected">' + p_list[key]['variants'][key2][0] + '</option>';
            else
                row += '<option pid="' + key + '" cid="' + key2 + '" value="' + key + '#' + key2 + '#' + p_list[key]['variants'][key2][0] + '">' + p_list[key]['variants'][key2][0] + '</option>';
        }
        row += '</optgroup>';
    }

    row += '</select></div></td>';
    row += '<td style="min-width: 55px;"><div class="precheckout-items-table-item-size">';
    if (p_list[pid]['sizes'].length) {
        row += '<a class="tooltip-form tooltip-size" data-toggle="tooltip" title="Choose your size"><select style="padding: 0;" class="size-select form-control" name="order-size[]"><option value="--">--</option>';
        $.each(p_list[pid]['sizes'], function (i, item) {
            if (sizeval === item[0]) {
                row += '<option selected="selected" value="' + item[0] + '">' + item[1] + '</option>';
            } else {
                row += '<option value="' + item[0] + '">' + item[1] + '</option>';
            }
        });
        row += '</select></a>';
    }
    else {
        row += '<div><input type="hidden" name="order-size[]" value=""/>No size</div>';
    }
    row += '</div></td>';
    row += '<td><div class="qty"><div class="selecttor"><span class="qty-minus" data-content="{{$campaign_select->price}}"><i class="ion-minus-round"></i></span><input type="number" min=1 name="order-quantity[]" class="quantity-input" title="qty value" data-content="' + p_list[pid]['price'] + '" value="' + $('#qty-value').val() + '" min="1"><span class="qty-plus"><i class="ion-plus-round"></i></span></div></div></td>';

    row += '<td><div class="precheckout-items-table-item-price"><p class="price-value" style="margin:0"><b>$'
        + ( ( parseFloat(p_list[pid]['price']) * parseInt($('#qty-value').val()))).toFixed(2) + '</b></p><a href="javascript:void(0);" class="precheckout-item-remove-button">' + remove_text + '</a></div></td></tr>';
    $('#table-list-cart').append(row);

    $('.row-cart-item:last').find('select').selectctr({css: {'width':'100%','height': '35px', 'z-index': 'initial'}});

    if ($('.row-cart-item').length == 1) {
        $('.row-cart-item').find('.precheckout-item-remove-button').hide();
    }
    else {
        $('.row-cart-item').find('.precheckout-item-remove-button').show();
    }

    $('.precheckout-item-remove-button').click(function () {
        $(this).closest('tr').remove();
        if ($('.row-cart-item').length == 1) {
            $('.row-cart-item').find('.precheckout-item-remove-button').hide();
        }
        else {
            $('.row-cart-item').find('.precheckout-item-remove-button').show();
        }
        showOtherLikeItem();
    });

    $('.select-order-variant').change(function () {
        pid = $(this).find(":selected").attr('pid');
        cid = $(this).find(":selected").attr('cid');
        var row = $(this).closest('tr');
        row.attr('pid', pid);
        row.attr('cid', cid);
        changeOrderRow(row);
    });

    $('.size-select').change(function () {
        if ($(this).val() === '--') {
            $(this).closest('a').tooltip('show');
        }
        else {
            $(this).closest('a').tooltip('destroy');
        }
    });

    $('.qty-minus').unbind("click").bind("click", function () {
        var qtyval = parseInt($(this).next().val());
        if (qtyval > 1) {
            qtyval = qtyval - 1;
            $(this).next().val(qtyval).trigger('change');
        }
    });

    $('.qty-plus').unbind("click").bind("click", function () {
        var qtyval = parseInt($(this).prev().val());
        qtyval = qtyval + 1;
        $(this).prev().val(qtyval).trigger('change');
    });

    $('.quantity-input').change(function () {
        var qtyval = parseInt($(this).val());
        var price = $(this).attr('data-content');
        var pricevalue = qtyval * price;
        $(this).closest('td').next().find('.price-value').html('<b>$' + pricevalue.toFixed(2) + '</b>');
    });

    showOtherLikeItem();
}

function changeOrderRow(row) {
    var img = row.find('img');
    img.attr("src", p_list[pid]['variants'][cid][1][0].replace(".png", "_s.png"));
    img.attr("onerror", "this.src='" + p_list[pid]['variants'][cid][1][0] + "'");
    img.attr("alt", p_list[pid]['variants'][cid][0]);

    var curr_size = row.find('.size-select');
    if (curr_size) var cur_select = curr_size.val();
    var div_size = row.find('.precheckout-items-table-item-size');
    div_size.empty();
    if (p_list[pid]['sizes'].length) {

        var size = '<a class="tooltip-form tooltip-size" data-toggle="tooltip" title="Choose your size"><select class="size-select form-control change-select" name="order-size[]"><option value="--">--</option>';
        $.each(p_list[pid]['sizes'], function (i, item) {
            if (cur_select && item[0] == cur_select)
                size += '<option value="' + item[0] + '" selected="selected">' + item[1] + '</option>';
            else
                size += '<option value="' + item[0] + '">' + item[1] + '</option>';
        });
        size += '</select></a>';
    }
    else {
        var size = '<div><input type="hidden" name="order-size[]" value=""/>No size</div>';
    }
    div_size.append(size);
    $('.change-select').selectctr({css: {'width':'100%','height': '35px', 'z-index': 'initial'}});
    $('.change-select').removeClass('change-select');

    var price = row.find('p');
    price.html('<b>$'+p_list[pid]['price']+'</b>');
}

function showOtherLikeItem() {
    var arr_pid = [];
    $.each($('.row-cart-item'), function (i, item) {
        arr_pid.push($(this).attr('pid'));
    });

    var check_also_like = 0;
    $.each($('.content-also-like-list'), function (i, item) {
        if ($.inArray($(this).attr('pid'), arr_pid) == -1) {
            check_also_like = 1;
            $('.content-also-like').hide();
            $(this).parent().show();
            return false;
        }
    });

    if (check_also_like == 0) {
        $('#item-like-other').hide();
        $('#item-like-other-title').hide();
    }
    else {
        $('#item-like-other').show();
        $('#item-like-other-title').show();
    }
}

function setupDetailPage() {
    $('#sl-product').val(pid);

    $('.li-variant').removeClass('active').each(function (i, item) {
        if ($(this).attr('pid') === pid) {
            $(this).addClass('active');
            if (!cid) cid = $(this).attr('cid');
        }
    });

    $('.variant-list').each(function () {
        if ($(this).attr('id') === cid) {
            $(this).show();
            var li = $(this).children();
            $(li).removeClass('active');
            var src = '';
            if ($(li).length <= sid) {
                sid = 0;
            }

            src = $(li).eq(sid).addClass('active').children('img').attr('data-src');
            var preview = $('#img-preview');

            $(preview).fadeOut(250, function () {
                $(this).hide();
                $(this).attr('data-zoo-image', src);
                $(this).empty().ZooMove({
                    cursor: 'true',
                    scale: '2'
                });
                $(this).show();
            }).fadeIn(0);

            $('.arrow-prev').unbind("click").bind("click", function () {
                if (sid > 0) {
                    sid--;
                } else {
                    sid = li.length - 1;
                }
                setupDetailPage();
            });
            $('.arrow-next').unbind("click").bind("click", function () {
                if (sid < li.length - 1) {
                    sid++;
                } else {
                    sid = 0;
                }
                setupDetailPage();
            });
        } else {
            $(this).hide();
        }
    });

    $('.ul-variant-color').each(function () {
        if ($(this).attr('id') === pid) {
            $(this).show();
            var li = $(this).children();
            $(li).removeClass('active').each(function () {
                if ($(this).attr('id') === cid) {
                    $(this).addClass('active');
                }
            });
        } else {
            $(this).hide();
        }
    });

    $('.ul-variant-size').each(function () {
        if ($(this).attr('id') === pid) {
            $(this).show();
            var li = $(this).children();
            $(li).unbind("click").bind("click", function () {
                $(li).removeClass('active');
                $(this).addClass('active');
                sizeval = $(this).attr('id');
            });
        } else {
            $(this).hide();
        }
    });
}

function seturl() {
    var url = '';
    var catid = getURLParams('catid');
    if (catid) url += '?catid=' + catid;
    url += (catid) ? '&pid=' + pid : '?pid=' + pid;
    url += '&cid=' + cid;
    url += '&sid=' + sid;
    window.history.replaceState(null, null, url);
}

function priceCacul(qty, price) {
    var pricevalue = qty * price;
    $('#price-value').text('$' + pricevalue.toFixed(2));
}

$(document).ready(function () {
    //select box
    $('#sl-product').selectctr({css: {'width': '100%', 'height': '60px', 'margin': '10px 0', 'z-index': 'initial'}});

    if (typeof(pid) != "undefined" && pid !== null && typeof(cid) != "undefined" && cid !== null) {
        fbq('track', 'ViewContent', {
            url: campaign_url,
            content_name: p_list[pid]['variants'][cid][0][0],
            content_category: category,
            content_ids: [cid],
            content_type: 'product',
            price: p_list[pid]['price'].substring(1),
            currency: 'USD'
        });
    }

    //CAMPAIGN DETAIL
    var time = parseInt($('.time-campaign').attr('data-seconds-left'));
    if (time <= 0) {
        $('#p-day').text('00');
        $('#p-hour').text('00');
        $('#p-minute').text('00');
        $('#p-second').text('00');

        $('#p-day').css('color', 'red');
        $('#p-hour').css('color', 'red');
        $('#p-minute').css('color', 'red');
        $('#p-second').css('color', 'red');
    }
    else {
        var x = setInterval(function () {

            time = time - 1000;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(time / (1000 * 60 * 60 * 24));
            var hours = Math.floor((time % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((time % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((time % (1000 * 60)) / 1000);

            if (days < 10) days = "0" + days;
            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;
            if (seconds < 10) seconds = "0" + seconds;
            $('#p-day').text(days);
            $('#p-hour').text(hours);
            $('#p-minute').text(minutes);
            $('#p-second').text(seconds);

            // If the count down is finished, write some text
            if (time <= 0) {
                clearInterval(x);
                $('#p-day').css('color', 'red');
                $('#p-hour').css('color', 'red');
                $('#p-minute').css('color', 'red');
                $('#p-second').css('color', 'red');
            }
        }, 1000);
    }

    $('.btn-buy').click(function () {
        $('.row-cart-item').remove();
        fbq('track', 'BuyItNow', {
            url: campaign_url,
            content_name: p_list[pid]['variants'][cid][0][0],
            content_category: category,
            content_ids: [cid],
            content_type: 'product',
            price: p_list[pid]['price'].substring(1),
            currency: 'USD'
        });
        addOrderRow();
        $('#myModal').modal('show')
    });

    $('#a-add-order-row').click(function () {
        addOrderRow();
    });

    $('.btn-add-orther').click(function () {
        pid = $(this).parent().parent().attr('pid');
        cid = $(this).parent().parent().attr('cid');
        addOrderRow();
        event.preventDefault();
    });

    $('#form-submit-to-cart').submit(function (event) {
        var check_submit = true;
        $.each($('.size-select'), function (i, item) {
            if ($(this).val() == '--') {
                check_submit = false;
                $(this).closest('a').tooltip('show');
            }
        });
        $.each($('.quantity-input'), function (i, item) {
            if ($(this).val() <= 0) {
                check_submit = false;
                $(this).closest('a').tooltip('show');
            }
        });
        if (check_submit == true) {
            var content_ids = [];
            var subtotal = 0;
            $.each($('.row-cart-item'), function (i, item) {
                content_ids.push($(this).attr('cid'));
            });
            $.each($('.precheckout-items-table-item-price'), function (i, item) {
                subtotal += parseFloat($(this).find('p').text().match(/[+-]?\d+(\.\d+)?/g)[0]);
            });
            fbq('track', 'AddToCart', {
                url: campaign_url,
                content_name: campaign,
                content_category: category,
                content_ids: content_ids,
                content_type: 'product',
                price: Math.round(subtotal, 2),
                currency: 'USD'
            });

            $('#myModal').modal('hide');
            $('.loader').show();
            $('.loader-modal').show();
        }
        else
            event.preventDefault();
    });

    if ($('#pr_code').val()) {
        $.ajax({
            url: '/checkpromotion',
            type: "GET",
            dataType: 'json',
            data: {
                code: $('#pr_code').val(),
                campaign: $('#camp_id').val()
            },
            success: function (data) {
                if (data && data.id) {
                    $('#promotion-modal').modal('show');
                    $('.div-promotion-alert').show();
                    if (data.type.name == 'discount') {
                        var oldval = parseFloat($('#price-value').attr('data-content'));
                        if (data.discount.type == 'FIX') {
                            $('.promotion-value').text(data.discount.value + '$ off');
                            $('.save-value').text(data.discount.value + '$');
                            $('#price-value').text('$' + (oldval - parseFloat(data.discount.value)).toString());
                        }
                        else {
                            $('.promotion-value').text(data.discount.value + '% off');
                            $('.save-value').text(data.discount.value + '%');
                            var proval = data.discount.value * oldval / 100;
                            $('#price-value').text('$' + (oldval - proval).toFixed(2));
                        }
                    }
                    else {
                        $('.promotion-value').text('freeship');
                        //$('.save-value').text('freeship');
                        $('.old-price').hide();
                        $('.save').hide();
                    }
                }
                else {
                    document.cookie = "pr_code=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                }
            }
        });
    }

    $('.togglerDesc').bind("click", function () {
        if ($(this).hasClass('ion-plus-round')) {
            $(this).removeClass('ion-plus-round').addClass('ion-minus-round');
        } else {
            $(this).removeClass('ion-minus-round').addClass('ion-plus-round');
        }
        var conten = $(this).parent().next();
        $(conten).toggle();
    });

    $('.mockup-item').bind("click", function () {
        var li = $(this).parent().children();
        sid = $(li).index(this);
        setupDetailPage();
        seturl();
    });

    $('.preview').bind("mouseover", function () {
        $('.preview-arrow').show();
    }).bind("mouseout", function () {
        $('.preview-arrow').hide();
    }).bind("touchstart", function () {
        $('.preview-arrow').show();
    });


    $('.qty-minus').unbind("click").bind("click", function () {
        var qtyval = parseInt($(this).next().val());
        if (qtyval > 1) {
            qtyval = qtyval - 1;
            $(this).next().val(qtyval).trigger('change');
        }
    });

    $('.qty-plus').unbind("click").bind("click", function () {
        var qtyval = parseInt($(this).prev().val());
        qtyval = qtyval + 1;
        $(this).prev().val(qtyval).trigger('change');
    });

    $('#qty-value').bind("change", function () {
        var qtyval = parseInt($(this).val());
        var price = $(this).attr('data-content');
        priceCacul(qtyval, price);
    });

    $('.li-variant').bind("click", function () {
        pid = $(this).attr('pid');
        cid = $(this).attr('cid');
        setupDetailPage();
        seturl();
    });

    $('.product-color-list-item').click(function () {
        cid = $(this).attr('id');
        setupDetailPage();
        seturl();
    });

    $('#sl-product').change(function () {
        pid = $(this).val();
        $('.li-variant').each(function () {
            if (pid === $(this).attr('pid')) {
                $(this).trigger('click');
                return false;
            }
        });
    });

    $('.facebook').click(function (event) {
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

    $('.twitter').click(function (event) {
        var shareurl = 'http://twitter.com/share';
        var url = shareurl + '?url=' + window.location.toString().split('?')[0];
        var width = 575,
            height = 400,
            left = ($(window).width() - width) / 2,
            top = ($(window).height() - height) / 2,
            opts = 'status=1' +
                ',width=' + width +
                ',height=' + height +
                ',top=' + top +
                ',left=' + left;
        window.open(url, 'twitter', opts);
        return false;
    });

    $('.list-style-group-detail').slick({
        arrows: false,
        slidesToShow: 5,
        dots: false,
        infinite: false,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    centerMode: false,
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    centerMode: false,
                    slidesToShow: 3
                }
            }
        ]
    });

    var preview = document.getElementById('image-product-preview');
    var mc = new Hammer(preview);
    mc.on("swipeleft", function (ev) {
        $('.arrow-prev').trigger('click');
    }).on("swiperight", function (ev) {
        $('.arrow-next').trigger('click');
    });

    setupDetailPage();
});