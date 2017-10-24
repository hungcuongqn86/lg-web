@extends('layouts.master')

@section('content')
    <h1 style="display:none;">Search</h1>
    <!-- Page Content -->
    <div>
        <div class="container" style="margin-top: 40px;">
        @if( $total > 0)
            <!-- List products -->
                <div class="row">
                    <h2 class="title-box margin">{{$total}} results for “{{ $search }}”</h2>
                    <div class="select-sort-box pull-right">
                        <label for="select-sort">Sort by:</label>
                        <select style="width: 150px;" id="select-sort"
                                title="select sort">
                            <option value="lowest">Price: Lowest first</option>
                            <option value="highest">Price: Highest first</option>
                            <option value="">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="list-products">
                        @foreach($pages as $item)
                            <div class="col-md-3 col-xs-6">
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
                                        <h3 class="title-product"><a href="/shop{{ $item->url }}">{{ $item->title }}</a>
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
                <div class="row">
                    <div class="paging text-center">
                        {!! $pages->appends(request()->input())->links('pagination.default') !!}
                    </div>
                </div>
            @else
                <div class="row text-center" style="margin-bottom: 40px;">
                    <h2 class="title-box margin">{{ trans('page.no_result_found')}}?</h2>
                </div>
                @if($more_campaign)
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="title-box">{{ trans('page.more_campaigns_you_might_like')}}</h2>
                            <div class="row list-item-products">
                                @foreach($more_campaign as $item)
                                    <div class="col-md-2 col-sm-2 col-xs-6 item-product">
                                        <a href="/shop{{ $item->url }}">
                                            <div class="img-feature">
                                                <img class="img-responsive"
                                                     src="{{getImgFromCampaign($item)['front']}}"
                                                     onerror="this.src='{{ $item->image }}'"
                                                     alt="{{ $item->title }}"/>
                                            </div>
                                            <div class="meta-product text-center">
                                                <h2 class="title-product">{{ $item->title }}</h2>
                                                <ul class="list-inline">
                                                    <li class="price-product"><i
                                                                class="ion-ios-pricetag-outline"></i>
                                                        <span>${{ $item->price }}</span></li>
                                                    <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                                        <span>{{secondsToTime($item->remaining)}}</span></li>
                                                </ul>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        var search = '{{ $search }}';
        var sortval = '{{$sort}}';
    </script>
@stop
