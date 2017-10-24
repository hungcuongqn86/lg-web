@extends('layouts.master')

@section('content')
    <!-- Page Content -->
    <h1 style="display:none;">Shopping Cart</h1>
    <div>
        <div class="container">
            <div class="shopping-cart" style="margin: 20px 0 40px 0;">
                <div class="row">
                    @if($orderno)
                        <form id="form-submit-edit-cart" action="/shop/cart" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <div class="col-md-8 col-xs-12 list-products-cart">
                                <h2 class="title-box">{{ trans('page.shopping_cart')}}</h2>
                                @foreach($order->items as $item)
                                    <div class="cart-line clearfix">
                                        <input type="hidden" name="order_item_id[]" value="{{ $item->id }}"/>
                                        <div class="col-md-1 col-xs-2 img-cart-item">
                                            <a href="/shop{{ $item->campaign_url }}">
                                                <img class="img-responsive"
                                                     src="<?php echo str_replace('.png', '_s.png', $item->variant_image);?>"
                                                     onerror="this.src='{{ $item->variant_image }}'"
                                                     alt="{{ $item->variant_name }}"/>
                                            </a>
                                        </div>
                                        <div class="col-md-7 col-xs-7 meta-cart-item">
                                            <p class="cart-title"><a href="/shop{{ $item->campaign_url }}"
                                                                     class="title-cart-item">{{ $item->campaign_title }}</a>
                                            </p>
                                            <p class="info-cart-item">{{ $item->variant_name }}</p>
                                            <p class="info-cart-item">Size: <span
                                                        style="font-size: 12px;font-weight: bold;color: #212121;margin-left: 5px;">{{ $item->size_name }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-2 col-xs-3 qty-cart-item">
                                            <nobr class="pull-right"><span
                                                        style="font-size: 10px;letter-spacing: -1px;margin-right: 8px;">{{ $item->quantity }}
                                                    x</span> <b>${{ $item->price }}</b></nobr>
                                        </div>
                                        <div class="col-md-2 col-xs-12 action-cart-item">
                                            <button class="btn-simple cart-btn-edit pull-right"
                                                    id="cart-btn-edit-{{ $item->id }}">{{ trans('page.edit')}}</button>
                                            <button class="btn-simple  cart-btn-save pull-right"
                                                    id="cart-btn-save-{{ $item->id }}"
                                                    style="display:none;">{{ trans('page.save')}}</button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <ul class="change-select-cart" style="display: none;">
                                            <li style="width: 49%;text-align: left;padding-left: 30px;">
                                                <a class="cart-a-remove">
                                                    <span style="color: #cfcfcf;font-size: 12px;cursor: pointer;">{{ trans('page.remove')}}</span>
                                                </a>
                                            </li>
                                            <li style="width: 49%;text-align: right;padding-right: 30px;">
                                                <div class="qty">
                                                    <span class="title">QTY:</span>
                                                    <div class="selecttor">
                                                    <span class="qty-minus"><i
                                                                class="ion-minus-round"></i></span>
                                                        <input class="cart-quantity-input"
                                                               id="cart-quantity-input-{{ $item->id }}"
                                                               name="order_item_quantity[]"
                                                               title="qty value" type="text" cid="{{ $item->id }}"
                                                               value="{{ $item->quantity }}" min="1">
                                                        <span class="qty-plus"><i
                                                                    class="ion-plus-round"></i></span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                @endforeach
                            </div>
                            <div class="col-md-4 col-xs-12 cart-total">
                                <p><b>{{ trans('page.cart_subtotal')}}
                                        <span>(<span
                                                    class="num-items">{{ $order->total_quantity }}</span> {{ trans('page.items')}}
                                            )</span></b></p>
                                <p class="note-infor">{{ trans('page.shipping_and_tax_will_be_calculated_on_checkout')}}</p>
                                <p class="price-show-ci"
                                   data-content="{{ $order->total_price }}">
                                    ${{ $order->total_price }}</p>
                                <p>
                                    <a href="/shop/checkout"
                                       class="btn-checkout btn-hilight-color btn-loading-progess"><span
                                                class="loading-progess"></span>{{ trans('page.proceed_to_checkout')}}
                                    </a>
                                </p>
                            </div>
                        </form>
                    @else
                        <div class="col-md-8 col-xs-12 list-products-cart">
                            <div class="empty_shopping_cart"></div>
                            <div class="empty_shopping_cart_text">
                                <h2 class="title-box">{{ trans('page.your_cart_is_empty')}}</h2>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12 cart-total">
                            <p><b>{{ trans('page.cart_subtotal')}}
                                    <span>(<span
                                                class="num-items">0</span> {{ trans('page.items')}}
                                        )</span></b></p>
                            <p class="note-infor">{{ trans('page.shipping_and_tax_will_be_calculated_on_checkout')}}</p>
                            <p style="font-size: 36px;font-weight: bold;margin-top: 25px;">
                                $0</p>
                            <p>
                                <button disabled
                                        class="btn-checkout btn-hilight-color disabled">{{ trans('page.proceed_to_checkout')}}</button>
                            </p>
                        </div>
                    @endif
                    @if($recently_view)
                        <div class="clearfix"></div>
                        <div class="col-xs-12">
                            <h2 class="title-box">{{ trans('page.recently_viewed')}}</h2>
                            <div class="list-item-products">
                                @foreach($recently_view as $item)
                                    <div class="col-md-2 col-sm-2 col-xs-6 item-product">
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
                        </div>
                    @endif

                    @if($more_campaign)
                        <div class="clearfix"></div>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop