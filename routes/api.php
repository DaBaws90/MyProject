<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'API\RegisterController@login');
    Route::post('register', 'API\RegisterController@register');
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'API\RegisterController@logout');
        Route::get('user', 'API\RegisterController@user');
        Route::post('user', 'API\RegisterController@editUser');

        // Route::resource('products', 'API\ProductController');
    });
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('/products/refSearch', 'API\ProductController@references');
    Route::post('/products/catSearch', 'API\ProductController@categories');
    Route::get('/products', 'API\ProductController@index');

    // Route::resource('products', 'API\ProductController'); 
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group('auth:api')->group( function () { 
//     Route::resource('products', 'API\ProductController'); 
// });
