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
    //users
    //activities
    Route::get('activities_overview', 'ActivityController@activities_overview');
    Route::get('activity_details/{id}', 'ActivityController@activity_details');
    Route::post('sign_up_for_activity', 'ActivityController@sign_up_for_activity');
    Route::post('sign_out_for_activity', 'ActivityController@sign_out_for_activity');

    Route::get('scoreboard', 'ActivityController@get_scoreboard');
    //members
    Route::get('members_overview', 'UserController@get_members_overview');
    Route::get('download_members_as_excel', 'UserController@download_members_as_excel');
    Route::get('search_members', 'UserController@search_members');
    Route::post('update_profile_pic', 'UserController@update_profile_pic');

    //winterhours
    Route::get('winterhours_overview', 'WinterhourController@get_winterhours_overview');
    Route::get('add_winterhour', 'WinterhourController@add_winterhour');
    Route::post('add_winterhour', 'WinterhourController@create_winterhour');
    Route::get('edit_winterhour/{id}', 'WinterhourController@edit_winterhour');
    Route::get('availabilities/{id}', 'WinterhourController@edit_availabilities');
});

Route::group(['middleware' => ['auth', 'youth_board']], function () {
	//activities
    Route::get('add_activity', 'ActivityController@add_activity');
    Route::post('add_activity', 'ActivityController@create_activity');
    Route::get('edit_activity/{id}', 'ActivityController@edit_activity');
    Route::post('update_activity', 'ActivityController@update_activity');
    Route::post('delete_activity', 'ActivityController@delete_activity');
    Route::get('activities_list', 'ActivityController@get_activities_list');
    Route::get('activity_participants/{id}', 'ActivityController@get_activity_participants');
    Route::get('download_participants_as_excel/{id}', 'ActivityController@download_participants_as_excel');

    //member management
    Route::post('import_members', 'UserController@import_members');
});
