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

Route::group(['prefix' => 'data'], function () {
    Route::get('/urls', [
        'as' => 'data.urls', 'uses' => 'UrlsController@index'
    ]);
    
    Route::post('/urls-add', [
        'as' => 'data.urls.add', 'uses' => 'UrlsController@addProduct'
    ]);

    Route::get('/asins', [
        'as' => 'data.asins', 'uses' => 'AsinController@index'
    ]);

    Route::post('/asins-add', [
        'as' => 'data.asins.add', 'uses' => 'AsinController@addAsins'
    ]);

    Route::post('/asins-delete', [
        'as' => 'data.asins.delete', 'uses' => 'AsinController@deleteAsin'
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
    Route::get('/yahoo-auction', [
        'as' => 'csv.yahoo-auction', 'uses' => 'ProfitsController@index'
    ]);

    Route::post('/yahoo-auction/putDateCsv', [
        'as' => 'csv.yahoo-auction.putDateCsv', 'uses' => 'ProfitsController@putDateCsv'
    ]);

    Route::post('/yahoo-auction/putPageCsv', [
        'as' => 'csv.yahoo-auction.putPageCsv', 'uses' => 'ProfitsController@putPageCsv'
    ]);

    Route::get('/amazon', [
        'as' => 'csv.amazon', 'uses' => 'AsinController@viewCsvData'
    ]);

    Route::post('/amazon/getimport', [
        'as' => 'csv.amazon.getimport', 'uses' => 'AsinController@getImportName'
    ]);

    Route::post('/amazon/putAsinCsv', [
        'as' => 'csv.amazon.putAsinCsv', 'uses' => 'AsinController@putAsinCsv'
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
