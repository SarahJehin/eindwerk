<?php

namespace App\Http\Controllers;
use App\Activity;
use App\User;
use DB;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    //return all activities, that are visible, to show on the calendar on the homepage
    public function get_calendar_activities(Request $request) {
        $calendar_activities = Activity::select('id', 'title', 'start', 'category_id', 'is_visible')->with(['category' => function($query) {
		    $query->select('id', 'color as backgroundColor');
		}])->where('is_visible', 1)->get()->toArray();

        $new_calendar_activities = array();
        foreach($calendar_activities as $act) {
        	$act['backgroundColor'] = $act['category']['backgroundColor'];
        	$act['borderColor'] = $act['category']['backgroundColor'];
            $act['url'] = url('/') . '/activity_details/' . $act['id'];
        	array_push($new_calendar_activities, $act);
        }

        return $new_calendar_activities;
    }

    //return the 5 first users who match the search results
    public function get_matching_users(Request $request) {
        $searchstring   = $request->searchstring;
        $not_ids        = $request->not_ids;

        //select birth_date as well, for when you have two users with the same name
        $matching_users = User::select('id', 'first_name', 'last_name', 'birth_date')
                                ->whereNotIn('id', $not_ids)
                                ->where(function($query) use ($searchstring) {
                                    $query->where('first_name', 'like', '%'.$searchstring.'%')
                                            ->orWhere('last_name', 'like', '%'.$searchstring.'%')
                                            ->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%'.$searchstring.'%')
                                            ->orWhere(DB::raw("CONCAT(`last_name`, ' ', `first_name`)"), 'like', '%'.$searchstring.'%');
                                })
                                ->orderBy('last_name')
                                ->orderBy('first_name')
                                ->limit(5)
                                ->get();

        return $matching_users;
    }

    //update paid status
    public function update_activity_participant_status(Request $request) {
        //dd($request);
        $user = User::find($request->user_id);
        $is_checked = false;
        $is_checked = ($request->is_checked == 'true');
        if($is_checked) {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 2]);
        }
        else {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 1]);
        }
        return $user->activities->where('id', $request->activity_id)->first()->pivot->status;
    }

    public function update_activity_visibility(Request $request) {
        //
        $activity = Activity::find($request->activity_id);
        $activity->is_visible = $request->is_visible;
        $activity->save();
        return "success";
    }

}
