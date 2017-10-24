{{--@extends('blog::layouts.master')--}}
@extends('layouts.master')

@section('content')
    <h1>Blog Module</h1>
    <h4>App: {{config('config.app_name')}}</h4>
    <p>This is from helper: {{_test('21212')}}</p>
    <p>This is from Lib: {{Lib::_test()}}</p>
    <p>Lang: {{ trans('common.test')}}</p>
    <p>
        This view is loaded from module: {!! config('blog.name') !!}
    </p>
@stop
