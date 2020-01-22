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
Auth::routes();

Route::resource('admin/configuration', 'Admin\\ConfigurationController');
Route::resource('admin/configuration', 'Admin\\ConfigurationController');
Route::resource('admin/configuration', 'Admin\\ConfigurationController');
Route::resource('admin/contact_us', 'Admin\\Contact_usController');
Route::resource('admin/contact', 'Admin\\ContactController');
Route::resource('admin/contact', 'Admin\\ContactController');
Route::resource('admin/contact', 'Admin\\ContactController');
Route::resource('admin/contact', 'Admin\\ContactController');
Route::resource('admin/exercise', 'Admin\\ExerciseController');