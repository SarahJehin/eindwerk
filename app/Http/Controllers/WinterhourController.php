<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winterhour;
use App\Date;
use Auth;

class WinterhourController extends Controller
{
    //
	//all
    public function get_winterhours_overview() {
    	//$winterhour_groups = Winterhour::all();
    	$winterhour_groups = Auth::user()->winterhours;
    	//dd($winterhour_groups);
    	return view('winterhours/winterhours_overview', ['winterhour_groups' => $winterhour_groups]);
    }

    public function edit_availabilities($id) {
    	$winterhour = Winterhour::find($id);
    	$dates_by_month = $winterhour->dates->groupBy(function($item) {
		    return((new \DateTime($item->date))->format('Y-m'));
		})->reverse();
    	//dd($winterhour->participants);
    	//dd($winterhour->dates[0]->users);
    	//dd(Auth::user()->dates->where('winterhour_id', $winterhour->id));
    	$user_dates = Auth::user()->dates->where('winterhour_id', $winterhour->id);
    	//dd($user_dates);
    	$user_dates_array = array();
    	foreach ($user_dates as $user_date) {
    		$user_dates_array[$user_date->id] = $user_date;
    	}
    	//dd($user_dates_array);
    	//dd($test);
    	return view('winterhours/availabilities', ['winterhour' => $winterhour, 'dates_by_month' => $dates_by_month, 'user_dates_array' => $user_dates_array]);
    }

    public function add_winterhour() {
    	return view('winterhours/add_winterhour');
    }

    public function create_winterhour(Request $request) {
    	//dd($request);
    	//create basic winterhour + redirect to the edit winterhour view
    	$this->validate($request, [	'groupname'	=> 'required|string',
    								'day'		=> 'required|not_in:select_day',
    								'time'		=> 'required|not_in:select_hour|date_format:H:i',
    								'date'		=> 'required|array|between:6,45',
    								'date.*'	=> 'required|date',
    								'participant_id'	=> 'required|array|between:4,20'
    								]);
    	

    	$made_by = Auth::user()->id;
    	$status	 = 1;
    	//for now the default is 1 court
    	$amount_of_courts = 1;
    	//for now mixed doubles is set to 0 which means no
    	$mixed_doubles = 0;
    	//statuses
    		//0 = inactive
    		//1 = created, waiting for availabilties
    		//2 = availabilities ok, waiting for scheme
    		//3 = scheme generated

    	$winterhour = new Winterhour([
            'title'         => $request->groupname,
            'day'   		=> $request->day,
            'time'     		=> $request->time,
            'made_by'		=> $made_by,
            'status'		=> $status,
            'amount_of_courts'	=> $amount_of_courts,
            'mixed_doubles'		=> $mixed_doubles
        ]);
    	//dd($winterhour);
        $winterhour->save();

        foreach ($request->date as $date) {
        	$new_date = new Date([
        		'date'	=> $date
        	]);
        	$winterhour->dates()->save($new_date);
        }

        foreach ($request->participant_id as $participant_id) {
        	$winterhour->participants()->attach($participant_id);
        }

        //when everything is created redirect to edit
        return redirect('edit_winterhour/' . $winterhour->id);
    }

    //author
    public function edit_winterhour($id) {
    	$winterhour = Winterhour::find($id);
    	//dd($winterhour->participants);
    	return view('winterhours/edit_winterhour', ['winterhour' => $winterhour]);
    }

    public function generate_scheme() {
    	//this function will generate a random scheme considering each participant's availability
    }
}
