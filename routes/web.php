<?php

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
| Define the routes for your Frontend pages here
|
*/

Route::get('/', [
    'as' => 'home', 'uses' => 'UrlsController@home'
]);

Route::group(['prefix' => 'urls'], function () {
    Route::get('/', [
        'as' => 'urls', 'uses' => 'UrlsController@index'
    ]);
    
    Route::post('/add', [
        'as' => 'urls.add', 'uses' => 'UrlsController@addProduct'
    ]);
});

Route::group(['prefix' => 'currencys'], function () {
    Route::get('/', [
        'as' => 'currencys', 'uses' => 'CurrencysController@index'
    ]);

    Route::post('/updateCurrency', [
        'as' => 'currencys.updateCurrency', 'uses' => 'CurrencysController@updateCurrency'
    ]);

    Route::post('/updateProfit', [
        'as' => 'currencys.updateProfit', 'uses' => 'CurrencysController@updateProfit'
    ]);
});

Route::group(['prefix' => 'csv'], function () {
    Route::get('/', [
        'as' => 'csv', 'uses' => 'ProfitsController@index'
    ]);

    Route::post('/putCsv', [
        'as' => 'csv.putCsv', 'uses' => 'ProfitsController@putCsv'
    ]);
});

Route::group(['prefix' => 'auto'], function () {
    Route::get('/', [
        'as' => 'auto', 'uses' => 'AutoFunController@index'
    ]);

    Route::post('/createProduct', [
        'as' => 'auto.createProduct', 'uses' => 'AutoFunController@createProduct'
    ]);

    Route::post('/deleteProduct', [
        'as' => 'auto.deleteProduct', 'uses' => 'AutoFunController@deleteProduct'
    ]);
});
