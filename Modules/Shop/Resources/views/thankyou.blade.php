@extends('layouts.master')

@section('content')
    @if($campaign_pixel)
        @foreach($campaign_pixel as $item)
            <img height="1" width="1" alt="" style="display:none"
                 src="https://www.facebook.com/tr?id={{ $item->fb_pixel }}&amp;ev=Purchase&amp;cd[url]={{ $item->url }}&amp;cd[quantity]={{ $item->quantity }}&amp;cd[price]={{ $item->price }}&amp;cd[currency]=USD"/>
        @endforeach
    @endif
    <!-- Page Content -->
    <h1 style="display:none;">Payment Success</h1>
    <div class="thankyou">
        <div class="container">
            <div class="top-thank text-center">
                <div class="status-thankyou clearfix">
                    <div class="col-md-3 col-xs-12"></div>
                    <div class="col-md-6 col-xs-12 Progress-Bar">
                        <hr/>
                        <div class="Paymented"><span><img class="img-responsive" src="{{asset('images/logo-burger-white.png')}}"></span>Purchased</div>
                        <div class="Printing"><span>2</span><p>Printing</p></div>
                        <div class="Delivery"><span>3</span><p>Delivery</p></div>
                    </div>
                    <div class="col-md-3 col-xs-12"></div>
                </div>
                <h2 class="title-box" style="height: 52px;line-height: 52px;text-align: center;"><span><i
                                class="ion-ios-checkmark-outline"></i></span>{{ trans('page.thank_you_for_your_order')}}
                </h2>
                <p class="text-center">{{ trans('page.your_order_has_been_placed')}}</p>
            </div>

            <!-- List order -->
            <div class="list-order">
                <b>Ordered Items</b>
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2" style="width: 50%;">Items</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th style="width: 10%;">Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr class="row-order-list">
                            <td style="padding-left: 0;"><img style="max-height: 70px;"
                                        src="<?php echo str_replace('.png', '_s.png', $item->variant_image);?>"
                                        onerror="this.src='{{ $item->variant_image }}'"
                                        alt="{{ $item->variant_name }}"/>
                            </td>
                            <td>{{ $item->variant_name }}</td>
                            <td>{{ $item->size_name }}</td>
                            <td><b>{{ $item->quantity }}</b></td>
                            <td style="padding-right: 0;"><b>${{ $item->price }}</b></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Sub shipping -->
            <div class="row sub-total clearfix">
                <div class="col-md-3 col-sm-3 col-xs-12 shipping-to">
                    <p><b>{{ trans('page.shipping_to')}}</b></p>
                    <p>
                        <span style="color:#6f6e72;display: inline-block;min-width: 80px;">Name:</span>{{ $order->shipping->name }}
                    </p>
                    <p>
                        <span style="color:#6f6e72;display: inline-block;min-width: 80px;">Address:</span>{{ $order->shipping->address->line1 }}@if($order->shipping->address->line2)
                            , {{ $order->shipping->address->line2 }}@endif</p>
                    <p style="padding-left: 80px;">{{ $order->shipping->address->city }}@if($order->shipping->address->state)
                            , {{ $order->shipping->address->state }}@endif, {{ $order->shipping->address->country }}</p>
                    <p>
                        <span style="color:#6f6e72;display: inline-block;min-width: 80px;">Phone:</span>{{ $order->shipping->phone }}
                    </p>
                </div>
                <div class="col-md-6 col-sm-6 hidden-xs"></div>
                <div class="col-md-3 col-sm-3 col-xs-12 sub-total-order">
                    <div class="list-total">
                        <p><span style="color:#6f6e72">{{ trans('page.shipping')}}:</span><span
                                    class="pull-right">${{ $order->total_shipping }}</span></p>
                        <p><span style="color:#6f6e72">{{ trans('page.subtotal')}}:</span><span
                                    class="pull-right">${{ $order->total_price }}</span></p>
                        <hr/>
                        <p><b>{{ trans('page.total')}}:<span class="pull-right">${{ $order->amount }}</span></b></p>
                    </div>
                </div>
                <div class="col-md-12" style="text-align: center;margin: 40px 0 80px 0;"><a href="/"
                                                                                            class="btn-hilight-color">{{ trans('page.continue_shopping')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop