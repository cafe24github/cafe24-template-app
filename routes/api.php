<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['domain' => env('API_URL')], function() {
    Route::get('/categories', 'CategoryApiController@getCategories');

    Route::prefix('/products')->group(function() {
        Route::get('/', 'ProductsApiController@getProducts');
        Route::get('/page', 'ProductsApiController@getProductPageData');
        Route::get('/saved', 'ProductsApiController@getSavedProduct');
        Route::get('/count', 'ProductsApiController@countProduct');
        Route::get('/bundle', 'ProductsApiController@getBundleProducts');
    });

    Route::prefix('/product')->group(function() {
        Route::get('/save', 'ProductsApiController@saveProduct');
        Route::get('/{iProductId}', 'ProductsApiController@getProductByNo');
        Route::delete('/{iProductId}', 'ProductsApiController@deleteProduct');
    });

    Route::prefix('/orders')->group(function() {
        Route::get('/', 'OrdersApiController@getAllOrders');
        Route::get('/count', 'OrdersApiController@getOrderCount');
        Route::get('/page', 'OrdersApiController@getOrderPage');
    });
    Route::prefix('/order')->group(function() {
        Route::get('/{order_id}', 'OrdersApiController@getOrderByParams');
        Route::post('/update/{order_id}', 'OrdersApiController@updateOrder');
    });

    Route::get('/scripttag', 'ProductsApiController@getScriptTagStatus');
    Route::post('/scripttag', 'ProductsApiController@toggleScriptTag');
});

