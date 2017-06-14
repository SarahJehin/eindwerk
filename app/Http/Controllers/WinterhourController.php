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
    //return winterhours_overview view
    public function get_winterhours_overview() {
    	$winterhour_groups = Auth::user()->winterhours;
    	foreach ($winterhour_groups as $winterhour_group) {
    		$winterhour_group->made_by_user = User::find($winterhour_group->made_by);
    		$scheme = null;
	    	//if winterhour status is 4, the scheme is generated and should be passed to the view as well
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
    	return view('winterhours/winterhours_overview', ['winterhour_groups' => $winterhour_groups]);
    }

    /**
     *
     * Download an Excel file for a certain winterhour scheme
     *
     * @param       [integer]   winterhoud id 
     * @return      [Excel]     Excel with the winterhour scheme
     *
     */
    public function download_scheme($id) {
        $winterhour = Winterhour::find($id);

        return Excel::create('Schema winteruur ' . $winterhour->title, function($excel) use ($winterhour) {
            $excel->sheet('Winteruur', function($sheet) use ($winterhour) {
                //row to start on is 1
                $new_row = 1;
                for($i = 0; $i < count($winterhour->dates); $i++) {
                    $date = date('d/m/Y', strtotime($winterhour->dates[$i]->date));
                    if($i == 0) {
                        $date_nr = 0;
                    }
                    //if the iteration is dividable by four, start a new row
                    if ($i % 4 == 0 && $i != 0) {
                        //new of scheme should be placed at previous row + amount of participants (4*courts) +2
                        $new_row = $new_row + (4 * $winterhour->amount_of_courts) + 2;
                        //date_nr back to zero -> is actually column nr
                        $date_nr = 0;
                    }
                    $col = $date_nr * 2 + 1;
                    //get col letter based on col nr
                    $col_letter = $this->num_to_alphabet($col);
                    //set the title cell (containing the date)
                    $title_cell = $col_letter . $new_row;
                    $sheet->cell($title_cell, function($cell) use($date) {
                        $cell->setValue($date);
                        $cell->setBackground('#1abc9c');
                        $cell->setFontColor('#ffffff');
                        $cell->setAlignment('center');
                    });
                    //set all participants for this date
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

    /**
     *
     * Return the alphabet letter according to its place in the alphabet
     *
     * @param       [integer]   number from 1-26 
     * @return      [string]    letter
     *
     */
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

    //return edit availabilities view
    public function edit_availabilities($id, $user_id = null) {
    	$winterhour = Winterhour::find($id);
    	$is_author = (Auth::user()->id == $winterhour->made_by);

        $min_available_days = floor(((count($winterhour->dates) * $winterhour->amount_of_courts * 4) / count($winterhour->participants)) + 2);

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
        //get all dates grouped by their month
    	$dates_by_month = $winterhour->dates->groupBy(function($item) {
		    return((new \DateTime($item->date))->format('Y-m'));
		});
    	$user_dates = $user->dates->where('winterhour_id', $winterhour->id);
    	$user_dates_array = array();
    	foreach ($user_dates as $user_date) {
    		$user_dates_array[$user_date->id] = $user_date;
    	}

    	return view('winterhours/availabilities', ['winterhour' => $winterhour, 'dates_by_month' => $dates_by_month, 'user_dates_array' => $user_dates_array, 'user' => $user, 'min_available_days' => $min_available_days]);
    }

    //update availability
    public function update_availability(Request $request) {
        $winterhour = Winterhour::find($request->winterhour_id);
        $validator = Validator::make($request->all(), []);

        $available_days = 0;
        foreach ($request->date as $date) {
            if($date == 'on') {
                $available_days++;
            }
        }
        $min_available_days = floor(((count($winterhour->dates) * $winterhour->amount_of_courts * 4) / count($winterhour->participants)) + 2);

        $validator->after(function ($validator) use ($available_days, $min_available_days) {
            if ($available_days < $min_available_days) {
                $validator->errors()->add('date', 'Je moet minstens ' . $min_available_days . ' dagen beschikbaar zijn.');
            }
        });
        if ($validator->fails()) {
            return redirect('availabilities/' . $request->winterhour_id . '/' .$request->user_id)
                        ->withErrors($validator)
                        ->withInput($request->all());
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
			if($user_has_date) {
				//if the user already has an entry with this date id in the pivot table -> update existing
				$user->dates()->updateExistingPivot($key, ['available' => $available, 'assigned' => 0]);
			}
			else {
				//if not, create a new entry for this date
				$user->dates()->attach($key, ['available' => $available, 'assigned' => 0]);
			}
    	}

      if($winterhour->status > 2) {
          //if availabilities were updated, clear scheme again
          foreach ($winterhour->dates as $date) {
              //when scheme is regenerated, first set all assigned back to 0
              foreach ($date->users as $participant) {
                  $participant->dates()->updateExistingPivot($date->id, ['assigned' => 0]);
              }
          }
      }
        
        $all_availabilities_ok = true;
        //check whether all the participants have updated their availability
        foreach ($winterhour->participants as $participant) {
            if(count($participant->where('winterhour_id', $winterhour->id)) <= 0 || !count($participant->dates)) {
                $all_availabilities_ok = false;
                break;
            }
        }
        if($all_availabilities_ok && $winterhour->status < 3) {
            $winterhour->status = 2;
            $winterhour->save();
        }
    	if($user->id != Auth::user()->id) {
            $redirect_path = 'edit_winterhour/' . $winterhour_id . '?step=3';
            $message_type = '';
    	}
    	else {
    		$redirect_path = 'availabilities/' . $winterhour_id;
            $message_type = 'success_msg';
    	}
    	return redirect($redirect_path)->with($message_type, 'Dankjewel om je beschikbaarheid te updaten!');
    }

    /**
     *
     * Swap places for participants
     *
     * @param       [request]       swap1[user_id, date_id], swap2[user_id, date_id]
     * @return      [json_response] success/failure + message
     *
     */
    public function swap_places(Request $request) {
        $user_id1 = intval($request->swap1['user_id']);
        $date_id1 = intval($request->swap1['date_id']);

        $user_id2 = intval($request->swap2['user_id']);
        $date_id2 = intval($request->swap2['date_id']);

        //check if one of the users won't be set as a duplicate participant
        $user_1_already_plays = $this->check_if_user_plays_on_date($user_id1, $date_id2);
        $user_2_already_plays = $this->check_if_user_plays_on_date($user_id2, $date_id1);

        if($user_1_already_plays || $user_2_already_plays) {
            return response()->json(['status' => 'failed', 'message' => 'Één van beide spelers speelt al op de wisseldag.']);
        }

        //check if they are both available on the other day
        $user_1_is_available = $this->check_if_user_is_available($user_id1, $date_id2);
        $user_2_is_available = $this->check_if_user_is_available($user_id2, $date_id1);

        if(!$user_1_is_available || !$user_2_is_available) {
            return response()->json(['status' => 'failed', 'message' => 'Één van beide spelers is niet beschikbaar op de wisseldag.']);
        }

        //if all checks were passed
        //switch the entries in the date_user table
        $this->switch_user($user_id1, $date_id1, $date_id2);
        $this->switch_user($user_id2, $date_id2, $date_id1);
        return response()->json(['status' => 'success', 'message' => 'Spelers werden gewisseld.']);
    }

    /**
     *
     * Check if the user already plays on the given date
     *
     * @param       [integer]   user id
     * @param       [integer]   date id 
     * @return      [boolean]   user plays on date
     *
     */
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

    /**
     *
     * Check if the user is available on the given date
     *
     * @param       [integer]   user id
     * @param       [integer]   date id 
     * @return      [boolean]   user is available
     *
     */
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

    /**
     *
     * Check if the user already plays on the given date
     *
     * @param       [integer]   user id
     * @param       [integer]   old date id
     * @param       [integer]   new date id
     *
     */
    public function switch_user($user_id, $old_date, $new_date) {
        $user = User::find($user_id);
        $user->dates()->updateExistingPivot($old_date, ['assigned' => 0]);
        $user->dates()->updateExistingPivot($new_date, ['assigned' => 1]);
    }

    //return add winterhour view
    public function add_winterhour() {
    	return view('winterhours/add_winterhour');
    }

    //create winterhour
    public function create_winterhour(Request $request) {
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
    	//default amount of courts = 1
    	$amount_of_courts = 1;
    	//for now mixed doubles is set to 0 which means no
    	$mixed_doubles = 0;
    	//winterhour statuses
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

    //return edit winterhour view
    public function edit_winterhour($id, Request $request) {
    	$winterhour = Winterhour::find($id);
    	//only the author of the winterhour is allowed to edit it
    	if(Auth::user()->id != $winterhour->made_by) {
    		abort(404);
    	}
    	$all_availabilities_ok = true;
    	//check whether all the participants have updated their availability
    	foreach ($winterhour->participants as $participant) {
    		if(count($participant->dates) <= 0 || count($participant->dates)) {
    			$all_availabilities_ok = false;
                break;
    		}
    	}
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
    	$participants = $winterhour->participants;
    	$play_times = array();
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

    	return view('winterhours/edit_winterhour', ['winterhour' => $winterhour, 'scheme' => $scheme, 'play_times' => $play_times]);
    }

    /**
     *
     * Return the scheme and the amount of play times per participant
     *
     * @param       [integer]       winterhour id
     * @return      [json_response] scheme + play times per participant
     *
     */
    public function get_scheme($id) {
        $winterhour = Winterhour::find($id);
        $scheme = null;
        //if winterhour status is 4, the scheme is generated and should be passed to the view as well
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
        $participants = $winterhour->participants;
        $play_times = array();
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

    //update winterhour
    public function update_winterhour(Request $request) {
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
    		if(!$date_exists) {
    			echo('doesnt exist');
    			//if the date does not yet exist, create it
    			$new_date = new Date([
	        		'date'	=> $date
	        	]);
        		$winterhour->dates()->save($new_date);
    		}
        }
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
    		if(!in_array($participant->id, $request->participant_id)) {
    			$winterhour->participants()->detach($participant->id);
    			//remove all date_user for this winterhour for this user, must be done this way, because a user can have multiple winterhours
    			foreach ($winterhour->dates as $date) {
	    			$date->users()->detach($participant_id);
	    		}
    		}
    	}
    	return redirect('edit_winterhour/' . $winterhour->id)->with('success_msg', 'Winteruurgroep werd geüpdatet');
    }

    //delete the given winterhour
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

    //return the dates that belong to a winterhour
    public function get_winterhour_dates(Request $request) {
    	$winterhour_id = $request->winterhour_id;
    	$winterhour = Winterhour::find($winterhour_id);
    	$winterhour_dates = $winterhour->dates;
    	return $winterhour_dates;
    }

    //return the winterhour status
    public function get_winterhour_status(Request $request) {
    	$winterhour_status = Winterhour::find($request->winterhour_id)->status;
    	return $winterhour_status;
    }

    /**
     *
     * Generate the winterhour scheme based on availabilities, max play times, ... en return success/failed message
     *
     * @param       [integer]       winterhour id
     * @param       [integer]       times : amount of times the generate winterhour function was called
     * @return      [json_response] success/failed message
     *
     */
    public function generate_scheme($id, $times = 0) {
    	$winterhour = Winterhour::find($id);

        $failed_date = 0;

        try {
            //total spots = total amounts of spots when someone can play = amount_of_courts * 4 (4 players per court) * dates;
            $total_spots = $winterhour->amount_of_courts * 4 * count($winterhour->dates);
            //the amount of times people can play = total_spots / participants -> rounded down
            $amount_of_turns = intval(floor($total_spots/count($winterhour->participants)));
            //the remaining spots, so #$rest people can play one time more than the others
            $rest = $total_spots%count($winterhour->participants);

            //create an array of user id's and amount of turns
            //participants with the most available dates will have one more turn than the others
            $ordered_participants = $this->order_participants_by_availability($winterhour->participants);
            $ordered_participants_ids = $this->get_ids_array($ordered_participants);
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
                //exclude array which will hold all of the already assigned participants
                $exclude_ids = array();
                //for each date keep an array with the assigned participants
                $date_participants[$date->id] = array();
                
                for($i = 0; $i < ($winterhour->amount_of_courts * 4); $i++) {
                    //for the amount of spots get random participant (that is not yet in the exclude ids)
                    $participant = $this->get_random_participant($ordered_participants_ids, $exclude_ids, $date->id, $participant_with_amount_of_turns, $amount_of_turns, $extra_turn, $last_date);
                    if($participant == null) {
                        $scheme_successfully_generated = false;
                        $failed_date = $date->date;
                        break 2;
                    }
                    //push id from above participant to the exclude ids list
                    array_push($exclude_ids, $participant->id);
                    array_push($date_participants[$date->id], $participant->id);
                    //add 1 to the amount of turns of the assigned participant
                    $participant_with_amount_of_turns[$participant->id]++;
                }
            }
            
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
        }
        if($scheme_successfully_generated) {
            return "success";
        }
        elseif($times < 4) {
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
            return ["failed", $failed_date];
        }
    }

    //accept the scheme so it will be visible for everybody
    public function save_scheme($id) {
    	$winterhour = Winterhour::find($id);
    	$winterhour->status = 4;
    	$winterhour->save();
    	return redirect('edit_winterhour/' . $id . '?step=4')->with('success_msg', 'Je hebt het schema geaccepteerd! Het is nu zichtbaar voor alle groepsleden.');
    }

    /**
     *
     * Return an array with the participants ordered based on who has the greatest availability
     *
     * @param       [Collection]    participants
     * @return      [array]         ordered participants
     *
     */
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
    	return $sorted_participants;
    }

    //return an array with all the id's from a collection
    public function get_ids_array($collection) {
    	$ids_array = array();
    	foreach ($collection as $item) {
    		array_push($ids_array, $item->id);
    	}
    	return $ids_array;
    }

    /**
     *
     * Generate the winterhour scheme based on availabilities, max play times, ... en return success/failed message
     *
     * @param       [array]         haystack_ids : the id's of all the participants
     * @param       [array]         exclude_ids : the id's which should be excluded (because they already are assigned)
     * @param       [integer]       date id
     * @param       [integer]       participant turns : the amount of times this player had already played in the scheme
     * @param       [integer]       amount of turns : max total amount of turns
     * @param       [array]         extra turn : id's of participants who are preferred to get an extra turn
     * @param       [boolean]       last date : true if it is the last iteration through the dates of the scheme
     * @return      [json_response] success/failed message
     *
     */
    public function get_random_participant($haystack_ids, $exclude_ids, $date_id, $participant_turns, $amount_of_turns, $extra_turn, $last_date) {
    	//array diff -> keep only haystack id's that are not in the excluded id's, array_values -> reindex array
    	$available_ids = array_values(array_diff($haystack_ids, $exclude_ids));
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
	    	$participant = User::find($available_ids[$random_nr]);
	    	//check if this user is available, otherwise get another random participant
	    	$available = $participant->dates->where('id', $date_id)->where('pivot.available', 1)->first();
    	}

    	//if it is the last date, the participant is allowed to exceed his max turns by one
    	if($last_date) {
            //
    	}
    	else {
    		//check whether this person doesn't exceed his max turns
		    if($participant_turns[$participant->id] < $amount_of_turns) {
		    }
		    else {
		    	//if the id is in the array with one more turn and is still smaller then ok, else not ok (-> not available)
		    	if(in_array($participant->id, $extra_turn) && $participant_turns[$participant->id] < ($amount_of_turns+1)) {
                    //
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
    		array_push($exclude_ids, $participant->id);
    		$available_ids = array_values(array_diff($haystack_ids, $exclude_ids));
	    	$random_nr = rand(0,(count($available_ids)-1));
            try {
                $participant = User::find($available_ids[$random_nr]);
            } catch (\Exception $e) {
                //if there are no more participants because they exceed their max amount of play times, but the current date can't be filled with participant, take the first available participant
                $winterhour = Winterhour::find(Date::find($date_id)->winterhour_id);
                $i = 0;
                foreach($winterhour->participants as $p) {
                    $p_available = $p->dates->where('id', $date_id)->where('pivot.available', 1)->where('pivot.assigned', 0)->first();
                    if($p_available) {
                        if(in_array($p->id, $already_added_ids)) {
                            //already exists, try again
                            $p_available = false;
                        }
                    }
                    
                    if($p_available) {
                        $participant = $p;
                        $check_play_times = false;
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

            if($check_play_times) {
                //check whether this person doesn't exceed his max turns
                if($participant_turns[$participant->id] < $amount_of_turns) {
                    //then everything is ok
                }
                else {
                    //if the id is in the array with one more turn and is still smaller then ok, else not ok (-> not available)
                    if(in_array($participant->id, $extra_turn) && $participant_turns[$participant->id] < ($amount_of_turns+1)) {
                        //ok
                    }
                    else {
                        //if this participant has reached his max amount of participations, set available to false and find another participant
                        $available = false;
                    }
                }
            }
	    	
            //to avoid eternal looping
    		$a++;
    		if($a > 12) {
    			break;
    		}
    	}
    	return $participant;
    }

    /**
     *
     * Return an array with the participants who have had the least turns
     *
     * @param       [array] participants with their turns
     * @return      [array] 2 participants with the least turns
     *
     */
    public function get_priority_participants($participant_turns) {
    	$priority_ids = array();
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
    	return $priority_ids;
    }

}
