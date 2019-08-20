<?php

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

Route::post('/salon-admin/login', 'Auth\LoginController@salonAdminCheckAuth')->name('salon-admin.login');
Route::get('/salon-admin', function () {
    if (\Auth::check()) {
        return redirect()->route('salon-admin.dashboard');
    }
    return redirect()->route('salon-admin.login');
});
Route::get('/salon-admin/login', function () {
    if (\Auth::check()) {
        return redirect('/salon-admin');
    }
    return view('auth.salon-admin-login');
})->name('salon-admin.login');

Route::get('salon-admin/dashboard', 'SalonAdmin\SalonAdminController@index')->name('salon-admin.dashboard');

Route::resource('salon-admin/services', 'SalonAdmin\\ServicesController');
Route::resource('salon-admin/appointments', 'SalonAdmin\\AppointmentsController');

