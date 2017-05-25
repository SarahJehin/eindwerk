<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winterhour;
use App\Date;
use App\User;
use Auth;

class WinterhourController extends Controller
{
    //
	//all
    public function get_winterhours_overview() {
    	//$winterhour_groups = Winterhour::all();
    	$winterhour_groups = Auth::user()->winterhours;
    	foreach ($winterhour_groups as $winterhour_group) {
    		$winterhour_group->made_by_user = User::find($winterhour_group->made_by);

    		$scheme = null;
	    	//if winterhour status is 3, the scheme is generated and should be passed to the view as well
	    	if($winterhour_group->status > 3) {
	    		$scheme = array();
	    		foreach ($winterhour_group->dates as $date) {
	    			$scheme[$date->date] = array();
	    			$scheme[$date->date]['participants'] = array();
	    			$scheme[$date->date]['date_id'] = $date->id;
	    			foreach ($date->assigned_participants as $participant) {
	    				array_push($scheme[$date->date]['participants'], $participant);
	    			}
	    		}
	    	}

	    	$winterhour_group->scheme = $scheme;

    		//dump($winterhour_group);
    	}

    	//dd('test');
    	//dd($winterhour_groups);
    	return view('winterhours/winterhours_overview', ['winterhour_groups' => $winterhour_groups]);
    }

    public function edit_availabilities($id, $user_id = null) {
    	$winterhour = Winterhour::find($id);
    	$is_author = (Auth::user()->id == $winterhour->made_by);
    	//dd($is_author);

    	if($user_id) {
    		$user = User::find($user_id);
    		//if the currently authenticated user is not the one who made the winterhour or the user of whom you want to edit the availability is not a participant of this winterhour, -> abort to 404
    		//so only the author of the winterhour can update everyones availability
    		if(!$is_author || count($user->winterhours->where('id', $id)) <= 0) {
    			abort(404);
    		}
    	}
    	else {
    		$user = Auth::user();
    	}
    	//dd($winterhour->dates);
    	$dates_by_month = $winterhour->dates->groupBy(function($item) {
		    return((new \DateTime($item->date))->format('Y-m'));
		});
    	//dd($winterhour->participants);
    	//dd($winterhour->dates[0]->users);
    	//dd(Auth::user()->dates->where('winterhour_id', $winterhour->id));
    	$user_dates = $user->dates->where('winterhour_id', $winterhour->id);
    	//dd($user_dates);
    	$user_dates_array = array();
    	foreach ($user_dates as $user_date) {
    		$user_dates_array[$user_date->id] = $user_date;
    	}

    	//dd($user_dates_array);
    	//dd($test);
    	return view('winterhours/availabilities', ['winterhour' => $winterhour, 'dates_by_month' => $dates_by_month, 'user_dates_array' => $user_dates_array, 'user' => $user]);
    }

    public function update_availability(Request $request) {
    	//dd($request);
    	$winterhour_id = $request->winterhour_id;
    	$user = User::where('id', $request->user_id)->first();
    	foreach ($request->date as $key => $value) {
    		$available = 0;
    		if($value == 'on') {
    			$available = 1;
    		}
    		//echo($available);
    		//check whether the logged in user has this date in the pivot table
    		/*
    		$user_has_date = $user->whereHas('dates', function ($query) use ($key) {
			    $query->where('dates.id', $key);
			})->first();*/
			$user_has_date = $user->dates->where('id', $key)->first();
			//dump($user_has_date);
			
			if($user_has_date) {
				//if the user already has an entry with this date id in the pivot table -> update existing
				$user->dates()->updateExistingPivot($key, ['available' => $available, 'assigned' => 0]);
			}
			else {
				//if not, create a new entry for this date
				$user->dates()->attach($key, ['available' => $available, 'assigned' => 0]);
			}
    	}
    	if($user->id != Auth::user()->id) {
    		$redirect_path = 'availabilities/' . $winterhour_id . '/' . $user->id;
    	}
    	else {
    		$redirect_path = 'availabilities/' . $winterhour_id;
    	}
    	return redirect($redirect_path)->with('success_msg', 'Dankjewel om je beschikbaarheid te updaten!');
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
        return redirect('edit_winterhour/' . $winterhour->id . '?step=3');
    }

