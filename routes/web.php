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
Route::get('/', function () {
    user::find('1')->notify(new testnotification);
});

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('admin/users/role/{role_id}', 'Admin\UsersController@indexByRoleId');


Route::get('/admin', 'Admin\AdminController@index');
Route::resource('admin/roles', 'Admin\RolesController');
Route::resource('admin/permissions', 'Admin\PermissionsController');
Route::resource('admin/users', 'Admin\UsersController');
Route::resource('admin/pages', 'Admin\PagesController');
Route::resource('admin/activitylogs', 'Admin\ActivityLogsController')->only([
    'index', 'show', 'destroy'
]);
Auth::routes();
Route::resource('admin/settings', 'Admin\SettingsController');
Route::get('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@getGenerator']);
Route::post('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@postGenerator']);
Route::resource('admin/categories', 'Admin\CategoriesController');

/*  admin subcategories routes  */

Route::get('admin/subcategories/{id}', 'Admin\SubcategoryController@index');
Route::get('admin/subcategory/{id}', 'Admin\SubcategoryController@create');
//Route::get('admin/showpage', 'Admin\SubcategoryController@getsubcategorypage');
Route::post('admin/save', 'Admin\SubcategoryController@store');
Route::get('admin/view/{id}', 'Admin\SubcategoryController@show');
Route::get('admin/subcategory/{id}/edit', 'Admin\SubcategoryController@edit');
Route::patch('admin/subcategoryupdate/{id}', 'Admin\SubcategoryController@update');
Route::delete('admin/subcategorydelete/{id}', 'Admin\SubcategoryController@destroy');


/*  admin dashboard routes  */
Route::get('admin/dashboard', 'Admin\AdminController@index');
Route::get('admin/display', 'Admin\AdminController@display');
Route::get('admin/show/{id}', 'Admin\AdminController@show');
Route::get('admin/formcreate', 'Admin\AdminController@createform');
Route::post('admin/freelancersave', 'Admin\AdminController@store');
Route::get('admin/freelancer/{id}/edit', 'Admin\AdminController@edit');
Route::patch('admin/freelancerupdate/{id}', 'Admin\AdminController@update');
Route::delete('admin/freelancerdelete/{id}', 'Admin\AdminController@destroy');


/*  client dashboard routes  */
Route::get('admin/clientdashboard', 'Admin\ClientController@index');
Route::get('admin/clientform', 'Admin\ClientController@create');
Route::post('admin/clientsave', 'Admin\ClientController@store');
Route::get('admin/clientshow/{id}', 'Admin\ClientController@show');
Route::get('admin/client/{id}/edit', 'Admin\ClientController@edit');
Route::patch('admin/clientupdate/{id}', 'Admin\ClientController@update');
Route::delete('admin/clientdelete/{id}', 'Admin\ClientController@destroy');





Route::resource('admin/portfolios', 'Admin\\portfoliosController');

Route::get('login/{provider}', 'SocialController@redirectToProvider');
Route::get('{provider}/callback', 'SocialController@handleProviderCallback');

