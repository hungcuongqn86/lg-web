<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="author" content=""/>
    <meta name="robots" content="noindex"/>
    <meta name="googlebot" content="noindex"/>

    <meta property="og:site_name" content="BurgerPrints"/>
    <!--
    <meta property="twitter:account_id" content="273515759" />
    <meta property="fb:app_id" content="187750784648365"/>
    <meta property="fb:admins" content="1307820481"/>
    -->
    <meta property="og:type" content="product"/>
    <meta property="og:availability" content="instock"/>

    <meta name="twitter:domain" content="burgerprints.com"/>
    <meta name="twitter:site" content="@burgerprints"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:creator" content="@burgerprints"/>

    @if($site == 'home')
        <meta property="og:title" content="BurgerPrints"/>
        <meta property="og:image" content="http://burgerprints.com/images/logo-burger.png"/>
        <meta property="og:image:width" content="256"/>
        <meta property="og:image:height" content="32"/>
        <meta property="og:description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <!--<meta property="og:url" content="/">-->

        <meta name="twitter:description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <meta name="twitter:title" content="BurgerPrints"/>
        <meta name="twitter:image" content="http://burgerprints.com/images/logo-burger.png"/>

        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com"/>
    @elseif($site == 'list')
        <meta property="og:title"
              content="{{ $category_select->name }} T-shirts | Unique {{ $category_select->name }} Apparel | BurgerPrints"/>
        <meta property="og:image" content="http://burgerprints.com/images/logo-burger.png"/>
        <meta property="og:image:width" content="256"/>
        <meta property="og:image:height" content="32"/>
        <meta property="og:description"
              content="Choose your favorite {{ $category_select->name }} shirt from a wide variety of unique high quality designs in various styles, colors and fits. Shop online at BurgerPrints now!"/>
    <!--<meta property="og:url" content="/shop{{ $category_select->url }}">-->

        <meta name="twitter:description"
              content="Choose your favorite {{ $category_select->name }} shirt from a wide variety of unique high quality designs in various styles, colors and fits. Shop online at BurgerPrints now!"/>
        <meta name="twitter:title"
              content="{{ $category_select->name }} T-shirts | Unique {{ $category_select->name }} Apparel | BurgerPrints"/>
        <meta name="twitter:image" content="http://burgerprints.com/images/logo-burger.png"/>

        <meta name="description"
              content="Choose your favorite {{ $category_select->name }} shirt from a wide variety of unique high quality designs in various styles, colors and fits. Shop online at BurgerPrints now!"/>
        <title>{{ $category_select->name }} T-shirts | Unique {{ $category_select->name }} Apparel |
            BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/shop{{ $category_select->url }}"/>
    @elseif($site == 'detail')
        <meta property="og:title" content="{{ $campaign_select->title }} T-Shirt | BurgerPrints"/>
        @foreach($campaign_select->products as $prod_item)
            @if ($prod_item->id == $pid)
                <meta property="og:price:amount" content="{{ $prod_item->price }}"/>
                <meta property="og:price:currency" content="{{ $prod_item->currency }}"/>
                @foreach($prod_item->variants as $variant_item)
                    @if ($variant_item->id == $cid)
                        @if ($sid == 0)
                            <meta property="og:image"
                                  content="http://burgerprints.com/image/social?url=<?php echo urlencode($variant_item->image->front);?>"/>
                            <meta name="twitter:image"
                                  content="http://burgerprints.com/image/social?url=<?php echo urlencode($variant_item->image->front);?>"/>
                        @else
                            <meta property="og:image"
                                  content="http://burgerprints.com/image/social?url=<?php echo urlencode($variant_item->image->back);?>"/>
                            <meta name="twitter:image"
                                  content="http://burgerprints.com/image/social?url=<?php echo urlencode($variant_item->image->back);?>"/>
                        @endif
                    @endif
                @endforeach
            @endif
        @endforeach
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="1400"/>
        <meta property="og:description"
              content="Discover {{ $campaign_select->title }} T-Shirt, a custom product made just for you by BurgerPrints. With world-class production and customer support, your satisfaction is guaranteed."/>
    <!--<meta property="og:url" content="/shop{{ $campaign_select->url }}">-->

        <meta name="twitter:description"
              content="Discover {{ $campaign_select->title }} T-Shirt, a custom product made just for you by BurgerPrints. With world-class production and customer support, your satisfaction is guaranteed."/>
        <meta name="twitter:title" content="{{ $campaign_select->title }} T-Shirt | BurgerPrints"/>

        <meta name="description"
              content="Discover {{ $campaign_select->title }} T-Shirt, a custom product made just for you by BurgerPrints. With world-class production and customer support, your satisfaction is guaranteed."/>
        <title>{{ $campaign_select->title }} T-Shirt | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/shop{{ $campaign_select->url }}"/>
    @elseif($site == 'cart')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Shopping Cart | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/shop/cart"/>
    @elseif($site == 'checkout')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Checkout | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/shop/checkout"/>
    @elseif($site == 'search')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Search | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/shop/search?term={{ $search }}"/>
    @elseif($site == 'thankyou')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Payment Success | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com"/>
    @elseif($site == 'error')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Error page | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com"/>
    @elseif($site == 'track')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Track order | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/track"/>
    @elseif($site == 'term')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Terms | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/term"/>
    @elseif($site == 'privacy')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Privacy | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/privacy"/>
    @elseif($site == 'store')
        <meta name="description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <title>Store | BurgerPrints</title>
        <link rel="canonical" href="http://burgerprints.com/privacy"/>
    @endif

    @if($site == 'cart'||$site == 'checkout'||$site == 'search'||$site == 'thankyou'||$site == 'error'||$site == 'track'||$site == 'term'||$site == 'privacy'||$site == 'store')
        <meta property="og:title" content="BurgerPrints"/>
        <meta property="og:image" content="http://burgerprints.com/images/logo-burger.png"/>
        <meta property="og:image:width" content="256"/>
        <meta property="og:image:height" content="32"/>
        <meta property="og:description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <!--<meta property="og:url" content="/">-->

        <meta name="twitter:description"
              content="BurgerPrints makes it easier than ever to sell shirts you design, leveraging crowd funding and social media to help you sell your shirt and make money, all with absolutely no money down."/>
        <meta name="twitter:title" content="BurgerPrints"/>
        <meta name="twitter:image" content="http://burgerprints.com/images/logo-burger.png"/>
