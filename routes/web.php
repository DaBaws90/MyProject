<?php

use Illuminate\Support\Facades\Storage;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

// AUTHENTICATION ROUTES ----------------------------------------------------------------------------------------------------
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::group(['middleware' => 'isAdmin'], function(){
    // Registration Routes use isAdmin middleware...
    Route::get('register', 'UserController@showRegisterForm')->name('register');
    Route::post('register', 'UserController@register')->name('registerUser');

    Route::post('/users/disable', 'UserController@disable')->name('disable');
    Route::get('/users/verify/{id}', 'UserController@verify')->name('verify');

    // // USER ROUTES -----------------------------------------------------------------------------------------------------------
    // Route::get('/users/profile', 'UserController@profileView')->name('profile');
    // Route::get('/users/uploads/{id}/download/{browser?}', 'UserController@download')->name('download');
    // // Datatables for User
    // Route::get('/users/index', 'UserController@index')->name('users.index');
    // Route::get('/users/testeo', 'UserController@getData')->name('users.index.datatables');

    // Route::resource('users', 'UserController');
    
});


// ROUTES WHICH REQUIRES AUTHENTICATION -------------------------------------------------------------------------------------
Route::group(['middleware' => 'auth'], function () {

    Route::get('/home', 'HomeController@index')->name('home');


    // USER ROUTES -----------------------------------------------------------------------------------------------------------
    Route::get('/users/profile', 'UserController@profileView')->name('profile');
    Route::get('/users/uploads/{id}/download/{browser?}', 'UserController@download')->name('download');
    Route::get('/users/editProfileView', 'UserController@editProfileView')->name('editProfileView');
    
    // Redirects to proper URL in case you were accessing via GET instead of POST
    Route::get('/users/editProfile', function() {
        return redirect('/');
    });
    Route::post('/users/editProfile', 'UserController@editProfile')->name('editProfile');

    // Datatables for User
    // Route::get('/users/index', 'UserController@index')->name('users.index'); --- NO NEED FOR THIS ONE ---
    Route::get('/users/testeo', 'UserController@getData')->name('users.index.datatables');

    Route::resource('users', 'UserController');

    
    // PRODUCTS ROUTES ------------------------------------------------------------------------------------------------------

    // Redirect to home in case GET request to specific URL
    Route::get('/products/results/referencesSearch', function(){
        return redirect()->route('home');
    });
    Route::post('/products/results/referencesSearch', 'ProductController@references')->name('products.results.refSearch');

    // Redirect to home in case GET request to specific URL
    Route::get('/products/results/categoriesSearch', function(){
        return redirect()->route('home');
    });
    Route::post('/products/results/categoriesSearch', 'ProductController@categories')->name('products.results.catSearch');

    // Redirect to home in case GET request to specific URL
    Route::get('/products/pdfUpload', function(){
        return redirect()->route('home');
    });
    Route::post('/products/pdfUpload/{newFile?}', 'ProductController@pdfUpload')->name('upload');

    // Redirect to home in case GET request to specific URL
    Route::get('/products/alternativeBudget', function() {
        return redirect()->route('home');
    });
    Route::post('/products/alternativeBudget', 'ProductController@generateAlternativeBudget')->name('alternativeBudget');

    Route::post('/products/choices', 'ProductController@showAlternativeResults')->name('choices');

    // Resource controller methods routes
    Route::resource('products', 'ProductController');


    // UPLOADS ROUTES -------------------------------------------------------------------------------------------------------

    // Resource controller methods routes
    Route::resource('uploads', 'UploadController');


});

// DATATABLES TESTING ROUTES
// Route::get('test', 'HomeController@test')->name('datatables');
// Route::get('testeo', 'HomeController@getData')->name('datatables.data');



// Route::get('/index', function() {
//     return view('index');
// });
