<?php
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::group(['middleware' => 'web', 'namespace' => 'Modules\Shop\Http\Controllers'], function () {
        Route::get('/', 'HomeController@index');
        Route::any('/track', 'HomeController@track');
        Route::any('/checkpromotion', 'HomeController@promotion');
        Route::get('/reportAnalytics', 'HomeController@reportAnalytics');
    });

    Route::group(['middleware' => 'web', 'prefix' => 'shop', 'namespace' => 'Modules\Shop\Http\Controllers'], function () {
        Route::get('/', 'ShopController@index');
        Route::get('/search', 'ShopController@search');
        Route::any('/checkout', 'ShopController@payment');
        Route::any('/cart', 'ShopController@payment');
        Route::get('/thankyou', 'ShopController@thankyou');
        Route::get('/{url}', 'ShopController@index');
    });

    Route::group(['middleware' => 'web', 'prefix' => 'stores', 'namespace' => 'Modules\Shop\Http\Controllers'], function () {
        Route::get('/{url}', 'StoreController@index');
    });
});
