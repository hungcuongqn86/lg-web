<nav class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <div class="row">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="{{asset('/')}}">
                    <img class="" src="{{asset('images/logo-burger.png')}}" alt="">
                </a>
                <div class="shop-cart pull-right hidden-lg hidden-md hidden-sm">
                    <a href="{{asset('shop/cart')}}"><img src="{{asset('images/shopping-bag.png')}}"/>
                        @if($orderno)
                            <span class="noti-cart">{{ $orderno }}</span>
                        @endif
                    </a>
                </div>
                <!-- Search form mobile -->
                <div class="form-search-header hidden-lg hidden-md hidden-sm pull-right">
                    <div class="ion-ios-search ico-search-mobile"></div>

                    <form action="{{asset('shop/search')}}"
                          class="input-group stylish-input-group search-form-mobile submitform-search-header"
                          method="get">
                        <span class="ico-search-mb"><i class="ion-ios-search"></i></span>
                        <input type="text" class="form-control text-search-header"
                               placeholder="{{ trans('header.search')}}" name="term"
                               @isset($search)value="{{ $search }}"@endisset>
                        <span class="close-form-search"><i class="ion-ios-close-outline"></i></span>
                        <div class="search-examples">
                            <p class="heading-examples">Examples</p>
                            <ul class="list-unstyled list-examples-text">
                                <li><a href="#">Soccer Mom</a></li>
                                <li><a href="#">Pug Lover</a></li>
                                <li><a href="#">NFL Patriot Dads</a></li>
                                <li><a href="#">Coffee Addict</a></li>
                                <li><a href="#">Car Fanatic</a></li>
                                <li><a href="#">Mancherter</a></li>
                            </ul>
                        </div>
                        <button style="display: none" type="submit">
                        </button>
                        <input type="hidden" name="sort" @isset($sort)value="{{ $sort }}"@endisset/>
                    </form>
                </div>
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <div class="form-search-header col-md-3 hidden-xs">
                <form action="{{asset('shop/search')}}" class="input-group stylish-input-group submitform-search-header"
                      method="get">
                    <input type="text" class="form-control text-search-header" placeholder="{{ trans('header.search')}}"
                           name="term"
                           @isset($search)value="{{ $search }}"@endisset>
                    <span class="input-group-addon">
                    <button type="submit">
                        <span class="ion-ios-search"></span>
                    </button>
                </span>
                    <input type="hidden" name="sort" @isset($sort)value="{{ $sort }}"@endisset/>
                </form>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse js-navbar-collapse" id="bs-example-navbar-collapse-1">
                <div class="shop-cart pull-right hidden-xs">
                    <a href="{{asset('shop/cart')}}">
                        <img src="{{asset('images/shopping-bag.png')}}"/>
                        @if($orderno)
                            <span class="noti-cart">{{ $orderno }}</span>
                        @endif
                    </a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="dropdown mega-dropdown">
                        <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown">
                            Shop <i class="ion-ios-arrow-down"></i>
                        </a>
                        <div class="dropdown-menu mega-menu mega-dropdown-menu">
                            <ul class="container">
                                <li>
                                    <p class="title-mnu-mega black">{{ trans('header.shop_categories')}}</p>
                                </li>

                                <li class="col-md-3 hidden-xs hidden-sm">
                                    <ul class="list-unstyled">
                                        @if($category)
                                            @foreach($category as $key=>$item)
                                                @if ($key%3 === 0)
                                                    <li><a href="/shop{{ $item->url }}">{{ $item->name }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li class="col-md-3 hidden-xs hidden-sm">
                                    <ul class="list-unstyled">
                                        @if($category)
                                            @foreach($category as $key=>$item)
                                                @if ($key%3 === 1)
                                                    <li><a href="/shop{{ $item->url }}">{{ $item->name }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li class="col-md-3 hidden-xs hidden-sm">
                                    <ul class="list-unstyled">
                                        @if($category)
                                            @foreach($category as $key=>$item)
                                                @if ($key%3 === 2)
                                                    <li><a href="/shop{{ $item->url }}">{{ $item->name }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li class="col-xs-6 hidden-md hidden-lg hidden-print">
                                    <ul class="list-unstyled">
                                        @if($category)
                                            @foreach($category as $key=>$item)
                                                @if ($key%2 === 0)
                                                    <li><a href="/shop{{ $item->url }}">{{ $item->name }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li class="col-xs-6 hidden-md hidden-lg hidden-print">
                                    <ul class="list-unstyled">
                                        @if($category)
                                            @foreach($category as $key=>$item)
                                                @if ($key%2 === 1)
                                                    <li><a href="/shop{{ $item->url }}">{{ $item->name }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li class="col-md-3 hidden-xs">
                                    <img class="img-responsive" src="{{asset('images/mnu-banner.png')}}"
                                         alt="banner mneu"/>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="{{asset('track')}}"
                           class="text-uppercase warm-grey">{{ trans('header.track_order')}}</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </div>
    <!-- /.container -->
</nav>
<div class="site-search-overlay"></div>