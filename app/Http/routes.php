<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {

    return view('welcome');
   
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});


//home APIs
Route::group(['prefix' => 'guideme/v1','middleware' => 'verifytoken'], function(){

	Route::post('sendcaptcha', 'ApiUserController@sendCaptcha');
	Route::post('user/register', 'ApiUserController@register');
	Route::post('user/login', 'ApiUserController@login');
	Route::post('user/logout', 'ApiUserController@logout');

});
//Route::group(['prefix' => 'home','middleware' => ['auth']],function()

Route::group(['prefix' => 'home','namespace' => 'Home'],function()
{
    Route::get('/','HomeController@index');
    Route::post('/guides', 'HomeController@guides');
    Route::post('/schedules', 'HomeController@schedules');
    Route::post('/orders', 'HomeController@orders');
    
});

//APIs

