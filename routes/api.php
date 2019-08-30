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
    Route::post('salon/update', 'API\AuthController@Salonupdate');
});

Route::post('salon/register', 'API\AuthController@Salonregister');
Route::post('customer/register', 'API\AuthController@CustomerRegister');
Route::post('customer/update', 'API\AuthController@CustomerUpdate');
Route::post('login', 'API\AuthController@Login');




