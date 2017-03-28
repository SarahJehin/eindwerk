<?php

namespace App\Http\Controllers;
use App\Activity;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function get_calendar_activities(Request $request) {
        $calendar_activities = Activity::select('id', 'title', 'startdate as start', 'category_id')->with(['category' => function($query) {
		    $query->select('id', 'color as backgroundColor');
		}])->get()->toArray();

        $new_calendar_activities = array();
        foreach($calendar_activities as $act) {
        	$act['backgroundColor'] = $act['category']['backgroundColor'];
        	$act['borderColor'] = $act['category']['backgroundColor'];
        	array_push($new_calendar_activities, $act);
        }

        //dd($calendar_activities, $new_calendar_activities);
        return $new_calendar_activities;
    }
}
