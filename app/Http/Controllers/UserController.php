<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use Auth;
use Excel;
use Hash;
use DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    //

    public function get_authenticated_user() {
        if (Auth::check()) {
            return Auth::user();
        }
        else {
            return null;
        }
        
    }

    public function get_members_overview(Request $request) {
        $rankings = $this->rankings_array();
        $is_admin = false;
        //$user_roles = Auth::user()->roles->pluck('level')->toArray();
        /*
        $user_has_admin_role = Auth::user()->roles->whereIn('level', [11])->first();
        if (Auth::user() && $user_has_admin_role) {
            $is_admin = true;
        }
        $is_youth_chairman = false;
        $user_has_youth_chairman_role = Auth::user()->roles->whereIn('level', [21])->first();
        if (Auth::user() && $user_has_youth_chairman_role) {
            $is_youth_chairman = true;
        }
        $is_headtrainer = false;
        $user_has_headtrainer_role = Auth::user()->roles->whereIn('level', [31])->first();
        if (Auth::user() && $user_has_headtrainer_role) {
            $is_headtrainer = true;
        }*/

        if($request->has('searching')) {
            $search_results = $this->search_members($request);
            return view('members/members_overview', ['members' => $search_results, 'rankings' => $rankings])->with(['input' => Input::all()]);
        }
        else {
            $members = User::orderBy('last_name')->orderBy('first_name')->paginate(50);
            return view('members/members_overview', ['members' => $members, 'rankings' => $rankings]);
        }
    }

    public function download_members_as_excel() {
    	$members = User::select('last_name as Achternaam', 'first_name as Voornaam', 'birth_date as Geboortedatum', 'gsm as GSM', 'ranking_singles as Enkel', 'ranking_doubles as Dubbel')->orderBy('last_name')->orderBy('first_name')->get()->toArray();
    	//dd($members);
    	//export members as Excel file
        return Excel::create('Ledenlijst_' . date('Y'), function($excel) use ($members) {
            $excel->sheet('mySheet', function($sheet) use ($members) {
                $sheet->fromArray($members);
            });
        })->download('xlsx');
    }

    public function search_members(Request $request) {
        $is_admin = false;
        $user_roles = Auth::user()->roles->pluck('level')->toArray();
        if (Auth::user() && $user_roles && min($user_roles) < 30) {
            $is_admin = true;
        }

    	$name = $request->name;
    	$from_ranking = ($request->from_ranking != 'from') ? $request->from_ranking : null;
    	$to_ranking = ($request->to_ranking != 'to') ? $request->to_ranking : null;
    	$from_birth_date = ($request->from_birth_year != 'from') ? $request->from_birth_year . '-01-01 00:00:00' : null;
    	$to_birth_date = ($request->to_birth_year != 'to') ? $request->to_birth_year . '-12-31 23:59:59' : null;
    	$search_results = User::orderBy('last_name')->orderBy('first_name');

    	if($name) {
    		$search_results = $search_results->where(function($query) use ($name) {
                                    $query->where('first_name', 'like', '%'.$name.'%')
                                            ->orWhere('last_name', 'like', '%'.$name.'%')
                                            ->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%'.$name.'%')
                                            ->orWhere(DB::raw("CONCAT(`last_name`, ' ', `first_name`)"), 'like', '%'.$name.'%');
                                });
    	}
    	if($from_ranking && $to_ranking) {
    		$from_passed = false;
    		$to_passed = false;
    		$allowed_rankings = array();
    		foreach ($this->rankings_array() as $ranking) {
    			if($ranking == $from_ranking) {
    				$from_passed = true;
    			}
    			if($from_passed && !$to_passed) {
    				array_push($allowed_rankings, $ranking);
    			}
    			if($ranking == $to_ranking) {
    				$to_passed = true;
    			}
    		}
    	}
    	elseif($from_ranking) {
    		$from_passed = false;
    		$allowed_rankings = array();
    		foreach ($this->rankings_array() as $ranking) {
    			if($ranking == $from_ranking) {
    				$from_passed = true;
    			}
    			if($from_passed) {
    				array_push($allowed_rankings, $ranking);
    			}
    		}
    	}
    	elseif($to_ranking) {
    		$to_passed = false;
    		$allowed_rankings = array();
    		foreach ($this->rankings_array() as $ranking) {
    			if(!$to_passed) {
    				array_push($allowed_rankings, $ranking);
    			}
    			if($ranking == $to_ranking) {
    				$to_passed = true;
    			}
    		}
    	}
    	if(isset($allowed_rankings)) {
    		$search_results = $search_results->where(function($query) use ($allowed_rankings) {
                                    $query->where(function($query) use ($allowed_rankings) {
	                                    $query->whereIn('ranking_singles', $allowed_rankings);
	                                })
	                                ->orWhere(function($query) use ($allowed_rankings) {
	                                    $query->whereIn('ranking_doubles', $allowed_rankings);
	                                });
                                });
    	}
    	if($from_birth_date && $to_birth_date) {
    		$search_results = $search_results->whereBetween('birth_date', [$from_birth_date, $to_birth_date]);
    	}
    	elseif($from_birth_date) {
    		$search_results = $search_results->where('birth_date', '>', $from_birth_date);
    	}
    	elseif($to_birth_date) {
    		$search_results = $search_results->where('birth_date', '<', $to_birth_date);
    	}

    	$search_results = $search_results->paginate(50)->appends(['name'                => $request->name,
                                                                  'from_ranking'        => $request->from_ranking,
                                                                  'to_ranking'          => $request->to_ranking,
                                                                  'from_birth_year'     => $request->from_birth_year,
                                                                  'to_birth_year'       => $request->to_birth_year,
                                                                  'searching'           => $request->searching]);

    	//dd($search_results);
        //dd($search_results);
        return $search_results;
    	//return view('members/members_overview', ['members' => $search_results, 'rankings' => $rankings, 'is_admin' => $is_admin])->with(['input' => Input::all()]);
    }

    public function get_allowed_update_roles(Request $request) {
        $is_admin = Auth::user()->roles->whereIn('level', 11)->first();
        $is_youth_chairman = Auth::user()->roles->whereIn('level', [21])->first();
        $is_headtrainer = Auth::user()->roles->whereIn('level', [31])->first();
        //dd($is_admin);
        $allowed_update_roles = null;

        //if authenticated user is admin (main chairman), show all user roles
        if($is_admin) {
            $allowed_update_roles = Role::all();
        }
        elseif($is_youth_chairman || $is_headtrainer) {
            $allowed_update_roles = Role::orderBy('level');
            if($is_youth_chairman) {
                $allowed_update_roles = $allowed_update_roles->orWhereIn('level', [20, 21, 22, 23, 24, 25]);
            }
            if($is_headtrainer) {
                $allowed_update_roles = $allowed_update_roles->orWhereIn('level', [30, 31, 32, 33, 34, 35, 36]);
            }
            $allowed_update_roles = $allowed_update_roles->get();
        }
        //dd($allowed_update_roles);
        return $allowed_update_roles;
        
    }

    public function get_user_roles(Request $request) {
        //return $request->member_id;
        $member_roles = User::find($request->member_id)->roles->pluck('id');
        //dd($member_roles);
        return $member_roles;
    }

    public function update_user_role(Request $request) {
        //return (string)$request->new_value;
        $user = User::find($request->member_id);
        //return $user;
        if((string)$request->new_value == '1') {
            $user->roles()->attach($request->role_id);
            return $user->first_name . ' ' . $user->last_name . ' now has new role (' . $request->role_id . ')';
        }
        else {
            $user->roles()->detach($request->role_id);
            return $user->first_name . ' ' . $user->last_name . ' no longer has role role (' . $request->role_id . ')';
        }
    }

    public function update_profile_pic(Request $request) {
    	$base64_encoded_image = $request->imagebase64;
    	//$base64_encoded_image = $request->testbase64;
    	//$base64_encoded_image = $request->last_test_base64;
    	//get the base64 code
    	$data = explode(';', $base64_encoded_image)[1];
        $data = explode(',', $data)[1];
        $profile_pictures_path = public_path() . '/images/profile_pictures/';
        $image_name = time() . strtolower(Auth::user()->first_name . '_' . Auth::user()->last_name) . '.png';

        //decode the data
        $data = base64_decode($data);
        //save the data
        $total_path = $profile_pictures_path . $image_name;
        file_put_contents($total_path, $data);

        //update the user model
        Auth::user()->image = $image_name;
        Auth::user()->save();

        return redirect()->back();

        dd($profile_pictures_path . $image_name);
        dd($data);
    }

    //admin
    public function import_members(Request $request) {

        //init an empty array which will hold all of the import errors
        /*
        $errors = array('first_name'    => [],
                        'last_name'     => [],
                        'gsm_nr'        => [],
                        'tel_nr'        => [],
                        'birth_date'    => [],
                        'gender'        => [],
                        'ranking_singles'   => [],
                        'ranking_doubles'   => []);
        */
        $error_messages = array();
        $problem_users  = array();
        $warning_users  = array();

    	//check if post request contains file, if yes, read out the excel file, with all the members
    	if ($request->hasFile('members_excel')) {
		    //
		    $path = $request->file('members_excel')->getRealPath();
			$data = Excel::load($path, function($reader) {
			})->get();
			if(!empty($data) && $data->count()){
				$sheet1 = $data[0];

                $e_year = $this->get_singles_property($sheet1[0]);
                //some default fields that are not coming from the Excel
                $email      = null;
				$image = 'default.jpg';
				$password	= Hash::make('sportiva');
				foreach ($sheet1 as $key => $member) {
                    //array to hold only errors for this user
                    $user_errors = false;
                    $errs = array();

                    if($member->vtv) {
                        $vtv_nr     = $member->vtv;
                    }
					else {
                        //generate random nr
                        $vtv_nr = $this->get_unique_vtv_nr();
                    }
					$first_name = ucfirst(mb_strtolower($member->voornaam));
					$last_name 	= $this->get_clean_last_name($member->naam);
					if($member->gsmnr) {
						$gsm_nr 	= '0' . str_replace(' ', '', (string)$member->gsmnr);
					}
					else {
						$gsm_nr = null;
					}
					if($member->telefoonnr) {
						$tel_nr 	= '0' . str_replace(' ', '', (string)$member->telefoonnr);
					}
					else {
						$tel_nr = null;
					}
                    if(strtotime($member->datum)) {
                        //echo('valid date <br>');
                        $birth_date = date('Y-m-d', strtotime($member->datum));
                    }
                    elseif(!$member->datum) {
                        //echo('no date: ' . $last_name);
                        $birth_date = null;
                        array_push($warning_users, $last_name . ' ' .$first_name);
                    }
                    else {

                        //echo('invalid birth date <br>');
                        $birth_date = $this->format_date($member->datum);
                        //if the date still is not valid, add to errors
                        if(!$this->validate_date($birth_date)) {
                            $err_msg = 'Incorrecte datums: zorg ervoor dat alle datums in de Excel file in datumformaat staan.';
                            array_push($errs, $err_msg);
                            $user_errors = true;
                            if((!in_array($err_msg, $error_messages))) {
                                array_push($error_messages, $err_msg);
                            }
                        }
                    }
                    //echo($last_name);
					$gender		= strtoupper($member->mv);
                    if($gender != 'M' && $gender != 'V') {
                        $gender = null;
                        if(!in_array($last_name . ' ' .$first_name, $warning_users)) {
                            array_push($warning_users, $last_name . ' ' .$first_name);
                        }
                        /*
                        $err_msg = 'Geslacht moet ofwel M of V zijn.';
                        array_push($errs, $err_msg);
                        $user_errors = true;
                        if((!in_array($err_msg, $error_messages))) {
                            array_push($error_messages, $err_msg);
                        }
                        */
                    }
                    
					if(strtoupper($member->{$e_year}) == 'NG') {
						$ranking_singles = 'NG (5)';
					}
					else {
                        if(is_numeric($member->e)) {
                            $ranking_singles = $this->number_to_ranking($member->e);
                        }
						else {
                            $ranking_singles = null;
                            /*
                            $err_msg = 'De enkelklassement-kolom (E) moet een cijfer bevatten (5, 10, 15, ..., 115).';
                            $user_errors = true;
                            array_push($errs, $err_msg);
                            if((!in_array($err_msg, $error_messages))) {
                                array_push($error_messages, $err_msg);
                            }
                            */
                        }
					}
                    if(is_numeric($member->d)) {
                        $ranking_doubles = $this->number_to_ranking($member->d);
                    }
                    else {
                        $ranking_doubles = null;
                        /*
                        $err_msg = 'De dubbelklassement-kolom (D) moet een cijfer bevatten (5, 10, 15, ..., 115).';
                        $user_errors = true;
                        array_push($errs, $err_msg);
                        if((!in_array($err_msg, $error_messages))) {
                            array_push($error_messages, $err_msg);
                        }
                        */
                    }
					$level		= $this->get_level_by_birth_date($birth_date);
                    //echo($last_name . ' ' . $first_name);
					//echo($member->naam . ' ' . $member->voornaam . ' (' .$vtv_nr . '):  ' . $ranking_singles . $ranking_doubles . '<br>');
					//check if a user with this vtv nr already exists, if not instantiate a new one
					//actually better updateOrCreate //but maybe not because level should not be overrided, nor should password
					$user = User::firstOrNew(
					    ['vtv_nr' => $vtv_nr], 
					    ['email' 		=> $email,
					     'first_name'	=> $first_name,
					     'last_name'	=> $last_name,
					     'gsm'			=> $gsm_nr,
					     'tel'			=> $tel_nr,
					     'birth_date'	=> $birth_date,
					     'gender'		=> strtoupper($member->mv),
					     'ranking_singles'	=> $ranking_singles,
					     'ranking_doubles'	=> $ranking_doubles,
					     'image'		=> $image,
					     'level_id'		=> $level,
					     'password'		=> $password]
					);

					if($user->exists) {
						//update the current user
						//echo('user, already exists, only some attributes will be updated <br>');
                        $user->first_name       = $first_name;
                        $user->last_name        = $last_name;
						$user->ranking_singles 	= $ranking_singles;
						$user->ranking_doubles 	= $ranking_doubles;
						$user->gsm 				= $gsm_nr;
						$user->tel 				= $tel_nr;
                        $user->birth_date       = $birth_date;
                        $user->gender           = strtoupper($member->mv);
                        $user->updated_at       = date('Y-m-d H:i:s');
					}
                    else {
                        //echo('user does not exist, create from scratch <br>');
                    }
                    //before the user is saved, check if there are no errors for this user
                    if(!$user_errors) {
                        $user->save();
                    }
                    else {
                        array_push($problem_users, $user->last_name . ' ' . $user->first_name);
                    }
                    
					//dump($user);
				}
                //all where updated at is more than a minute ago
                $users_to_delete = User::select('id', 'updated_at', 'last_name')->where('updated_at', '<', (date('Y-m-d H:') . (date('i')-1) . ':00'))->get();
                //dump($users_to_delete);
                foreach ($users_to_delete as $user) {
                    //dump($user);
                    //echo('stap 1: ' . $user->first_name . ' ' . $user->last_name . ' (' . $user->id . ')');
                    //beneath is temporary :)
                    if($user->id != 2 && $user->id != 6 && $user->id != 7) {
                        //echo('stap2 : ');
                        //echo($user->first_name . ' ' . $user->last_name);
                        //this users may be hard deleted, cause most of them just will be doubles
                        //$user->delete();
                        $user->forceDelete();
                    }
                }
			}
		}
        //dd($warning_users);
        //dd('stop right here!');
		//dd('test');
        //dd($error_messages);
        if (!empty($error_messages)) {
            //dd('redirect back with errors', $error_messages);
            //return back with errors
            return redirect()->back()->with('error_messages', $error_messages)->with('problem_users', $problem_users);
        }
        else {
            //dd("nope!");
            //return back with success message
            return redirect()->back()->with('success_msg', 'Alle leden werden succesvol geïmporteerd!')->with('warning_users', $warning_users);
        }

    	//dd($request);
    }

    public function get_unique_vtv_nr() {
        $unique_nr = mt_rand(100000, 999999);
        $user_exists = User::where('vtv_nr', $unique_nr)->first();
        if($user_exists) {
            return $this->get_unique_vtv_nr();
        }
        else {
            return $unique_nr;
        }
    }


    public function get_clean_last_name($member_last_name) {
        $last_name = mb_strtolower($member_last_name);
        $last_name_pieces = explode(' ', $last_name);
        $last_name = array();
        foreach($last_name_pieces as $piece) {
            array_push($last_name, ucfirst($piece));
        }
        $last_name = implode(' ', $last_name);
        return $last_name;
    }

    //check if the date passed is a valid date
    function validate_date($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    //function to convert an invalid date to a valid one
    //only works for the folowwing invalid format: dd/mm/yy
    public function format_date($date) {
        //split date by delimter
        $new_date   = explode('/', $date);
        //reverse the order from dd,mm,yy to yy,mm,dd
        $new_date   = array_reverse($new_date);
        //join together again with - as delimiter
        $new_date   = implode('-', $new_date);
        return $new_date;
    }

    public function get_singles_property($member) {
        //check for previous, current and coming year
        $previous_year  = 'e_' . (date('Y')-1);
        $current_year   = 'e_' . date('Y');
        $next_year      = 'e_' . (date('Y')+1);
        if($member->{$previous_year}) {
            $e_year = $previous_year;
        }
        elseif($member->{$current_year}) {
            $e_year = $current_year;
        }
        elseif($member->{$next_year}) {
            $e_year = $next_year;
        }
        echo($e_year);
        return $e_year;
    }

    public function number_to_ranking($number) {
    	//array with all rankings as number and offcial name (//if 5 points two possibilities: N.G. / C+30/5)
    	$rankings = ['5'	=> 'C+30/5',
    				 '10'	=> 'C+30/4',
    				 '15'	=> 'C+30/3',
    				 '20'	=> 'C+30/2',
    				 '25'	=> 'C+30/1',
    				 '30'	=> 'C+30',
    				 '35'	=> 'C+15/5',
    				 '40'	=> 'C+15/4',
    				 '45'	=> 'C+15/3',
    				 '50'	=> 'C+15/2',
    				 '55'	=> 'C+15/1',
    				 '60'	=> 'C+15',
    				 '65'	=> 'B+4/6',
    				 '70'	=> 'B+2/6',
    				 '75'	=> 'B0',
    				 '80'	=> 'B-2/6',
    				 '85'	=> 'B-4/6',
    				 '90'	=> 'B-15',
    				 '95'	=> 'B-15/1',
    				 '100'	=> 'B-15/2',
    				 '105'	=> 'B-15/4',
    				 '110'	=> 'A nationaal',
    				 '115'	=> 'A internationaal'];
    	if($number) {
    		$ranking = $rankings[$number] . ' (' . $number . ')';
    	}
    	else {
    		$ranking = null;
    	}
    	
    	return $ranking;
    }

    public function get_level_by_birth_date($birth_date) {
    	if($birth_date) {
    		//get age
	    	$age = intval(date('Y')-date('Y', strtotime($birth_date)));
	    	if($age >= 14) {
	    		$level = 6;
	    	}
	    	else {
	    		$level = null;
	    	}
    	}
    	else {
    		$level = null;
    	}
    	
    	return $level;
    }

    public function rankings_array() {
    	return ['C+30/5 (5)',
    			'C+30/4 (10)',
    			'C+30/3 (15)',
    			'C+30/2 (20)',
    			'C+30/1 (25)',
    			'C+30 (30)',
    			'C+15/5 (35)',
    			'C+15/4 (40)',
    			'C+15/3 (45)',
    			'C+15/2 (50)',
    			'C+15/1 (55)',
    			'C+15 (60)',
    			'B+4/6 (65)',
    			'B+2/6 (70)',
    			'B0 (75)',
    			'B-2/6 (80)',
    			'B-4/6 (85)',
    			'B-15 (90)',
    			'B-15/1 (95)',
    			'B-15/2 (100)',
    			'B-15/4 (105)',
    			'A nationaal (110)',
    			'A internationaal (115)'];
    }

    
}
