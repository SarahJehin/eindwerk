<?php

namespace App\Http\Controllers;
use App\Activity;
use App\User;
use Auth;
use DB;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /*
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
    */

    /*
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
    */
/*
    //update profile contactinfo (like mobile, phone and email)
    public function update_profile(Request $request) {
        //return $request->new_value;
        //type can be: mobile/phone/email
        $type = $request->type;
        //this new value is already validated in javascript
        $new_value = $request->new_value;
        $user = User::find($request->user_id);
        //dd($user);
        switch ($type) {
            case 'mobile':
                $user->gsm = $new_value;
                break;
            case 'phone':
                $user->phone = $new_value;
                break;
            case 'email':
                $user->email = $new_value;
                break;
        }

        $user->save();
        return 'success';
    }
    */

    /*
    //winterhour
    //swap places for dates en participants
    public function swap_places(Request $request) {

        $user_id1 = intval($request->swap1['user_id']);
        $date_id1 = intval($request->swap1['date_id']);

        $user_id2 = intval($request->swap2['user_id']);
        $date_id2 = intval($request->swap2['date_id']);

        //check if one of the users won't be set as a duplicate participant
        $user_1_already_plays = $this->check_if_user_plays_on_date($user_id1, $date_id2);
        $user_2_already_plays = $this->check_if_user_plays_on_date($user_id2, $date_id1);

        if($user_1_already_plays || $user_2_already_plays) {
            //dd('Één van beide spelers speelt al op de wisseldag');
            return response()->json(['status' => 'failed', 'message' => 'Één van beide spelers speelt al op de wisseldag.']);
        }

        //dd($user_1_already_plays, $user_2_already_plays);
        //echo($user_id1 . ' ' . $date_id1 . ' ' . $user_id2 . ' ' . $date_id2);

        //check if they are both available on the other day
        $user_1_is_available = $this->check_if_user_is_available($user_id1, $date_id2);
        $user_2_is_available = $this->check_if_user_is_available($user_id2, $date_id1);
        //dd($user_1_is_available, $user_2_is_available);

        if(!$user_1_is_available || !$user_2_is_available) {
            //dd('Één van beide spelers is niet beschikbaar op de wisseldag');
            return response()->json(['status' => 'failed', 'message' => 'Één van beide spelers is niet beschikbaar op de wisseldag.']);
        }

        //if all checks were passed
        //switch the entries in the date_user table
        $this->switch_user($user_id1, $date_id1, $date_id2);
        $this->switch_user($user_id2, $date_id2, $date_id1);
        //dd(User::find($user_id1), User::find($user_id2));
        //dd("doesn't work does it?");
        return response()->json(['status' => 'success', 'message' => 'Spelers werden gewisseld.']);

        //dd($request);
        //json_decode($request);
        //dd($request->swap1['user_id']);
        //return response()->json(['blib' => $request->test]);
        return response()->json([$request->swap1['date_id']]);
        //return $request;
    }

    public function check_if_user_plays_on_date($user_id, $date_id) {
        $user = User::find($user_id);
        $user_plays_on_date = $user->dates->where('id', $date_id)->where('pivot.assigned', 1)->first();
        if($user_plays_on_date) {
            $user_plays_on_date = true;
        }
        else {
            $user_plays_on_date = false;
        }
        return $user_plays_on_date;
    }

    public function check_if_user_is_available($user_id, $date_id) {
        $user = User::find($user_id);
        $user_is_available = $user->dates->where('id', $date_id)->where('pivot.available', 1)->first();
        if($user_is_available) {
            $user_is_available = true;
        }
        else {
            $user_is_available = false;
        }
        return $user_is_available;
    }

    public function switch_user($user_id, $old_date, $new_date) {
        $user = User::find($user_id);
        $user->dates()->updateExistingPivot($old_date, ['assigned' => 0]);
        $user->dates()->updateExistingPivot($new_date, ['assigned' => 1]);
    }
    */

    /*
    //admin
    //update paid status
    public function update_activity_participant_status(Request $request) {
        //dd($request);
        $user = User::find($request->user_id);
        $activity = Activity::find($request->activity_id);
        $is_checked = false;
        $is_checked = ($request->is_checked == 'true');
        if($is_checked) {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 2]);
            if($this->activity_has_enough_participants($activity->id)) {
                $activity->status = 1;
                $activity->save();
            }
        }
        else {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 1]);
            if(!$this->activity_has_enough_participants($activity->id)) {
                $activity->status = 0;
                $activity->save();
            }
        }
        return $user->activities->where('id', $request->activity_id)->first()->pivot->status;
    }
    public function activity_has_enough_participants($activity_id) {
        $activity = Activity::find($activity_id);
        if(count($activity->paid_participants) >= $activity->min_participants) {
            return true;
        }
        else {
            return false;
        }
    }
    */
    /*
    public function update_activity_visibility(Request $request) {
        //
        $activity = Activity::find($request->activity_id);
        $activity->is_visible = $request->is_visible;
        $activity->save();
        return "success";
    }
    */

}