@endif

<!-- Bootstrap Core CSS -->
    <link href="{{asset('css/bootstrap.min.css?v=1.001')}}" rel="stylesheet"/>
    <link href="{{asset('css/ionicons.min.css?v=1.000')}}" rel="stylesheet"/>
    <!-- Font Import -->
    <link href="{{asset('css/font-awesome.min.css?v=1.000')}}" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat&v=1.000" rel="stylesheet"/>
    <link href="{{asset('plugin/slick/slick.min.css?v=1.000')}}" rel="stylesheet"/>
    <link href="{{asset('plugin/slick/slick-theme.min.css?v=1.000')}}" rel="stylesheet"/>
    <link href="{{asset('plugin/zoomove-master/dist/zoomove.min.css?v=1.000')}}" rel="stylesheet"/>
    <link href="{{asset('css/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.min.css?v=1.000')}}" rel="stylesheet"/>

    <!-- Custom CSS -->
    <link href="{{asset('css/mainstyle.css?v=1.049')}}" rel="stylesheet"/>

    <!--Favicon-->
    <link rel="icon" type="image/png" href="{{asset('images/logo-burger.png')}}"/>

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5BPG85V');</script>
    <!-- End Google Tag Manager -->

    <!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq)return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1508951712508536'); // Root facebook pixel id
        @if($site == 'detail' && $campaign_select->fb_pixel != '')
fbq('init', '{{ $campaign_select->fb_pixel }}');
        @endif
            fbq.disablePushState = true;
        @if($site != 'cart' && $site != 'checkout' && $site != 'error' && $site != 'thankyou')
fbq('track', 'PageView');
        @endif
        @if($site == 'checkout')
fbq('track', 'InitiateCheckout', {
            order_id: '{{ $order->id }}',
            amount: '{{ $order->amount }}',
            currency: 'USD'
        });
        @endif
        @if($site == 'thankyou')
fbq('track', 'Purchase', {
            order_id: '{{ $order->id }}',
            amount: '{{ $order->amount }}',
            currency: 'USD'
        });
        @endif
    </script>

    <!-- End Facebook Pixel Code -->

    <script type="text/javascript">
        setTimeout(function () {
            var a = document.createElement("script");
            var b = document.getElementsByTagName("script")[0];
            a.src = document.location.protocol + "//script.crazyegg.com/pages/scripts/0068/0907.js?" + Math.floor(new Date().getTime() / 3600000);
            a.async = true;
            a.type = "text/javascript";
            b.parentNode.insertBefore(a, b)
        }, 1);
    </script>

</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M3B2GWT"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<!-- Facebook Pixel Code (noscript)-->
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1508951712508536&ev=PageView&noscript=1"
    /></noscript>

@if($site == 'detail')
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

    <!-- Load Twitter SDK for JavaScript -->
    <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
    <script async type="text/javascript" src="//newsharecounts.s3-us-west-2.amazonaws.com/nsc.js"></script>

    <!-- Load Pinterest SDK for JavaScript -->
    <script async defer src="//assets.pinterest.com/js/pinit.js"></script>
@endif

@section('header')
    @include('layouts.header')
@show
@yield('content')
<!-- Footer -->
@section('footer')
    @include('layouts.footer')
@show
<div class="loader-modal"></div>
<div class="loader"></div>
<!-- jQuery -->
<script src="{{asset('js/jquery.js?v=1.000')}}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{asset('js/bootstrap.min.js?v=1.000')}}"></script>
<script src="{{asset('plugin/hammer/hammer.min.js?v=1.000')}}"></script>
<script src="{{asset('plugin/slick/slick.min.js?v=1.000')}}"></script>
<script src="{{asset('plugin/zoomove-master/src/js/zoomove.min.js?v=1.000')}}"></script>
@if(isset($payment_card_type) && $payment_card_type == 'stripe')
    <script src="https://js.stripe.com/v2/"></script>
@elseif(isset($payment_card_type) && $payment_card_type == 'braintree')
    <!-- Load the required client component. -->
    <script src="https://js.braintreegateway.com/web/3.19.1/js/client.min.js"></script>
    <!-- Load Hosted Fields component. -->
    <script src="https://js.braintreegateway.com/web/3.19.1/js/hosted-fields.min.js"></script>
@endif
<script src="{{asset('js/selectctr.min.js?v=1.025')}}"></script>
<script src="{{asset('js/30.js?v=1.027')}}"></script>
{!!isset($pagejs)?$pagejs:''!!}
</body>
</html>
