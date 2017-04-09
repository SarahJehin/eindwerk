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

Route::get('/', 'HomeController@index');


Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => ['auth']], function () {
    Route::get('add_activity', 'ActivityController@add_activity');
    Route::post('add_activity', 'ActivityController@create_activity');

    Route::get('activities_overview', 'ActivityController@activities_overview');
    Route::get('activity_details/{id}', 'ActivityController@activity_details');
});
