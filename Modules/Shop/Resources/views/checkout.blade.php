@extends('layouts.master')

@section('content')
    @if($campaign_pixel)
        @foreach($campaign_pixel as $item)
            <img height="1" width="1" alt="" style="display:none"
                 src="https://www.facebook.com/tr?id={{ $item->fb_pixel }}&amp;ev=InitiateCheckout&amp;cd[url]={{ $item->url }}&amp;cd[quantity]={{ $item->quantity }}&amp;cd[price]={{ $item->price }}&amp;cd[currency]=USD"/>
        @endforeach
    @endif
    <!-- Page Content -->
    <h1 style="display:none;">Checkout</h1>
    <div>
        <div class="container">
            <!-- CheckOut -->
            <div class="alert-error" @if(!$validation_error || !in_array('connection',$validation_error))style="display:none;"@endif>
                <div class="form">
                    <div class="form-group">
                        <p id="p-alert-connection-error"
                           @if(!$validation_error || !in_array('connection',$validation_error))style="display:none;"@endif>{{ trans('page.invalid.connection')}}</p>
                    </div>
                </div>
            </div>
            <div class="checkout" style="margin: 20px 0 80px 0;">
                <h2 class="title-box">{{ trans('page.checkout')}}</h2>
                <div class="row cart-body">
                    <form class="form-horizontal form-input-lg" method="post" action="/shop/checkout"
                          id="checkout-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" id="payment_card_type" name="payment_card_type"
                               value="{{ $payment_card_type }}"/>
                        <input type="hidden" id="brain_tree_key" value="{{ $brain_tree_key }}"/>

                        <div class="contact-form col-lg-6 col-md-6 col-sm-6 col-xs-12 col-md-push-6 col-sm-push-6">
                            <h4 class="heading4-form">Your infomation</h4>
                            <div class="form-group" style="margin-bottom:10px;">
                                <div class="col-md-12">
                                    <strong>{{ trans('page.contact_info')}}</strong>
                                    <input type="text" class="form-control" name="order-email" id="order-email"
                                           value="{{ $order->shipping->email }}"
                                           placeholder="{{ trans('page.email')}}"/>
                                    <p class="alert-contact-error" id="p-alert-email" style="display:none;">{{ trans('page.invalid.email')}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <strong>{{ trans('page.shipping_address')}}</strong>
                                    <div class="checkbox checkbox-warning" style="float:right;padding-top:0px;">
                                        <input id="checkbox7" type="checkbox" name="save-address-info" class="styled" checked>
                                        <label for="checkbox7">
                                            {{ trans('page.save_address_info')}}
                                        </label>
                                    </div>
                                    <input type="text" class="form-control" name="order-name" id="order-name"
                                           value="{{ $order->shipping->name }}"
                                           placeholder="{{ trans('page.full_name')}}"/>
                                    <p class="alert-contact-error" id="p-alert-name" style="display:none;">{{ trans('page.invalid.fullname')}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="order-address-line1"
                                           id="order-address-line1"
                                           value="{{ $order->shipping->address->line1 }}"
                                           placeholder="{{ trans('page.street_address')}}"/>
                                    <p class="alert-contact-error" id="p-alert-address" style="display:none;">{{ trans('page.invalid.address')}}</p>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="order-address-line2"
                                           id="order-address-line2"
                                           value="{{ $order->shipping->address->line2 }}"
                                           placeholder="{{ trans('page.street_address_2')}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div @if(!$validation_error || !in_array('city',$validation_error)) class="col-md-6"
                                     @else class="col-md-6 has-error" @endif>
                                    <input type="text" name="order-city" id="order-city"
                                           class="form-control input-horiziontal"
                                           value="{{ $order->shipping->address->city }}"
                                           placeholder="{{ trans('page.city')}}"/>
                                    <p class="alert-contact-error" id="p-alert-city" @if(!$validation_error || !in_array('city',$validation_error))style="display:none;"@endif>{{ trans('page.invalid.city')}}</p>
                                </div>
                                <div @if(!$validation_error || !in_array('state',$validation_error)) class="col-md-6"
                                     @else class="col-md-6 has-error" @endif>
                                    @if($state)
                                        <select class="form-control select-box" name="order-state" id="order-state">
                                            @foreach($state as $item)
                                                @if($order->shipping->address->state == $item['code'])
                                                    <option value="{{ $item['code'] }}"
                                                            selected="selected">{{ $item['name'] }}</option>
                                                @else
                                                    <option value="{{ $item['code'] }}">{{ $item['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="order-state" id="order-state"
                                               class="form-control"
                                               value="{{ $order->shipping->address->state }}"
                                               placeholder="{{ trans('page.province')}}"/>
                                    @endif
                                    <p class="alert-contact-error" id="p-alert-state" @if(!$validation_error|| !in_array('state',$validation_error))style="display:none;"@endif>{{ trans('page.invalid.state')}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div @if(!$validation_error || !in_array('zip',$validation_error)) class="col-md-6"
                                     @else class="col-md-6 has-error" @endif>
                                    <input type="text" name="order-postal-code" id="order-postal-code"
                                           class="form-control input-horiziontal"
                                           value="{{ $order->shipping->address->postal_code }}"
                                           placeholder="@if($state){{ trans('page.zipcode')}}@else{{ trans('page.postalcode')}}@endif"/>
                                    <p class="alert-contact-error" id="p-alert-postal-code" @if(!$validation_error|| !in_array('zip',$validation_error))style="display:none;"@endif>{{ trans('page.invalid.postalcode')}}</p>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control select-box" name="order-country" id="order-country">
                                        @foreach($country as $item)
                                            @if($order->shipping->address->country == $item['code'])
                                                <option value="{{ $item['code'] }}"
                                                        selected="selected">{{ $item['name'] }}</option>
                                            @else
                                                <option value="{{ $item['code'] }}">{{ $item['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="order-phone" id="order-phone"
                                           value="{{ $order->shipping->phone }}"
                                           placeholder="{{ trans('page.phone_number')}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox checkbox-warning">
                                        <input id="checkbox5" type="checkbox" name="ship-as-gift" class="styled">
                                        <label for="checkbox5">
                                            {{ trans('page.ship_as_gift')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr/>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <div class="radio radio-warning">
                                        <input type="radio" name="payment-method" id="radio1" value="0" checked>
                                        <label for="radio1">
                                            Pay via credit card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="radio radio-warning">
                                        <input type="radio" name="payment-method" id="radio2" value="1">
                                        <label for="radio2">
                                            Pay via Paypal
                                        </label>
                                    </div>
                                </div>


                                @if($payment_card_type == 'stripe')
                                    <div class="col-md-12 payment-cart">
                                        <input type="text" class="form-control" id="order-card-no"
                                               placeholder="{{ trans('page.card_number')}}" data-stripe="number"
                                               maxlength="23"/>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <input type="text" id="order-card-mm" class="form-control"
                                               placeholder="MM" data-stripe="exp_month" maxlength="2"/>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <input type="text" id="order-card-yy" class="form-control"
                                               placeholder="YYYY" data-stripe="exp_year" maxlength="4"/>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <input type="text" id="order-card-cvc" class="form-control"
                                               placeholder="CVC" data-stripe="cvc" maxlength="4"/>
                                    </div>
                                @else
                                    <div class="col-md-12 payment-cart">
                                        <div class="form-control" id="order-card-no"></div>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <div class="form-control" id="order-card-mm"></div>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <div class="form-control" id="order-card-yy"></div>
                                    </div>
                                    <div class="col-xs-4 payment-cart">
                                        <div class="form-control" id="order-card-cvc"></div>
                                    </div>
                                @endif
                                <div class="col-md-12 payment-cart">
                                    <p class="alert-contact-error" id="p-alert-card-no" style="display:none;">{{ trans('page.invalid.card_no')}}</p>
                                    <p class="alert-contact-error" id="p-alert-card-mm" style="display:none;">{{ trans('page.invalid.card_mm')}}</p>
                                    <p class="alert-contact-error" id="p-alert-card-yy" style="display:none;">{{ trans('page.invalid.card_yy')}}</p>
                                    <p class="alert-contact-error" id="p-alert-card-cvc" style="display:none;">{{ trans('page.invalid.card_cvc')}}</p>
                                    <p class="alert-contact-error" id="p-alert-payment-error" style="display:none;"></p>
                                </div>
                                <div class="col-md-12 checkbox" style="display:none;">
                                    <label><input type="checkbox" value=""
                                                  disabled>{{ trans('page.save_payment_info')}}</label>
                                </div>
                            </div>

                            <!--CREDIT CART PAYMENT END-->
                            <button type="submit" class="btn-hilight-color btn-loading-progess btn-checkout submit-order submit-order-cart"
                                    name="submit-order" value="true"><span class="loading-progess"></span>{{ trans('page.place_your_order')}}</button>
                            <button type="submit"
                                    class="btn-hilight-color btn-loading-progess btn-checkout submit-order submit-order-paypal"
                                    name="submit-order" value="true"
                                    style="display:none;"><span class="loading-progess"></span>{{ trans('page.pay_with_paypal')}}</button>
                            <div class="text-left term-checkout">
                                <p class="submit-order-cart">
                                By clicking 'Place Your Order' you agree to our <a href="/privacy" target="_blank">privacy policy</a> and <a href="/terms" target="_blank">terms of service</a>. You also agree to receive periodic email updates, discounts, and special offers.
                                </p>
                                <p class="submit-order-paypal"
                                   style="display:none;">
                                   By clicking 'Pay with Paypal' you agree to our <a href="/privacy" target="_blank">privacy policy</a> and <a href="/terms" target="_blank">terms of service</a>. You also agree to receive periodic email updates, discounts, and special offers
                                </p>
                                <div class="checkbox checkbox-warning">
                                    <input id="checkbox6" name="check_subscribe" type="checkbox" class="styled" value="1" checked>
                                    <label for="checkbox6">
                                        {{ trans('page.like_to_receive_email_about_new_product')}}
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-md-pull-6 col-sm-pull-6">
                            <h4 class="heading4-form">{{ trans('page.order_summary')}}
                                <span class="pull-right">
                                    <small><a class="afix-1 add-another-style"
                                              href="/shop/cart"><b>{{ trans('page.modify_order')}}</b></a>
                                    </small>
                                </span>
                            </h4>
                            @foreach($order->items as $item)
                                <hr/>
                                <div class="form-group">
                                    <div class="col-sm-2 col-xs-2 img-cart-item">
                                        <img class="img-responsive"
                                             src="<?php echo str_replace('.png', '_s.png', $item->variant_image);?>"
                                             onerror="this.src='{{ $item->variant_image }}'"/>
                                    </div>
                                    <div class="col-sm-10 col-xs-10">
                                        <div class="form-group">
                                            <div class="col-sm-8 col-xs-8">
                                                <b>{{ $item->variant_name }}</b>
                                            </div>
                                            <div class="col-sm-4 col-xs-4 text-right">
                                                <span>{{ $item->quantity }}</span> x
                                                <strong><span>$</span>{{ $item->price }}</strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8 col-xs-8">
                                                <small>{{ trans('page.shipping')}}</small>
                                            </div>
                                            <div class="col-sm-4 col-xs-4 text-right">
                                                <p>+${{ $item->shipping_fee }}</p>
                                            </div>
                                        </div>
                                        @isset($item->promotion)
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-8">
                                                    <small>{{ trans('page.discount')}}</small>
                                                </div>
                                                <div class="col-sm-4 col-xs-4 text-right">
                                                    <p>-${{ $item->promotion->discount->amount }}</p>
                                                </div>
                                            </div>
                                        @endisset
                                    </div>
                                </div>
                            @endforeach
                            <hr/>
                            <div>
                                <strong>{{ trans('page.order_total')}}</strong>
                                <div class="pull-right price"><span
                                            class="price-value price-show-ci" data-content="{{ $order->amount }}">${{ $order->amount }}</span></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop