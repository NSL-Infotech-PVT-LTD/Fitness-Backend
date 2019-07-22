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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('freelancer/users', 'API\UserController@getFreelancers');
});



Route::post('freelancer/register', 'API\RegisterController@frelancerRegister');
Route::post('client/register', 'API\RegisterController@ClientRegister');
Route::post('sms', 'API\RegisterController@sendsms');
Route::get('categories', 'API\CategoryController@index');
Route::post('login', 'API\RegisterController@phoneLogin');
Route::post('email', 'API\RegisterController@EmailLogin');
Route::post('otp', 'API\RegisterController@verifyotp');





//Route::post('login', 'API\UserController@login');
//Route::post('freelancer', 'API\UserController@FreelancerRegister');
//Route::post('client', 'API\UserController@ClientRegister');
//Route::group(['middleware' => 'auth:api'], function() {
    //Route::post('freelancers', 'API\UserController@FreelancerList');
    //Route::post('portfolio', 'API\UserController@Portfolio');
   // Route::post('phone', 'API\UserController@PhoneVerification');
   // Route::post('linkdin', 'API\UserController@connectLinkedIn');
//});
