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
    Route::post('coach/update', 'API\AuthController@CoachUpdate');
    Route::post('athlete/update', 'API\AuthController@AtheleteUpdate');
    Route::post('organiser/update', 'API\AuthController@OrganiserUpdate');

});

Route::post('coach/register', 'API\AuthController@CoachRegister');
Route::post('athlete/register', 'API\AuthController@AtheleteRegister');
Route::post('organiser/register', 'API\AuthController@OrganiserRegister');
Route::post('login', 'API\AuthController@Login');
Route::post('reset-password', 'API\AuthController@resetPassword');

Route::post('services', 'API\ServicesController@getitems');


