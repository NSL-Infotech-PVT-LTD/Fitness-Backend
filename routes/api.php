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
    Route::post('events/store', 'API\EventsController@store');
    Route::post('events/update', 'API\EventsController@Update');
    Route::post('spaces/create', 'API\SpacesController@store');
    Route::post('spaces/update', 'API\SpacesController@Update');
    Route::post('spaces/delete', 'API\SpacesController@destroy');
    Route::get('events/organiser/list', 'API\EventsController@getOrganiserEvents');
    Route::post('events/athlete/list', 'API\EventsController@getAthleteEvents');
    Route::get('events/coach/list', 'API\EventsController@getCoachEvents');
    Route::get('spaces/organiser/list', 'API\SpacesController@getOrganiserSpaces');
    Route::get('spaces/athlete/list', 'API\SpacesController@getAthleteSpaces');
    Route::get('spaces/coach/list', 'API\SpacesController@getCoachSpaces');
    Route::get('organisers/list', 'API\AuthController@getOrganisers');
    Route::get('coach/list', 'API\AuthController@getCoaches');
});

Route::post('coach/register', 'API\AuthController@CoachRegister');
Route::post('athlete/register', 'API\AuthController@AtheleteRegister');
Route::post('organiser/register', 'API\AuthController@OrganiserRegister');
Route::post('login', 'API\AuthController@Login');
Route::post('reset-password', 'API\AuthController@resetPassword');

Route::post('services', 'API\ServicesController@getitems');



