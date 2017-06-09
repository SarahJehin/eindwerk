<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winterhour;
use App\Date;
use App\User;
use Auth;
use Excel;
use Validator;

class WinterhourController extends Controller
{
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
    	}

    	//dd($winterhour_groups);
    	return view('winterhours/winterhours_overview', ['winterhour_groups' => $winterhour_groups]);
    }

    public function download_scheme($id) {
        $winterhour = Winterhour::find($id);

        return Excel::create('Schema winteruur ' . $winterhour->title, function($excel) use ($winterhour) {
            $excel->sheet('Winteruur', function($sheet) use ($winterhour) {
                $new_row = 1;
                for($i = 0; $i < count($winterhour->dates); $i++) {
                    $date = date('d/m/Y', strtotime($winterhour->dates[$i]->date));
                    if($i == 0) {
                        $date_nr = 0;
                    }
                    if ($i % 4 == 0 && $i != 0) {
                        $new_row = $new_row + (4 * $winterhour->amount_of_courts) + 2;
                        $date_nr = 0;
                    }
                    $col = $date_nr * 2 + 1;
                    $col_letter = $this->num_to_alphabet($col);
                    $title_cell = $col_letter . $new_row;
                    $sheet->cell($title_cell, function($cell) use($date) {
                        $cell->setValue($date);
                        $cell->setBackground('#1abc9c');
                        $cell->setFontColor('#ffffff');
                        $cell->setAlignment('center');
                    });
                    
                    for($j = 0; $j < count($winterhour->dates[$i]->assigned_participants); $j++) {
                        $participant = $winterhour->dates[$i]->assigned_participants[$j]->first_name . ' ' . $winterhour->dates[$i]->assigned_participants[$j]->last_name;
                        $participant_row = $new_row + $j + 1;
                        $participant_cell = $col_letter . $participant_row;
                        $sheet->cell($participant_cell, function($cell) use($participant) {
                            $cell->setValue($participant);
                        });
                    }
                    $date_nr++;
                }
            });
        })->download('xlsx');

    }

    public function num_to_alphabet($num) {
        $num_alpha = [1     =>  'A',
                      2     =>  'B',
                      3     =>  'C',
                      4     =>  'D',
                      5     =>  'E',
                      6     =>  'F',
                      7     =>  'G',
                      8     =>  'H',
                      9     =>  'I',
                      10    =>  'J',
                      11    =>  'K',
                      12    =>  'L',
                      13    =>  'M',
                      14    =>  'N',
                      15    =>  'O',
                      16    =>  'P',
                      17    =>  'Q',
                      18    =>  'R',
                      19    =>  'S',
                      20    =>  'T',
                      21    =>  'U',
                      22    =>  'V',
                      23    =>  'W',
                      24    =>  'X',
                      25    =>  'Y',
                      26    =>  'Z'];
        return $num_alpha[$num];
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
        $winterhour = Winterhour::find($request->winterhour_id);

        $validator = Validator::make($request->all(), []);

        $available_days = 0;
        foreach ($request->date as $date) {
            if($date == 'on') {
                $available_days++;
            }
        }
        $min_available_days = ((count($winterhour->dates) * $winterhour->amount_of_courts * 4) / count($winterhour->participants)) + 2;

        $validator->after(function ($validator) use ($available_days, $min_available_days) {
            if ($available_days < $min_available_days) {
                $validator->errors()->add('date', 'Je moet minstens ' . $min_available_days . ' dagen beschikbaar zijn.');
            }
        });
        if ($validator->fails()) {
            return redirect('availabilities/' . $request->winterhour_id . '/' .$request->user_id)
                        ->withErrors($validator)
                        ->withInput();
        }


    	$winterhour_id = $request->winterhour_id;
    	$user = User::where('id', $request->user_id)->first();
    	foreach ($request->date as $key => $value) {
    		$available = 0;
    		if($value == 'on') {
    			$available = 1;
    		}
    		//check whether the logged in user has this date in the pivot table
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
    		//$redirect_path = 'availabilities/' . $winterhour_id . '/' . $user->id;
            $redirect_path = 'edit_winterhour/' . $winterhour_id . '?step=3';
            $message_type = '';
    	}
    	else {
    		$redirect_path = 'availabilities/' . $winterhour_id;
            $message_type = 'success_msg';
    	}
    	return redirect($redirect_path)->with($message_type, 'Dankjewel om je beschikbaarheid te updaten!');
    }

    //swap places for dates en participants
    public function swap_places(Request $request) {
        //return json_encode('test');
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

    public function add_winterhour() {
    	return view('winterhours/add_winterhour');
    }

    public function create_winterhour(Request $request) {
    	//dd($request);
    	//create basic winterhour + redirect to the edit winterhour view
    	$validator = Validator::make($request->all(), [	'groupname'	=> 'required|string',
    								'day'		=> 'required|not_in:select_day',
    								'time'		=> 'required|not_in:select_hour|date_format:H:i',
    								'date'		=> $request->deadline != null ? 'required|array|between:6,45' : '',
    								'date.*'	=> 'required|date',
    								'participant_id'	=> 'required|array|between:6,20'
    								]);

        if ($validator->fails()) {
            //check on which step validation failed
            if(count($validator->errors()) == 1) {
                if(array_key_exists('participant_id', $validator->errors()->toArray())) {
                    //if the validation failed on participants, redirect to step 2
                    return redirect('add_winterhour?step=2')
                        ->withErrors($validator)
                        ->withInput();
                        
                }
            }
            return redirect('add_winterhour')
                        ->withErrors($validator)
                        ->withInput();
        }
    	

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
    		$total_play_dates = $participant->dates->where('winterhour_id', $winterhour->id)->where('pivot.assigned', 1)->count();
            //dump($participant->last_name, $total_play_dates);
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
        ksort($play_times);
        //dd($scheme, $play_times);

    	return view('winterhours/edit_winterhour', ['winterhour' => $winterhour, 'scheme' => $scheme, 'play_times' => $play_times]);
    }

    public function get_scheme($id) {
        $winterhour = Winterhour::find($id);
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
            $total_play_dates = $participant->dates->where('winterhour_id', $winterhour->id)->where('pivot.assigned', 1)->count();
            if(isset($play_times[$total_play_dates])) {
                array_push($play_times[$total_play_dates], $participant);
            }
            else {
                $play_times[$total_play_dates] = array();
                array_push($play_times[$total_play_dates], $participant);
            }
        }
        ksort($play_times);
        return ['scheme' => $scheme, 'play_times' => $play_times];
    }

    public function update_winterhour(Request $request) {
    	//dd($request);
    	$winterhour = Winterhour::find($request->winterhour_id);

        $validator = Validator::make($request->all(), [ 'groupname' => 'required|string',
                                    'day'       => 'required|not_in:select_day',
                                    'time'      => 'required|not_in:select_hour|date_format:H:i',
                                    'date'      => $request->deadline != null ? 'required|array|between:6,45' : '',
                                    'date.*'    => 'required|date',
                                    'participant_id'    => 'required|array|between:6,20'
                                    ]);

        if ($validator->fails()) {
            //check on which step validation failed
            if(count($validator->errors()) == 1) {
                if(array_key_exists('participant_id', $validator->errors()->toArray())) {
                    //if the validation failed on participants, redirect to step 2
                    return redirect('edit_winterhour/' . $winterhour->id . '?step=2')
                        ->withErrors($validator)
                        ->withInput();
                        
                }
            }
            return redirect('edit_winterhour/' . $winterhour->id)
                        ->withErrors($validator)
                        ->withInput();
        }

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
    	return redirect('edit_winterhour/' . $winterhour->id)->with('success_msg', 'Winteruurgroep werd geüpdatet');
    }

    public function delete_winterhour(Request $request) {
        $winterhour = Winterhour::find($request->winterhour_id);
        //detach all the dates //with force delete to prevent an overly full db
        foreach ($winterhour->dates as $date) {
            $date->forceDelete();
        }
        //detach all the participants
        $winterhour->participants()->detach();
        $winterhour->delete();
        return redirect('winterhours_overview')->with('success_msg', 'Winteruur "' . $winterhour->title . '" werd verwijderd.');
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

    public function generate_scheme($id, $times = 0) {
    	//this function will generate a random scheme considering each participant's availability
    	$winterhour = Winterhour::find($id);
        //return json_encode($winterhour->id);

        try {
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

            $scheme_successfully_generated = true;

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
                //dump($date->date);
                //exclude array which will hold all of the already assigned participants
                $exclude_ids = array();
                //echo('test');

                $date_participants[$date->id] = array();
                
                for($i = 0; $i < ($winterhour->amount_of_courts * 4); $i++) {
                    //**echo('<h2>Nieuwe deelnemer toevoegen</h2>');
                    //**echo('<br>Uitgesloten ids: <br>');
                    //**dump($exclude_ids);
                    //for the amount of spots get random participant (that is not yet in the exclude ids)
                    $participant = $this->get_random_participant($ordered_participants_ids, $exclude_ids, $date->id, $participant_with_amount_of_turns, $amount_of_turns, $extra_turn, $last_date);
                    if($participant == null) {
                        $scheme_successfully_generated = false;
                        break 2;
                    }
                    //push id from above participant to the exclude ids list
                    array_push($exclude_ids, $participant->id);
                    //echo('<br>tell me now the new excluded ids:');
                    //dump($exclude_ids);
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
        } catch (\Exception $e) {
            //dump($e);
        }
//dd('test');
        //dump($scheme_successfully_generated);
        if($scheme_successfully_generated) {
            //return json_encode('success');
            return "success";
            dd('success');
            return redirect('edit_winterhour/' . $winterhour->id . '?step=4');
        }
        elseif($times < 4) {
            //dump('TIMES: ' . $times);
            $times++;
            return $this->generate_scheme($winterhour->id, $times);
        }
        else {
            //clean scheme and return failed
            foreach ($winterhour->dates as $date) {
                //set all assigned back to 0
                foreach ($date->users as $user) {
                    $user->dates()->updateExistingPivot($date->id, ['assigned' => 0]);
                }
            }
            $winterhour->status = 2;
            $winterhour->save();
            //return json_encode('failed');
            return "failed";
            dd('failed');
        }

        //echo("you get here?");
        //return json_encode('success');
    	
    	//dd($winterhour);
		//dd("scheme generated, redirect back to edit winterhour and display the scheme");
		//return redirect('edit_winterhour/' . $winterhour->id . '?step=4');
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
        $check_play_times = true;
        $already_added_ids = $exclude_ids;

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
	    	//echo('<br> Random nummer00: ' . $random_nr . '<br>');
	    	$participant = User::find($available_ids[$random_nr]);
	    	//check if this user is available, otherwise get another random participant
	    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
    	}

    	if($available) {
    		//echo('available <br>');
    	}
    	else {
    		//echo('not available <br>');
    	}

    	//if it is the last date, the participant is allowed to exceed his max turns by one
    	if($last_date) {
    		if($participant_turns[$participant->id] < ($amount_of_turns+2)) {
		    	//then everything is ok
		    	//**echo('<br> Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
		    }
    	}
    	else {
    		//check whether this person doesn't exceed his max turns
		    if($participant_turns[$participant->id] < $amount_of_turns) {
		    	//then everything is ok
		    	//**echo('<br> Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
		    }
		    else {
		    	//if the id is in the array with one more turn and is still smaller then ok, else not ok (-> not available)
		    	if(in_array($participant->id, $extra_turn) && $participant_turns[$participant->id] < ($amount_of_turns+1)) {
		    		//**echo('<br>Extra_Turn_Participant now has ' . $participant_turns[$participant->id] . ' turns<br>');
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
            //echo('<br>exclude');
            //dump($exclude_ids);
    		//dump($available_ids);
	    	$random_nr = rand(0,(count($available_ids)-1));
	    	//echo('<br> Random nummer: ' . $random_nr . '<br>');
            try {
                $participant = User::find($available_ids[$random_nr]);
            } catch (\Exception $e) {
                //dump($e);
                $winterhour = Winterhour::find(Date::find($date_id)->winterhour_id);
                $i = 0;
                //echo('you here matee?');
                //dump($winterhour->participants);
                foreach($winterhour->participants as $p) {
                    //echo('in participant ');
                    $p_available = $p->dates->where('id', $date_id)->where('pivot.available', 1)->where('pivot.assigned', 0)->first();
                    //dump($p_available);
                    if($p_available) {
                        //echo("user has date and is available");
                        //dump($p->id);
                        //dump($exclude_ids);
                        if(in_array($p->id, $already_added_ids)) {
                            //echo('already exists, so go on again');
                            $p_available = false;
                        }
                    }
                    
                    //echo('<br>i: ' . $i . '<br>');
                    if($p_available) {
                        $participant = $p;
                        $check_play_times = false;
                        //dump("******************************" . $check_play_times);
                        //dd($participant);
                        break;
                    }
                    if($i == (count($winterhour->participants)-1)) {
                        return null;
                    }
                    $i++;
                }
            }
	    	//check if this user is available, otherwise get another random participant
	    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
	    	//echo($participant->first_name);
            if($available) {
                //echo(' is available');
            }
            else {
                //echo(' is not available');
            }

            if($check_play_times) {
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
            }
	    	
	    	//echo('a is' . $a);
	    	//extra check (didn't this person play last week) --> optional

            //because of this it will eventually proceed without checking the availabilities
    		$a++;
    		if($a > 12) {
    			break;
    		}
    	}
        //dump('PARTICIPANT: ' . $participant);
    	return $participant;
    	//dd($participant->first_name);
    }

    public function get_priority_participants($participant_turns) {
    	$priority_ids = array();
    	//asort($participant_turns, SORT_NUMERIC );
    	//**dump($participant_turns);
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
