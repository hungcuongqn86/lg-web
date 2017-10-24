@extends('layouts.master')

@section('content')
    <!-- Page Content -->
    <div class="list=products">
        <div class="container">
            <!-- List products -->
            <div class="list-item-products">
                <div class="error_page">
                    <h1>500</h1>
                    <h2>{{ trans('page.500_error')}}!</h2>
                    <p>{{ trans('page.500_error_text')}}</p>
                    <p>{{ trans('page.try_back_later')}}<p>
                </div>
            </div>
        </div>
    </div>
@stop
