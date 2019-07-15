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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('login', 'API\UserController@login');
Route::post('freelancer', 'API\UserController@FreelancerRegister');
Route::post('client', 'API\UserController@ClientRegister');
Route::group(['middleware' => 'auth:api'], function() {
    Route::post('freelancers', 'API\UserController@FreelancerList');
    Route::post('portfolio', 'API\UserController@Portfolio');
    Route::post('phone', 'API\UserController@PhoneVerification');
    Route::post('linkdin', 'API\UserController@connectLinkedIn');
});

Route::get('categories', 'API\CategoryController@index');
