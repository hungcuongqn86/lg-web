<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::get('/privacy', function () {
        $order_numb = redis_order_number();
        $category_list = redis_category();
        return view('pages.privacy', ['site' => 'privacy', 'orderno' => $order_numb, 'category' => $category_list]);
    });
    Route::get('/terms', function () {
        $order_numb = redis_order_number();
        $category_list = redis_category();
        return view('pages.terms', ['site' => 'terms', 'orderno' => $order_numb, 'category' => $category_list]);
    });
});
