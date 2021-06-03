<?php

use App\Http\Middleware\checkAuthorized;
use App\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;

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

Route::group(['domain' => env('APP_URL')], function () {
    Route::get('/', 'Auth\AuthenticateUserController@authorizeMall');
    Route::get('/token', 'Auth\AuthenticateUserController@authorizeMall');
    Route::get('/forbidden', function () {
        abort(403, 'Unauthorized action.');
    })->name('forbidden');

    Route::middleware(array(checkAuthorized::class))->group(function () {
        Route::get('/dashboard', 'DashboardController@getMarkedProducts');
        Route::get('/products', 'ProductController@getProducts');
        Route::delete('/product/{iProductId}', 'ProductController@deleteProduct');
        Route::post('/product/{iProductId}', 'ProductController@saveProductApp');
        Route::get('/categories', 'CategoryController@getCategories');
        Route::post('/scripttag', 'ProductController@toggleScriptTag');
        Route::get('/scripttag/status', 'ProductController@getScriptTagStatus');
        Route::get('/orders', 'OrdersController@displayList');
        Route::get('/search', 'OrdersController@searchProduct');
        Route::get('/details', 'OrdersController@showOrderDetails');
        Route::post('/order/list', 'OrdersController@getOrderList');
        Route::get('/order/count', 'OrdersController@countOrders');
        Route::get('/order/info', 'OrdersController@getOrderDetails');
        Route::post('/productList', 'OrdersController@getProductList');
        Route::post('/bundle', 'OrdersController@getBundle');
    });


    Route::middleware(array(HandleCors::class))->group(function () {
        Route::get('/products/saved', 'FrontController@getSavedProduct');
        Route::get('/scripttag', 'FrontController@getScriptTagStatus');
    });

});

Route::get('/scripttag/template-app.js', function() {
    return response()->file(public_path('js/template-app.js'), [
        'Access-Control-Allow-Origin' => '*',
        'Content-Type' => 'application/javascript',
    ]);
});
