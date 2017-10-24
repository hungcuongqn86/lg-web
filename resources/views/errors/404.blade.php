@extends('layouts.master')

@section('content')
    <h1 style="display:none;">404</h1>
    <!-- Page Content -->
    <div class="list=products">
        <div class="container">
            <!-- List products -->
            <div class="list-item-products">
                <div class="empty_shopping_cart_text">
                    <h2>{{ trans('page.not_found_page')}}!</h2>
                    {!! trans('page.error_label_text')!!}
                </div>
            </div>

            @if($recently_view)
                <h2 class="title-box">{{ trans('page.recently_viewed')}}</h2>
                <div class="row list-item-products">
                    @foreach($recently_view as $item)
                        <div class="col-md-3 col-sm-3 col-xs-6 item-product">
                            <a href="/shop{{ $item->url }}">
                                <div class="img-feature">
                                    <img class="img-responsive"
                                         src="{{getImgFromCampaign($item)['front']}}"
                                         onerror="this.src='{{ $item->image }}'" alt="{{ $item->title }}"/>
                                </div>
                                <div class="meta-product text-center">
                                    <h2 class="title-product">{{ $item->title }}</h2>
                                    <ul class="list-inline">
                                        <li class="price-product"><i class="ion-ios-pricetag-outline"></i>
                                            <span>${{ $item->price }}</span></li>
                                        <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                            <span>{{secondsToTime($item->remaining)}}</span></li>
                                    </ul>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($more_campaign)
                <h2 class="title-box">{{ trans('page.more_campaigns_you_might_like')}}</h2>
                <div class="row list-item-products">
                    @foreach($more_campaign as $item)
                        <div class="col-md-3 col-sm-3 col-xs-6 item-product">
                            <a href="/shop{{ $item->url }}">
                                <div class="img-feature">
                                    <img class="img-responsive"
                                         src="{{getImgFromCampaign($item)['front']}}"
                                         onerror="this.src='{{ $item->image }}'" alt="{{ $item->title }}"/>
                                </div>
                                <div class="meta-product text-center">
                                    <h2 class="title-product">{{ $item->title }}</h2>
                                    <ul class="list-inline">
                                        <li class="price-product"><i class="ion-ios-pricetag-outline"></i>
                                            <span>${{ $item->price }}</span></li>
                                        <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                            <span>{{secondsToTime($item->remaining)}}</span></li>
                                    </ul>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@stop
