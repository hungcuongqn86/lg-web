@extends('layouts.master')

@section('content')
    <!-- Page Content -->
    <h1 style="display:none;">{{ $store->title }}</h1>
    <div class="container store-breadcrumb">
        <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
                <a href="/" class="back-shop"><i class="ion-chevron-left"></i><span
                            class="hidden-xs">Home</span></a>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
                <ul class="breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li><a>Store</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <p class="heading-store">{{ $store->title }}</p>

        <!--Nav tab -->
        <div class="store-tab">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab-products" role="tab" data-toggle="tab">
                        <span>Products</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-introduction" role="tab" data-toggle="tab">
                        <span>Introduction</span>
                    </a>
                </li>
            </ul>
            <div class="share-store"> <i class="ion-social-facebook"></i> </div>
        </div>
        <!-- Tab panes -->
        <div class="tab-content row">
            <div role="tabpanel" class="tab-pane active" id="tab-products">
                <!-- List products -->
                <div class="list-products">
                    @foreach($pages as $item)
                        <div class="col-md-3 col-xs-6">
                            <div class="item-product">
                                <div class="img-feature">
                                    <a href="/shop{{ $item->url }}?pid={{ $item->id }}"><img @if($item->image->back != '')class="img-responsive img-flip"@else class="img-responsive"@endif
                                        src="{{$item->image->front}}"
                                        @if($item->image->back != '')
                                        onmouseover="changeImgUrl(this,'{{$item->image->back}}')"
                                        onmouseout="changeImgUrl(this,'{{$item->image->front}}')"
                                        @endif
                                        alt="{{ $item->title }}"/></a>
                                </div>
                                <div class="meta-product text-center">
                                    <h3 class="title-product"><a
                                                href="/shop{{ $item->url }}?pid={{ $item->id }}">{{ $item->title }}</a>
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
                <div class="clearfix"></div>
                <div class="paging text-center">
                    {!! $pages->appends(request()->input())->render() !!}
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-introduction">
                <div class="container">
                    <p>{!! $store->desc !!}</p>
                </div>
            </div>
        </div>
    </div>
@stop