    //author
    public function edit_winterhour($id, Request $request) {
    	$winterhour = Winterhour::find($id);
    	//dd($winterhour->participants);
    	//only the author of the winterhour is allowed to edit it
    	if(Auth::user()->id != $winterhour->made_by) {
    		abort(404);
    	}
    	$all_availabilities_ok = true;
    	//check whether all the participants have updated their availability
    	foreach ($winterhour->participants as $participant) {
    		if(count($participant->dates) <= 0) {
    			$all_availabilities_ok = false;
    		}
    	}
    	//if($all_availabilities_ok && $winterhour->status != 3) {
    	if($all_availabilities_ok && $winterhour->status < 3) {
    		$winterhour->status = 2;
    		$winterhour->save();
    	}

    	$scheme = null;
    	//if winterhour status is 3, the scheme is generated and should be passed to the view as well
    	if($winterhour->status >= 3) {
    		$scheme = array();
    		foreach ($winterhour->dates as $date) {
    			$scheme[$date->date] = array();
    			$scheme[$date->date]['participants'] = array();
    			$scheme[$date->date]['date_id'] = $date->id;
    			foreach ($date->assigned_participants as $participant) {
    				array_push($scheme[$date->date]['participants'], $participant);
    			}
    		}
    	}
    	//dd($scheme);
    	$participants = $winterhour->participants;
    	$play_times = array();
    	//dd($participants);
    	foreach ($participants as $participant) {
    		$total_play_dates = $participant->dates->where('pivot.assigned', 1)->count();
    		if(isset($play_times[$total_play_dates])) {
    			array_push($play_times[$total_play_dates], $participant);
    		}
    		else {
    			$play_times[$total_play_dates] = array();
    			array_push($play_times[$total_play_dates], $participant);
    		}
    		//dump($total_play_dates);
    	}
    	//dd($play_times);

    	return view('winterhours/edit_winterhour', ['winterhour' => $winterhour, 'scheme' => $scheme, 'play_times' => $play_times]);
    }

    public function update_winterhour(Request $request) {
    	//dd($request);
    	$winterhour = Winterhour::find($request->winterhour_id);
    	$this->validate($request, [	'groupname'	=> 'required|string',
    								'day'		=> 'required|not_in:select_day',
    								'time'		=> 'required|not_in:select_hour|date_format:H:i',
    								'date'		=> 'required|array|between:6,45',
    								'date.*'	=> 'required|date',
    								'participant_id'	=> 'required|array|between:4,20'
    								]);

    	$winterhour->title 	= $request->groupname;
    	$winterhour->day 	= $request->day;
    	$winterhour->time 	= $request->time;
    	$winterhour->save();

    	//update the dates, if dates are gone remove them along with all their entries in the date_user table
    	foreach ($request->date as $date) {
    		//check if date exists in the current winterhour dates, if not create
    		$date_exists = $winterhour->dates->where('date', $date)->first();
    		//dump($date_exists);
    		if(!$date_exists) {
    			echo('doesnt exist');
    			//if the date does not yet exist, create it
    			$new_date = new Date([
	        		'date'	=> $date
	        	]);
        		$winterhour->dates()->save($new_date);
    		}
        }
        //dd('test');
        //for all the winterhour dates, check if they also exist in the request->date array, if not, delete. along with their relations in date_user
    	foreach ($winterhour->dates as $date) {
    		if(!in_array($date->date, $request->date)) {
    			//if the winterhour date isnot in the newly passed date array, remove it along with its user availabilities
    			$date->users()->detach();
    			$date->delete();
    		}
    	}
    	//add new participants, remove the ones that were removed + their entries in the date_user table (met wherenotin), keep the ones that are kept
    	foreach ($request->participant_id as $participant_id) {
    		$participant_exists = $winterhour->participants->where('id', $participant_id)->first();
    		if(!$participant_exists) {
    			//if the participant does not yet exist, attach it
    			$winterhour->participants()->attach($participant_id);
    			//update the winterhour status back to 2, because this participant does not have an availability yet:
    			$winterhour->status = 2;
    			$winterhour->save();
    		}
    	}
    	//for all the winterhour participants, check if they also exist in the request->participant_id array, if not, delete. along with their relations in date_user
    	foreach ($winterhour->participants as $participant) {
    		//echo('wh parti: ' . $participant->first_name);
    		if(!in_array($participant->id, $request->participant_id)) {
    			$winterhour->participants()->detach($participant->id);
    			//echo('not in array');
    			//remove all date_user for this winterhour for this user, must be done this way, because a user can have multiple winterhours
    			foreach ($winterhour->dates as $date) {
	    			$date->users()->detach($participant_id);
	    		}
    		}
    	}
    	//dd('stop right here');
    	return redirect('edit_winterhour/' . $winterhour->id);
    }

