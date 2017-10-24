@extends('layouts.master')

@section('content')
    <div class="container detail-breadcrumb">
        <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
                <a href="/shop" class="back-shop"><i class="ion-chevron-left"></i><span
                            class="hidden-xs">Shop</span></a>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
                <ul class="breadcrumb">
                    <li><a href="/shop">{{ trans('page.shop')}}</a></li>
                    @isset ($category_select->parent)
                        <li><a href="/shop{{ $category_select->parent->url }}">{{ $category_select->parent->name }}</a>
                        </li>
                    @endisset
                    <li><a href="/shop{{ $category_select->url }}">{{ $category_select->name }}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="div-promotion-alert">
            {{ trans('page.promotion_alert_1')}} <span
                    class="promotion-value"></span> {{ trans('page.promotion_alert_2')}} {{ trans('page.promotion_alert_3')}}
        </div>
        <div class="row">
            <div class="col-md-7">
                <div id="image-product-preview" class="row image-product-feature">
                    <div class="preview col-md-10 col-md-push-2 col-sm-push-2 col-sm-10 col-xs-12">
                        <figure id="img-preview" class="zoo-item"
                                data-zoo-image=""></figure>
                        <div class="preview-arrow arrow-prev">
                            <i class="icon ion-chevron-left"></i>
                        </div>
                        <div class="preview-arrow arrow-next">
                            <i class="icon ion-chevron-right"></i>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-md-pull-10 col-sm-pull-10 col-xs-12">
                        @foreach($campaign_select->products as $prod_item)
                            @foreach($prod_item->variants as $variant_item)
                                <ul class="variant-list" id="{{$variant_item->id}}"
                                    style="display:none;">
                                    <li class="mockup-item">
                                        <img class="product-thumb"
                                             src="<?php echo str_replace('.png', '_s.png', $variant_item->image->front);?>"
                                             onerror="this.src='{{ $variant_item->image->front }}'"
                                             data-src="{{ $variant_item->image->front }}" alt="Front"/></li>
                                    @if(isValidUrl($variant_item->image->back))
                                        <li class="mockup-item">
                                            <img class="product-thumb"
                                                 src="<?php echo str_replace('.png', '_s.png', $variant_item->image->back);?>"
                                                 onerror="this.src='{{ $variant_item->image->back }}'"
                                                 data-src="{{ $variant_item->image->back }}" alt="Back"/></li>
                                    @endif
                                    @isset($variant_item->mockups)
                                        @foreach($variant_item->mockups as $mockups_item)
                                            <li class="mockup-item">
                                                <img class="product-thumb"
                                                     src="<?php echo str_replace('.png', '_s.png', $mockups_item->image->url);?>"
                                                     onerror="this.src='{{ $mockups_item->image->url }}'"
                                                     data-src="{{ $mockups_item->image->url }}"
                                                     alt="Mockup-{{ $mockups_item->type }}"/></li>
                                        @endforeach
                                    @endisset
                                </ul>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="share-are">
                    <ul>
                        <li>Share</li>
                        <li class="facebook">
                            <i class="ion-social-facebook"></i>
                        </li>
                        <li class="twitter">
                            <i class="ion-social-twitter"></i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-5 info-product">
                <h1 class="title-box">{{ $campaign_select->title }}</h1>
                @isset($campaign_select->stores->uri)
                    <p class="info-prod-from-store">{{ trans('page._from')}} <a
                                href="/stores/{{ $campaign_select->stores->uri }}"><b>{{ $campaign_select->stores->title }}</b></a>
                    </p>
                @endisset

                <div class="option-buy">
                    <form class="change-price">
                        <div class="form-group">
                            <label for="sl-product">{{ trans('page.available_products')}}:</label>
                            <select class="form-control" id="sl-product">
                                @foreach($campaign_select->products as $item)
                                    @if ($item->id == $pid)
                                        <option value="{{ $item->id }}" selected="selected">{{ $item->base->name }}
                                            - ${{ $item->price }}</option>
                                    @else
                                        <option value="{{ $item->id }}">{{ $item->base->name }} -
                                            ${{ $item->price }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="select-product-type">
                        <ul class="list-inline list-style-group-detail">
                            @foreach($campaign_select->products as $item)
                                <li class="li-variant" pid="{{ $item->id }}"
                                    cid="{{ $item->default_variant }}">
                                    <img class="img-responsive"
                                         src="<?php echo str_replace('.png', '_s.png', $item->image);?>"
                                         onerror="this.src='{{ $item->image }}'"/>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="tee-size">
                        <label>{{ trans('page.select_size')}}:</label>
                        @foreach($campaign_select->products as $item)
                            <ul class="list-inline ul-variant-size" id="{{ $item->id }}">
                                @foreach($item->sizes as $size_item)
                                    <li class="product-size-list-item" id="{{ $size_item->id }}">
                                        <div class="product-size-block">{{$size_item->name}}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                        <p><i style="color: #6f6e72;" class="ion-clipboard"></i>
                            <a class="label-pointer" data-toggle="modal" data-target="#size-guide-modal"
                               style="text-decoration:none;color: #8549ba;cursor: pointer">{{ trans('page.size_guide')}}</a>
                        </p>
                    </div>
                    <div class="change-color">
                        <label>{{ trans('page.select_color')}}:</label>
                        @foreach($campaign_select->products as $item)
                            <ul class="list-inline ul-variant-color" id="{{ $item->id }}" style="display: none;">
                                @foreach($item->variants as $variant_item)
                                    <li class="product-color-list-item" id="{{ $variant_item->id }}">
                                        <div class="product-color-block"
                                             style="background-color:{{ $variant_item->color }};"></div>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>

                <div class="price">
                    <div class="qty">
                        <span class="title">QTY</span>
                        <div class="selecttor">
                            <span class="qty-minus" data-content="{{$campaign_select->price}}"><i
                                        class="ion-minus-round"></i></span>
                            <input id="qty-value" data-content="{{$campaign_select->price}}" name="qty-value"
                                   title="qty value" type="number" min=1 value="1">
                            <span class="qty-plus" data-content="{{$campaign_select->price}}"><i
                                        class="ion-plus-round"></i></span>
                        </div>
                    </div>
                    <div class="price-value">
                        <span id="price-value"
                              data-content="{{$campaign_select->price}}">${{$campaign_select->price}}</span>
                        @if($pr_code&&$pr_code!=='')
                            <span style="" class="old-price">${{$campaign_select->price}}</span>
                            <div class="save">
                                <span style="font-size: 12px;">Save</span>
                                <span style="font-size: 16px;" class="save-value"></span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($campaign_select->state == 'launching')
                    <div class="detail-btn">
                        <a class="btn-hilight-color btn-buy"
                           href="javascript:void(0);">{{ trans('page.buy_it_now')}}</a>
                    </div>
                @else
                    <div class="detail-btn end-campaign">
                        {{ trans('page.campaign_ended')}}
                    </div>
                @endif
                @if ($campaign_select->state == 'launching')
                    <div class="time-campaign timer" data-seconds-left="{{ $campaign_select->remaining }}">
                        <p class="title-time">{{ trans('page.last_day_to_order')}}:</p>
                        <div class="time-box">
                            <div class="col-sm-4 col-xs-4 item-time">
                                <div class="clock">
                                    <img src="{{asset('images/time.svg')}}">
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-2 item-time">
                                <p id="p-day">00</p>
                                <span>{{ trans('page.days')}}</span>
                            </div>
                            <div class="col-sm-2 col-xs-2 item-time">
                                <p id="p-hour">00</p>
                                <span>{{ trans('page.hours')}}</span>
                            </div>
                            <div class="col-sm-2 col-xs-2 item-time">
                                <p id="p-minute">00</p>
                                <span>{{ trans('page.minutes')}}</span>
                            </div>
                            <div class="col-sm-2 col-xs-2 item-time">
                                <p id="p-second">00</p>
                                <span>{{ trans('page.seconds')}}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 30px">
        <div class="row">
            <div class="col-md-5 col-md-push-7 info-product">
                <div class="desc">
                    <h5>Description<i class="togglerDesc pull-right ion-minus-round"></i></h5>
                    <div class="desc-conten">
                        {!! $campaign_select->desc !!}
                    </div>
                </div>
                <div class="desc">
                    <h5>{{ trans('page.shipping_info')}}<i class="togglerDesc pull-right ion-minus-round"></i></h5>
                    <div class="desc-conten">
                        {!! trans('page.shipping_info_text')!!}
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-md-pull-5 image-product-feature">
                <!-- List recently viewed -->
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
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="form-submit-to-cart" action="/shop/cart" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <input type="hidden" name="campaignid" id="camp_id" value="{{$campaign_select->id}}"/>
                    <input type="hidden" name="promotioncode" id="pr_code" value="{{$pr_code}}"/>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" style="outline:none;"><i
                                    class="ion-close-round"></i></button>
                        <h4 class="modal-title">{{ trans('page.your_order')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="list-item-cart">
                            <table class="table" id="table-list-cart">
                                <thead class="precheckout-items-table-head">
                                <tr>
                                    <th class="hidden-xs" style="width:15%">Items</th>
                                    <th style="width:55%">{{ trans('page.style')}}</th>
                                    <th style="width:10%">{{ trans('page.size')}}</th>
                                    <th style="width:10%">{{ trans('page.qty')}}</th>
                                    <th style="width:10%">{{ trans('page.price')}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="add-div" style="margin: 15px 0;">
                            <a href="javascript:void(0);" class="add-another-style" id="a-add-order-row">
                                <span style="font-size: 28px;"><i
                                            class="ion-ios-plus-outline"></i></span><span>{{ trans('page.add_another_style_of_color')}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <b id="item-like-other-title" style="display: none;">{{ trans('page.you_may_also_like')}} </b>
                        <div class="row">
                            <div id="item-like-other" style="display:none;">
                                <div class="col-md-2 hidden-xs"></div>
                                <div class="may-also-like col-md-8 col-xs-12 center-block">
                                    @foreach($campaign_select->products as $item)
                                        @if($item->base->include == 1)
                                            <div class="content-also-like">
                                                <ul class="content-also-like-list" pid="{{ $item->id }}"
                                                    cid="{{ $item->default_variant }}">
                                                    <li style="width: 15%">
                                                        <img src="{{ $item->image }}" alt="Include image"
                                                             class="img-responsive"/>
                                                    </li>
                                                    <li style="width: 65%">
                                                        <p><b>{{ $item->default_variant_name }}</b></p>
                                                        <p style="font-size: 10px;font-weight: bold;color: #fa9918;">
                                                            ${{ $item->price }}</p>
                                                    </li>
                                                    <li style="width: 20%">
                                                        <button style="display: none;"></button>
                                                        <button class="btn-add-orther add-row btn-simple">{{ trans('page.add')}}</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-md-2 hidden-xs"></div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btn-add-to-cart col-md-4 col-xs-12">
                                <button class="submit-cart btn-hilight-color" name="submit-cart"
                                        value="true">{{ trans('page.add_to_cart')}}</button>
                            </div>

                            <div class="col-md-8 col-xs-12 text-right proceed-to-checkout-btn">
                                <span style="font-size: 28px;"><img
                                            style="width: 18px;height: 18px; margin-right: 10px;"
                                            src="{{asset('images/rocket-icon.png')}}"/></span>
                                <button class="add-another-style" name="submit-checkout"
                                        style="padding: 0;"
                                        value="true">{{ trans('page.proceed_to_checkout')}}</button>
                                <div class="clearfix"></div>
                                <img src="{{asset('images/pay.png')}}"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Size guide -->
    <div id="size-guide-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></span> Classic T-Shirt</h4>
                </div>

                <div class="modal-body">
                    <table class="table" style="text-align:center">
                        <thead>
                        <tr>
                            <th style="text-align:center"><span>Size</span></th>
                            <th style="text-align:center"><span>IN</span></th>
                            <th style="text-align:center"><span>CM</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span>Small</span></td>
                            <td>34-36</td>
                            <td>86-91</td>
                        </tr>
                        <tr>
                            <td><span>Medium</span></td>
                            <td>38-40</td>
                            <td>97-102</td>
                        </tr>
                        <tr>
                            <td><span>Large</span></td>
                            <td>42-44</td>
                            <td>107-112</td>
                        </tr>
                        <tr>
                            <td><span>X-Large</span></td>
                            <td>46-48</td>
                            <td>117-122</td>
                        </tr>
                        <tr>
                            <td><span>2X-Large</span></td>
                            <td>50-52</td>
                            <td>127-132</td>
                        </tr>
                        <tr>
                            <td><span>3X-Large</span></td>
                            <td>54-56</td>
                            <td>137-142</td>
                        </tr>
                        <tr>
                            <td><span>4X-Large</span></td>
                            <td>58-60</td>
                            <td>147-152</td>
                        </tr>
                        <tr>
                            <td><span>5X-Large</span></td>
                            <td>62-64</td>
                            <td>157-162</td>
                        </tr>
                        <tr>
                            <td><span>6X-Large</span></td>
                            <td>66-68</td>
                            <td>167-172</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button class="btn-hilight-color pull-right" data-dismiss="modal"><b>Done</b>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Promotion modal -->
    <div id="promotion-modal" class="promotion-modal modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" style="outline:none;"><i
                                class="ion-close-round"></i></button>
                    <p class="promotion-value header-1"></p>
                    <p class="header-2">{{ trans('page.your_purchase')}}</p>
                </div>

                <div class="modal-body text-center">
                    <p>{{ trans('page.promotion_alert_1')}} <span
                                class="promotion-value"></span> {{ trans('page.promotion_alert_2')}}</p>
                    <p>{{ trans('page.promotion_alert_3')}}</p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal"><b>{{ trans('page.redeem_offer')}}</b></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var pid = '{{$pid}}';
        var cid = '{{$cid}}';
        var sid = '{{$sid}}';
        var sizeval = '';

        var remove_text = "{{ trans('page.remove')}}";
        var category = '{{ $category_select->name }}';
        var campaign = '{{ $campaign_select->title }}';
        var campaign_url = '{{ $campaign_select->url }}';
        var p_list = [];
        @foreach($campaign_select->products as $item)
            p_list['{{ $item->id }}'] = [];
        p_list['{{ $item->id }}']['price'] = '{{ $item->price }}';
        p_list['{{ $item->id }}']['name'] = '{{ $item->base->name }}';
        p_list['{{ $item->id }}']['sizes'] = [];
        @foreach($item->sizes as $size_item)
            p_list['{{ $item->id }}']['sizes'].push(['{{ $size_item->id }}', '{{ $size_item->name }}']);
        @endforeach
            p_list['{{ $item->id }}']['variants'] = [];
        @foreach($item->variants as $vari_item)
            p_list['{{ $item->id }}']['variants']['{{ $vari_item->id }}'] = [];
        p_list['{{ $item->id }}']['variants']['{{ $vari_item->id }}'].push(['{{ $vari_item->name }}'], ['{{ $vari_item->image->front }}']);
        @endforeach
        @endforeach
    </script>
@stop


