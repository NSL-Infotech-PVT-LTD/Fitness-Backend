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



Route::get('getStripeData', 'API\ApiController@getStripeData');

Route::group(['middleware' => 'auth:api'], function() {


    Route::post('connectWithStripe', 'API\ApiController@connectWithStripe');

    Route::post('coach/update', 'API\AuthController@CoachUpdate');
    Route::post('athlete/update', 'API\AuthController@AtheleteUpdate');
    Route::post('organiser/update', 'API\AuthController@OrganiserUpdate');
    Route::post('events/store', 'API\EventsController@store');
    Route::post('events/update', 'API\EventsController@Update');
    Route::post('spaces/create', 'API\SpacesController@store');
    Route::post('spaces/update', 'API\SpacesController@Update');
    Route::post('spaces/delete', 'API\SpacesController@destroy');
    Route::post('events/organiser/list', 'API\EventsController@getOrganiserEvents');
    Route::post('events/athlete/list', 'API\EventsController@getAthleteEvents');
    Route::post('events/coach/list', 'API\EventsController@getCoachEvents');
    Route::post('spaces/organiser/list', 'API\SpacesController@getOrganiserSpaces');
    Route::post('spaces/athlete/list', 'API\SpacesController@getAthleteSpaces');
    Route::post('spaces/coach/list', 'API\SpacesController@getCoachSpaces');
    Route::post('organisers/list', 'API\AuthController@getOrganisers');
    Route::post('coach/list', 'API\AuthController@getCoaches');
    Route::post('session/store', 'API\SessionController@store');
    Route::post('session/update', 'API\SessionController@Update');
    Route::post('session/delete', 'API\SessionController@destroy');
    Route::post('session/organiser/list', 'API\SessionController@getOrganiserSession');
    Route::post('session/coach/list', 'API\SessionController@getCoachSession');
    Route::post('session/athlete/list', 'API\SessionController@getAthleteSession');
    Route::post('event/details', 'API\EventsController@getitem');
    Route::post('booking/store', 'API\BookingController@store');
    Route::post('booking/space/store', 'API\BookingController@spacestore');
    Route::post('booking/athlete/list', 'API\BookingController@getBookingsAthlete');
    Route::post('booking/list/all', 'API\BookingController@getBookingsAll');
    Route::post('booking/space/list', 'API\BookingController@getSpaceBookings');
    Route::post('booking/all/list/all', 'API\BookingController@getBookingscoachOrg');
    Route::post('received/transactions', 'API\BookingController@transactions');
    Route::post('booking/organiser/list', 'API\BookingController@getBookingsOrganiser');
    Route::post('booking/coach/list', 'API\BookingController@getBookingsCoach');
    Route::post('booking/details', 'API\BookingController@getitem');
    Route::post('both/bookings', 'API\BookingController@getbothbookings');
    Route::post('session/details', 'API\SessionController@getitem');
    Route::post('space/details', 'API\SpacesController@getitem');
    Route::post('coach/details', 'API\AuthController@getcoach');
    Route::post('organiser/details', 'API\AuthController@getorganiser');
    Route::post('organiser/coach/store', 'API\OrganiserCoachController@store');
    Route::post('organiser/coach/update', 'API\OrganiserCoachController@update');
    Route::post('organiser/coach/list', 'API\OrganiserCoachController@getitems');
    Route::post('organiser/coach/athlete/list', 'API\OrganiserCoachController@getOrganiseritems');
    Route::post('booking/rating', 'API\BookingController@rating');
    Route::post('notifications', 'API\NotificationController@getnotifications');
    Route::get('dashboard', 'API\NotificationController@getUserDashboard');
    Route::get('notification/read', 'API\NotificationController@MarkReadNotication');
    Route::get('terms', 'API\ConfigurationController@getTerms');
    Route::get('logout', 'API\AuthController@logout');
    Route::get('notify/toggle', 'API\AuthController@toggleNotifyUser');
    Route::post('availability', 'API\BookingController@getavailability');
    Route::get('configuration/{column}', 'API\ConfigurationController@getConfigurationColumn');
    Route::post('password/change', 'API\AuthController@changePassword');
    Route::post('contact/store', 'API\ContactController@store');
    Route::post('received/bookings', 'API\BookingController@receivedBookings');
    Route::post('coach/availability', 'API\CoachBookingController@getavailable');
    Route::post('coach/booking', 'API\CoachBookingController@store');
    Route::post('booking/coach/list', 'API\CoachBookingController@getCoachBookings');
    Route::post('getAtheleteCoachBookings', 'API\CoachBookingController@getAtheleteCoachBookings');
});
Route::post('coach/detail/any', 'API\AuthController@getcoachdetail');

Route::post('reset/password', 'API\AuthController@resetPassword');
Route::post('coach/register', 'API\AuthController@CoachRegister');
Route::post('athlete/register', 'API\AuthController@AtheleteRegister');
Route::post('organiser/register', 'API\AuthController@OrganiserRegister');
Route::post('login', 'API\AuthController@Login');
Route::post('services', 'API\ServicesController@getitems');
Route::post('sports', 'API\SportController@getitems');
Route::get('about/us', 'API\ConfigurationController@getaboutus');
Route::post('auth/check', 'API\AuthController@AuthCheck');