    public function get_winterhour_dates(Request $request) {
    	$winterhour_id = $request->winterhour_id;
    	$winterhour = Winterhour::find($winterhour_id);
    	$winterhour_dates = $winterhour->dates;
    	return $winterhour_dates;
    }

    public function get_winterhour_status(Request $request) {
    	$winterhour_status = Winterhour::find($request->winterhour_id)->status;
    	return $winterhour_status;
    }

    public function generate_scheme($id) {
    	//this function will generate a random scheme considering each participant's availability
    	$winterhour = Winterhour::find($id);

    	//total spots = total amounts of spots when someone can play = amount_of_courts * 4 (4 players per court) * dates;
    	$total_spots = $winterhour->amount_of_courts * 4 * count($winterhour->dates);

    	//the amount of times people can play = total_spots / participants -> rounded down
    	$amount_of_turns = intval(floor($total_spots/count($winterhour->participants)));
    	//the remaining spots, so #$rest people can play one time more than the others
		$rest = $total_spots%count($winterhour->participants);
		//dd($amount_of_turns);

		//create an array of user id's and amount of turns
		//participants with the most available dates will have one more turn than the others
		$ordered_participants = $this->order_participants_by_availability($winterhour->participants);
		$ordered_participants_ids = $this->get_ids_array($ordered_participants);
		//dd($ordered_participants);
		//people who get an extra turns will be saved in the extra turn array
		$extra_turn = array();
		for($i = 0; $i < $rest; $i++) {
			array_push($extra_turn, $ordered_participants[$i]->id);
		}

		//array with the participant id's and the amount of turn they already have, so they don't exceed the total amount of turns
		$participant_with_amount_of_turns = array();
		//prefill with all participants id's and 0 turns
		foreach ($ordered_participants_ids as $participant_id) {
			$participant_with_amount_of_turns[$participant_id] = 0;
		}
		//dd($participant_with_amount_of_turns);

		$participants_per_turn = $winterhour->amount_of_courts * 4;
		$date_participants = array();

		//total amount of dates
		$amount_of_dates = count($winterhour->dates);
		$date_iterator = 0;

		//foreach date get 4 participants (per court) (taking availabilties into account)
		foreach ($winterhour->dates as $date) {
			//when scheme is regenerated, first set all assigned back to 0
			foreach ($date->users as $user) {
				$user->dates()->updateExistingPivot($date->id, ['assigned' => 0]);
			}

			$date_iterator++;
			$last_date = false;
			if($date_iterator >= $amount_of_dates) {
				$last_date = true;
			}
			//dump($date);
			//exclude array which will hold all of the already assigned participants
			$exclude_ids = array();
			//echo('test');

			$date_participants[$date->id] = array();
			
			for($i = 0; $i < ($winterhour->amount_of_courts * 4); $i++) {
				echo('<h2>Nieuwe deelnemer toevoegen</h2>');
				echo('<br>Uitgesloten ids: <br>');
				dump($exclude_ids);
				//for the amount of spots get random participant (that is not yet in the exclude ids)
				$participant = $this->get_random_participant($ordered_participants_ids, $exclude_ids, $date->id, $participant_with_amount_of_turns, $amount_of_turns, $extra_turn, $last_date);
				//push id from above participant to the exclude ids list
				array_push($exclude_ids, $participant->id);
				//array_push($date_participants[$date->id], $participant->first_name . ' ' . substr($participant->last_name, 0, 1) . ' (' . $participant->id . ')');
				array_push($date_participants[$date->id], $participant->id);
				//add 1 to the amount of turns of the assigned participant
				$participant_with_amount_of_turns[$participant->id]++;
			}
			//echo('Deelnemers datum: <br>');
			//dump($date_participants);
		}
		//dd($date_participants);
		
		//when the scheme is generated update all the necessary entries in the date_user table to assigned 1
		//this could also be done in the foreach loop above, but to keep it clearer it's seperated
		foreach ($date_participants as $date_id => $participants) {
			foreach ($participants as $key => $participant_id) {
				$participant = User::find($participant_id);
				$participant->dates()->updateExistingPivot($date_id, ['assigned' => 1]);
			}
		}
		//scheme is generated so update the status
		$winterhour->status = 3;
    	$winterhour->save();
    	//dd($winterhour);
		//dd("scheme generated, redirect back to edit winterhour and display the scheme");
		return redirect('edit_winterhour/' . $winterhour->id . '?step=4');
    	/*
    	deelnemers + beschikbaarheid

		als je 10 deelnemers hebt en 22x dat je kan spelen
		= in totaal : 88 vrije plekjes

		foreach date() {
			//get 4 random mensen van de winterhour leden  (of 8 als 2 pleinen) (is dus 4 * amount of courts)
			//als ze er nog niet in staan -> voeg ze toe aan de array
			//staan ze beschikbaar voor deze dag
			//(eventueel extra controle -> als ze er vorige week instonden -> niet inzetten)
			//check of het aantal occurencies niet groter is dan de max (dus 8 in dit geval)
			//als het de laatste date is -> mag er voor 2 wel meer als 8 instaan
			//eventueel nog checken op geslacht -> dubbel gemengd maken
		)

		hoe ga je nog checken dat de mensen niet te vaak tegen dezelfde mensen moeten spelen??

		als het niet uitkomt -> opnieuw de functie runnen

		scheme = [	1 (=date_id)	=> array('Lies', 'Boris', 'Fiona', 'Landon'),
				2		=> array('Jasper', 'Inte', 'Gilles', 'Dries'),
				3		=> array('Jasper', 'Boris', 'Lies', 'Jente')];

		$aantal_keer = floor(88/10);
		$rest = 88%10;

		echo($aantal_keer);
		echo('<br>' . $rest);
		//dus dan heb je 8*9 + 2*8
		$result = ($rest * ceil(88/10)) + (10- $rest) * 8;
		echo('<br>' . $result);
		*/
    }

