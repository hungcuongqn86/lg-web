@extends('layouts.master')

@section('content')
    <h1 style="display:none;">Track order</h1>
    <div class="container" style="min-height: calc(100% - 378px);">
        <div class="row">
            <div class="col-md-8">
                <h2 class="larg-title-box track-title">{{ trans('page.track_your_order')}}</h2>
                <form action="/track" method="get" id="form-submit-track-order">
                    <p class="track-desc">{{ trans('page.track_order_text')}}</p>
                    <div class="form-group track-control">
                        <input class="form-control track-input" type="text" name="pid" id="lookup_number" value="{{$code}}"
                               placeholder="Lookup number">
                        @if($error_lookup)
                            <span class="confirm">?</span>
                        @endif
                        <button type="submit" class="track-btn btn-hilight-color">{{ trans('page.track_order')}}</button>
                    </div>

                    @if($error_lookup)
                        <p class="track-error">{{ trans('page.not_found_order')}}</p>
                        <p class="track-help">Forget my order number?</p>
                    @endif
                </form>
            </div>
            <div class="col-md-4" style="padding: 60px;">
                <img class="img-responsive" src="{{asset('images/shipping-art@3x.png')}}"/>
            </div>
        </div>
    </div>
@stop
