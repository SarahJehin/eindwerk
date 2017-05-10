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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get_activities', 'ApiController@get_calendar_activities');
Route::get('/get_matching_users', 'ApiController@get_matching_users');
Route::post('/update_activity_participant_status', 'ApiController@update_activity_participant_status');
Route::post('/update_activity_visibility', 'ApiController@update_activity_visibility');
Route::post('/update_profile', 'ApiController@update_profile');
