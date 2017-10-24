@extends('layouts.master')

@section('content')
    <div class="wrapper">
        <!-- Page Content -->
        <h1 style="display:none;">Find something made for you.</h1>
        <div class="container">
            <div class="home-banner">
                <div class="">
                    <img src="{{asset('images/banner/slide1.jpg')}}" alt="Banner 1"/>
                </div>
                <div class="">
                    <img src="{{asset('images/banner/slide1.jpg')}}" alt="Banner 2"/>
                </div>
                <div class="">
                    <img src="{{asset('images/banner/slide1.jpg')}}" alt="Banner 3"/>
                </div>
            </div>
        </div>

        <div class="container ">
            <!-- Because you view -->
            @if($recently_view)
                <div class="row">
                    <div class="because-view-home">
                        <h2 class="title-box margin">Recently Viewed</h2>
                        <div class="slide-product-home list-products">
                            @foreach($recently_view as $item)
                                <div class="col-md-3">
                                    <div class="item-product">
                                        <div class="img-feature">
                                            <a href="/shop{{ $item->url }}">
                                                <img @if(getImgFromCampaign($item)['back'] != '') class="img-responsive img-front img-flip-front"
                                                     @else class="img-responsive" @endif
                                                     src="{{getImgFromCampaign($item)['front']}}"
                                                     onerror="this.src='{{ $item->image }}'"
                                                     alt="{{ $item->title }}"/>
                                                @if(getImgFromCampaign($item)['back'] != '')
                                                    <img style="display: none;"
                                                         class="img-responsive img-back img-flip-back"
                                                         src="{{getImgFromCampaign($item)['back']}}"
                                                         alt="{{ $item->title }}"/>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="meta-product text-center">
                                            <h3 class="title-product"><a
                                                        href="/shop{{ $item->url }}">{{ $item->title }}</a>
                                            </h3>
                                            <ul class="list-inline">
                                                <li class="price-product"><i class="ion-ios-pricetag-outline"></i>
                                                    <span>${{ $item->price }}</span></li>
                                                <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                                    <span>{{secondsToTime($item->remaining)}}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        <!-- Best selling for couple -->
            @if($campaign_bestseller)
                <div class="row">
                    <div class="best-selling-for-couple">
                        <h2 class="title-box margin">
                            <img src="{{asset('images/best-selling.png')}}"/>{{ trans('page.best_sellers')}}
                        </h2>
                        <div class="slide-product-home list-products">
                            @foreach($campaign_bestseller as $item)
                                <div class="col-md-3">
                                    <div class="item-product">
                                        <div class="img-feature">
                                            <a href="/shop{{ $item->url }}">
                                                <img @if(getImgFromCampaign($item)['back'] != '') class="img-responsive img-front img-flip-front"
                                                     @else class="img-responsive" @endif
                                                     src="{{getImgFromCampaign($item)['front']}}"
                                                     onerror="this.src='{{ $item->image }}'"
                                                     alt="{{ $item->title }}"/>
                                                @if(getImgFromCampaign($item)['back'] != '')
                                                    <img style="display: none;"
                                                         class="img-responsive img-back img-flip-back"
                                                         src="{{getImgFromCampaign($item)['back']}}"
                                                         alt="{{ $item->title }}"/>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="meta-product text-center">
                                            <h3 class="title-product"><a
                                                        href="/shop{{ $item->url }}">{{ $item->title }}</a>
                                            </h3>
                                            <ul class="list-inline">
                                                <li class="price-product"><i class="ion-ios-pricetag-outline"></i>
                                                    <span>${{ $item->price }}</span></li>
                                                <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                                    <span>{{secondsToTime($item->remaining)}}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ADV Images Home -->
        <div class="container images-adv-home">
            <div class="row">
                <div class="col-sm-6">
                    <img class="img-responsive" src="{{asset('images/ads1.jpeg')}}" alt="adv 1"/>
                </div>
                <div class="col-sm-6">
                    <img class="img-responsive" src="{{asset('images/ads2.jpeg')}}" alt="adv 2"/>
                </div>
            </div>
        </div>

        <!-- Shopping Categories -->
        <div class="container shopping-by-categories">
            <div class="row">
                <h2 class="shopping-categories-title">shopping by categories</h2>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/age" class="categories-item">
                        <img src="{{asset('images/categories/ace.svg')}}" alt="ACE"/>
                        <span style="background-color: #7ac70c">ACE</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/family" class="categories-item">
                        <img src="{{asset('images/categories/family.svg')}}" alt="family"/>
                        <span style="background-color: #fa9918">family</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/hobbies" class="categories-item">
                        <img src="{{asset('images/categories/hobbies.svg')}}" alt="hobbies"/>
                        <span style="background-color: #ffc715">hobbies</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/animals" class="categories-item">
                        <img src="{{asset('images/categories/animals.svg')}}" alt="animals"/>
                        <span style="background-color: #1cb0f6">animals</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/locations" class="categories-item">
                        <img src="{{asset('images/categories/locations.svg')}}" alt="locations"/>
                        <span style="background-color: #d33131">locations</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/sports" class="categories-item">
                        <img src="{{asset('images/categories/sports.svg')}}" alt="sports"/>
                        <span style="background-color: #72659b">sports</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/entertainment" class="categories-item">
                        <img src="{{asset('images/categories/entertainment.svg')}}" alt="entertainment"/>
                        <span style="background-color: #d33131">entertainment</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/names" class="categories-item">
                        <img src="{{asset('images/categories/names.svg')}}" alt="names"/>
                        <span style="background-color: #72659b">names</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/occasions" class="categories-item">
                        <img src="{{asset('images/categories/occasions.svg')}}" alt="occasions"/>
                        <span style="background-color: #7ac70c">occasions</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/jobs" class="categories-item">
                        <img src="{{asset('images/categories/jobs.svg')}}" alt="jobs"/>
                        <span style="background-color: #fa9918">jobs</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop/awareness" class="categories-item">
                        <img src="{{asset('images/categories/awareness.svg')}}" alt="awareness"/>
                        <span style="background-color: #1cb0f6">awareness</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <a href="/shop" class="categories-item">
                        <img src="{{asset('images/categories/other.svg')}}" alt="other"/>
                        <span style="background-color: #cfcfcf">other</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
