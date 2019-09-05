<?php

/*
  |--------------------------------------------------------------------------
  | Web Admin Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
Route::get('/', function () {
    if (\Auth::check()) {
        return redirect('/admin');
    }
    return redirect()->route('admin.login');
});
Route::get('/admin/login', function () {
    if (\Auth::check()) {
        return redirect('/admin');
    }
    return view('auth.login');
})->name('admin.login');

/* * *************************************************************************************************************** */
/* * ********************************Default Routes**************************************************************** */
/* * *************************************************************************************************************** */
Route::resource('admin/settings', 'Admin\SettingsController');
Route::get('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@getGenerator']);
Route::post('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@postGenerator']);
Route::resource('admin/permissions', 'Admin\PermissionsController');
Route::resource('admin/pages', 'Admin\PagesController');
Route::resource('admin/activitylogs', 'Admin\ActivityLogsController')->only([
    'index', 'show', 'destroy'
]);
/* * *************************************************************************************************************** */
/* * ********************************Default Routes end************************************************************* */
/* * *************************************************************************************************************** */

Route::get('/admin', 'Admin\AdminController@index');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('users/role/{role_id}', 'UsersController@indexByRoleId');
    Route::post('users/change-status', 'UsersController@changeStatus')->name('user.changeStatus');
    Route::resource('roles', 'RolesController');
    Route::resource('users', 'UsersController');
    Route::get('dashboard', 'AdminController@index');
    Route::get('display', 'AdminController@display');
    Route::resource('products', 'ProductsController');
    Route::resource('/appointments', 'AppointmentsController');
    Route::post('appointments/salon-service', 'AppointmentsController@getServicebySalon')->name('appointment.getservice');
    Route::resource('orders', 'OrderController');
    Route::resource('subscriptions', 'SubscriptionController');
    Route::resource('events', 'EventsController');
    Route::resource('services', 'ServicesController');
    Route::resource('spaces', 'SpacesController');
});