    public function save_scheme($id) {
    	$winterhour = Winterhour::find($id);
    	$winterhour->status = 4;
    	$winterhour->save();
    	return redirect('edit_winterhour/' . $id . '?step=4')->with('success_msg', 'Je hebt het schema geaccepteerd! Het is nu zichtbaar voor alle groepsleden.');
    }

    public function order_participants_by_availability($participants) {
    	$participants_with_amount_availabilities = array();
    	foreach ($participants as $participant) {
    		//get the amount of dates that this participant is available
    		$total_availabilities = $participant->dates->sum('pivot.available');
    		$participants_with_amount_availabilities[$participant->id] = $total_availabilities;
    	}
    	arsort($participants_with_amount_availabilities);
    	$sorted_participants = array();
    	foreach ($participants_with_amount_availabilities as $key => $value) {
    		array_push($sorted_participants, User::find($key));
    	}
    	//dd($sorted_participants);
    	return $sorted_participants;
    }

    public function get_ids_array($collection) {
    	$ids_array = array();
    	foreach ($collection as $item) {
    		array_push($ids_array, $item->id);
    	}
    	return $ids_array;
    }

    public function get_random_participant($haystack_ids, $exclude_ids, $date_id, $participant_turns, $amount_of_turns, $extra_turn, $last_date) {
    	//dd($amount_of_turns, $extra_turn, $amount_of_turns+1);
    	//array diff -> keep only haystack id's that are not in the excluded id's, array_values -> reindex array
    	$available_ids = array_values(array_diff($haystack_ids, $exclude_ids));
    	//echo('<br> Available ids <br>');
    	//dump($available_ids);

    	//first check if there are available priority id's, if not get random
    	$priority_ids = $this->get_priority_participants($participant_turns);
    	if($priority_ids) {
    		//foreach if not available
    		foreach ($priority_ids as $priority_id) {
    			$participant = User::find($priority_id);
		    	//check if this user is available
		    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
		    	//echo('<br>Available ids: <br>');
		    	//dump($available_ids);
		    	//echo('<br>is id of Jorien in available_ids, dont thin so..<br>');
		    	//dump(in_array($priority_id, $available_ids));
		    	if($available && in_array($priority_id, $available_ids)) {
		    		//if this priority participant is available, break, else continue the loop and try the next one
		    		break;
		    	}
		    	else {
		    		$available = false;
		    	}
    		}
    	}
    	else {
    		//random
    		$random_nr = rand(0,(count($available_ids)-1));
	    	//echo('<br> Random nummer: ' . $random_nr . '<br>');
	    	$participant = User::find($available_ids[$random_nr]);
	    	//check if this user is available, otherwise get another random participant
	    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
    	}

    	
    	//dd($available->pivot->available);
    	if($available) {
    		//echo('available <br>');
    	}
    	else {
    		//echo('not available <br>');
    	}

    	//if it is the last date, the participant is allowed to exceed his max turns by one
    	if($last_date) {
    		if($participant_turns[$participant->id] < ($amount_of_turns+1)) {
		    	//then everything is ok
		    	echo('<br> Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
		    }
    	}
    	else {
    		//check whether this person doesn't exceed his max turns
		    if($participant_turns[$participant->id] < $amount_of_turns) {
		    	//then everything is ok
		    	echo('<br> Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
		    }
		    else {
		    	//if the id is in the array with one more turn and is still smaller then ok, else not ok (-> not available)
		    	if(in_array($participant->id, $extra_turn) && $participant_turns[$participant->id] < ($amount_of_turns+1)) {
		    		//ok
		    		echo('<br>Extra_Turn_Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
		    	}
		    	else {
		    		//if this participant has reached his max amount of participations, set available to false and find another participant
		    		$available = false;
		    	}
		    }
    	}
    	
    	
    	//as long as the participant is not available find another one
    	//for testing stop after 5 tries
    	$a = 0;
    	while(!$available) {
    		//echo('<br> amount of turns:');
    		//dump($participant_turns);
    		array_push($exclude_ids, $participant->id);
    		$available_ids = array_values(array_diff($haystack_ids, $exclude_ids));
    		//echo('<br> Available ids <br>');
    		//dump($available_ids);
	    	$random_nr = rand(0,(count($available_ids)-1));
	    	//echo('<br> Random nummer: ' . $random_nr . '<br>');
	    	$participant = User::find($available_ids[$random_nr]);
	    	//check if this user is available, otherwise get another random participant
	    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
	    	//echo($participant->first_name);

	    	//check whether this person doesn't exceed his max turns
	    	if($participant_turns[$participant->id] < $amount_of_turns) {
	    		//then everything is ok
	    		//echo('<br> Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
	    	}
	    	else {
	    		//if the id is in the array with one more turn and is still smaller then ok, else not ok (-> not available)
	    		if(in_array($participant->id, $extra_turn) && $participant_turns[$participant->id] < ($amount_of_turns+1)) {
	    			//ok
	    			//echo('<br>Extra_Turn_Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
	    		}
	    		else {
	    			//if this participant has reached his max amount of participations, set available to false and find another participant
	    			//echo('<br> Participants had too many turns<br>');
	    			$available = false;
	    		}
	    	}
	    	//echo('a is' . $a);
	    	//extra check (didn't this person play last week) --> optional

    		$a++;
    		if($a > 12) {
    			break;
    		}
    	}
    	return $participant;
    	//dd($participant->first_name);
    }

    public function get_priority_participants($participant_turns) {
    	$priority_ids = array();
    	//asort($participant_turns, SORT_NUMERIC );
    	dump($participant_turns);
    	$lowest_value = min($participant_turns);
    	$id_lowest_value = array_keys($participant_turns, min($participant_turns))[0];
    	unset($participant_turns[$id_lowest_value]);
    	$second_lowest_value = min($participant_turns);
    	$id_second_lowest_value = array_keys($participant_turns, min($participant_turns))[0];
    	$highest_value = max($participant_turns);
    	$id_highest_value = array_keys($participant_turns, max($participant_turns))[0];
    	if($lowest_value <= ($highest_value-2)) {
    		array_push($priority_ids, $id_lowest_value);
    		if($second_lowest_value <= ($highest_value-2)) {
    			array_push($priority_ids, $id_second_lowest_value);
    		}
    	}
    	else {
    		$priority_ids = null;
    	}
    	//dd($priority_ids);
    	return $priority_ids;
    }

}
