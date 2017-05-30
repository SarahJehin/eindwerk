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
//get authenticated user for 404
Route::get('get_authenticated_user', 'UserController@get_authenticated_user');

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
    Route::get('get_allowed_update_roles', 'UserController@get_allowed_update_roles');
    Route::get('get_user_roles', 'UserController@get_user_roles');
    Route::post('update_user_role', 'UserController@update_user_role');

    //winterhours
    Route::get('winterhours_overview', 'WinterhourController@get_winterhours_overview');
    Route::get('add_winterhour', 'WinterhourController@add_winterhour');
    Route::post('add_winterhour', 'WinterhourController@create_winterhour');
    Route::get('edit_winterhour/{id}', 'WinterhourController@edit_winterhour');
    Route::post('edit_winterhour', 'WinterhourController@update_winterhour');
    Route::get('availabilities/{id}/{user_id?}', 'WinterhourController@edit_availabilities');
    Route::post('update_availability', 'WinterhourController@update_availability');
    Route::get('generate_scheme/{id}', 'WinterhourController@generate_scheme');
    Route::get('save_scheme/{id}', 'WinterhourController@save_scheme');
    Route::get('get_winterhour_dates', 'WinterhourController@get_winterhour_dates');
    Route::get('get_winterhour_status', 'WinterhourController@get_winterhour_status');

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

Route::group(['middleware' => ['auth', 'trainer']], function () {
    //exercises
    Route::get('exercises_overview', 'ExerciseController@exercises_overview');
    Route::get('add_exercise', 'ExerciseController@add_exercise');
    Route::post('add_exercise', 'ExerciseController@create_exercise');
    Route::get('exercise_details/{id}', 'ExerciseController@exercise_details');
    Route::get('deny_exercise/{id}', 'ExerciseController@deny_exercise');
    Route::get('approve_exercise/{id}', 'ExerciseController@approve_exercise');
    Route::get('get_filtered_exercises', 'ExerciseController@get_filtered_exercises');
});
