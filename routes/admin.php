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
Route::get('admin/users/role/{role_id}', 'Admin\UsersController@indexByRoleId');
Route::resource('admin/roles', 'Admin\RolesController');
Route::resource('admin/users', 'Admin\UsersController');
Route::get('admin/dashboard', 'Admin\AdminController@index');
Route::get('admin/display', 'Admin\AdminController@display');
Route::resource('admin/products', 'Admin\ProductsController');
