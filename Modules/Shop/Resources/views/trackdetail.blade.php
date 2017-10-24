@extends('layouts.master')

@section('content')
    <h1 style="display:none;">Track order</h1>
    <!-- Page Content -->
    <div class="container">
        <div class="row" style="margin-bottom:20px;">
            <div class="col-md-8">
                <h2 class="larg-title-box track-title">{{ trans('page.track_your_order')}}</h2>
                <form action="/track" method="get" id="form-submit-track-order">
                    <p class="track-desc">{{ trans('page.track_order_text')}}</p>
                    <div class="form-group track-control">
                        <input class="form-control track-input" type="text" name="pid" id="lookup_number" value="{{$order_tracking}}"
                               placeholder="Lookup number">
                        <button type="submit" class="track-btn btn-hilight-color btn-loading-progess"><span
                                    class="loading-progess"></span>{{ trans('page.track_order')}}</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <p class="track"><span class="track-order">{{ trans('page.order')}}:</span> <span
                            class="track-order order-numb">{{ $order_tracking }}</span></p>
                @if($campaign_title)
                    <div class="row">
                        <img class="text-center img-track-order col-md-3 col-xs-12 img-circle img-responsive"
                             src="{{ $campaign_image }}" alt="{{ $campaign_title }}"/>
                        <div class="info-track-order col-md-8 col-xs-12 text-left">
                            <div>
                                <span class="order-from">{{ trans('page.order_from')}}:</span>
                                <p class="order-from-title">{{ $campaign_title }}</p>
                            </div>
                            <div class="end-date">
                                <span class="order-enddate">{{ trans('page.end_date')}}:</span>
                                <span class="order-enddate-val">{{ $date_end }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- List order track -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:15%">{{ trans('page.date')}}</th>
                    <th style="width:15%">{{ trans('page.payment_method')}}</th>
                    <th style="width:30%">{{ trans('page.subtotal')}}</th>
                    <th style="width:10%">{{ trans('page.shipping')}}</th>
                    <th style="width:10%">{{ trans('page.tax')}}</th>
                    <th style="width:10%">{{ trans('page._total')}}</th>
                    <th style="width:10%">{{ trans('page.refunded_amount')}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        @if($order->payment->pay_time)
                            {{ $order->payment->pay_time }}
                        @endif
                    </td>
                    <td>
                        @if($order->payment->method == 'paypal')
                            {{ trans('page.paypal')}}
                        @else
                            {{ trans('page.card')}}
                        @endif
                    </td>
                    <td>${{ $order->total_price }}</td>
                    <td>${{ $order->total_shipping }}</td>
                    <td>$0.00</td>
                    <td><b>${{ $order->amount }}</b></td>
                    <td>$0.00</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- List order track info user -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:15%">{{ trans('page.email')}}</th>
                    <th style="width:15%">{{ trans('page.name')}}</th>
                    <th style="width:15%">{{ trans('page.address_1')}}</th>
                    <th style="width:15%">{{ trans('page.address_2')}}</th>
                    <th style="width:10%">{{ trans('page.city')}}</th>
                    <th style="width:10%">{{ trans('page.state')}}</th>
                    <th style="width:10%">{{ trans('page.country')}}</th>
                    <th style="width:10%">{{ trans('page.zip')}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $order->shipping->email }}</td>
                    <td><b>{{ $order->shipping->name }}</b></td>
                    <td>{{ $order->shipping->address->line1 }}</td>
                    <td>{{ $order->shipping->address->line2 }}</td>
                    <td><b>{{ $order->shipping->address->city }}</b></td>
                    <td><b>{{ $order->shipping->address->state }}</b></td>
                    <td><b>{{ $order->shipping->address->country }}</b></td>
                    <td><b>{{ $order->shipping->address->postal_code }}</b></td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- Order items -->
        <p class="order-label">{{ trans('page.ordered_items')}}</p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:10%;min-width:50px;">{{ trans('page.item')}}</th>
                    <th style="width:50%"></th>
                    <th style="width:10%">{{ trans('page.size')}}/{{ trans('page.style')}}</th>
                    <th style="width:10%">{{ trans('page.price')}}</th>
                    <th style="width:10%">{{ trans('page.quantity')}}</th>
                    <th style="width:10%">{{ trans('page.status')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td><img class="img-responsive" src="{{ $item->variant_image }}"
                                 alt="{{ $item->variant_name }}"/></td>
                        <td><b>{{ $item->variant_name }}</b></td>
                        <td><b>{{ $item->size_name }}</b></td>
                        <td><b>${{ $item->price }}</b></td>
                        <td><b>{{ $item->quantity }}</b></td>
                        <td>
                            @isset($item->tracking)
                                <b class="track-state-{{ $item->tracking->state }}">
                                    {{ $item->tracking->state }}
                                </b>
                                @else
                                    <b class="track-state-{{ $order->state }}">
                                        {{ $order->state }}
                                    </b>
                                    @endisset
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tracking Infomation -->
        <p class="order-label">{{ trans('page.tracking_information')}}</p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:30%">{{ trans('page.item')}}</th>
                    <th style="width:30%">{{ trans('page.carrier')}}</th>
                    <th style="width:10%">{{ trans('page.size')}}</th>
                    <th style="width:20%">{{ trans('page.tracking_code')}}</th>
                    <th style="width:10%">{{ trans('page.link')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td><b>{{ $item->variant_name }}</b></td>
                        <td><b>
                                @isset($item->tracking)
                                    {{ $item->tracking->information }}
                                @endisset
                            </b></td>
                        <td><b>{{ $item->size_name }}</b></td>
                        <td><b>
                                @isset($item->tracking)
                                    {{ $item->tracking->code }}
                                @endisset
                            </b></td>
                        <td><b>
                                @isset($item->tracking)
                                    <a class="track-link" href="{{ $item->tracking->url }}"
                                       target="_blank">{{ trans('page.track_link')}}</a>
                                @endisset
                            </b></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
