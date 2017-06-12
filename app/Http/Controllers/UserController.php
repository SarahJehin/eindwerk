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
    public function get_authenticated_user() {
        if (Auth::check()) {
            return Auth::user();
        }
        else {
            return null;
        }
    }

    //return members_overview view
    public function get_members_overview(Request $request) {
        $rankings = $this->rankings_array();
        if($request->has('searching')) {
            $search_results = $this->search_members($request);
            return view('members/members_overview', ['members' => $search_results, 'rankings' => $rankings])->with(['input' => Input::all()]);
        }
        else {
            $members = User::orderBy('last_name')->orderBy('first_name')->paginate(50);
            return view('members/members_overview', ['members' => $members, 'rankings' => $rankings]);
        }
    }

    /**
     *
     * Return all the info from a certain member
     *
     * @param       [integer]   id of the member you're looking for
     * @return      [User]      the User object
     *
     */
    public function get_member_details($id) {
        $member = User::find($id);
        return $member;
    }

    /**
     *
     * Return first 5 members that match the searchstring
     *
     * @param       [request->string]       first and/or last name of the member you're searching for
     * @param       [request->array]        ids of members who should be ignored while searching
     * @return      [Collection]            members
     *
     */
    public function get_matching_users(Request $request) {
        $searchstring   = $request->searchstring;
        $not_ids        = $request->not_ids;

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

    //return an Excel file with a list of all members and their info
    public function download_members_as_excel() {
    	$members = User::select('last_name as Naam', 'first_name as Voornaam', 'vtv_nr as VTV', 'birth_date as Geboortedatum', 'gender as M/V', 'member_since as Lid sinds', 'gsm as GSMnr', 'tel as Telefoonnr', 'ranking_singles as E ' . date('Y'), 'ranking_singles as E', 'ranking_doubles as D')->orderBy('last_name')->orderBy('first_name')->get()->toArray();

        foreach ($members as $key => $member) {
            $member['E'] = substr($member['E'], strpos($member['E'], "(") + 1);
            $members[$key]['E'] = rtrim($member['E'], ')');
            $member['D'] = substr($member['D'], strpos($member['D'], "(") + 1);
            $members[$key]['D'] = rtrim($member['D'], ')');
            $members[$key]['Geboortedatum'] = date('d/m/Y', strtotime($member['Geboortedatum']));
            $members[$key]['Lid sinds'] = date('Y', strtotime($member['Lid sinds']));

            $members[$key]['GSMnr'] = substr($member['GSMnr'], 0, 4) . ' ' . chunk_split(substr($member['GSMnr'], 4), 2, ' ');
            $members[$key]['Telefoonnr'] = substr($member['Telefoonnr'], 0, 3) . ' ' . chunk_split(substr($member['Telefoonnr'], 3), 2, ' ');
        }
    	//export members as Excel file
        return Excel::create('Ledenlijst_' . date('Y'), function($excel) use ($members) {
            $excel->sheet('mySheet', function($sheet) use ($members) {
                $sheet->fromArray($members);
                $sheet->cells('A1:L1', function($cells) {
                         $cells->setBackground('#dddddd'); 
                });
            });
        })->download('xlsx');
    }

    /**
     *
     * Return all members who match the searchdata
     *
     * @param       [request->string]   containing all searchdata (name, ranking, birth_year)
     * @return      [Collection]        members
     *
     */
    public function search_members(Request $request) {

    	$name = $request->name;
    	$from_ranking = ($request->from_ranking != 'from') ? $request->from_ranking : null;
    	$to_ranking = ($request->to_ranking != 'to') ? $request->to_ranking : null;
    	$from_birth_date = ($request->from_birth_year != 'from') ? $request->from_birth_year . '-01-01 00:00:00' : null;
    	$to_birth_date = ($request->to_birth_year != 'to') ? $request->to_birth_year . '-12-31 23:59:59' : null;
    	$search_results = User::orderBy('last_name')->orderBy('first_name');

        //search by name: first name, last name, first name + last name, last name + first name
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

        return $search_results;
    }

    /**
     *
     * Return all the roles the authenticated user is allowed to update
     *
     * @return      [Collection]    roles
     *
     */
    public function get_allowed_update_roles() {
        $is_admin = Auth::user()->roles->whereIn('level', 11)->first();
        $is_youth_chairman = Auth::user()->roles->whereIn('level', [21])->first();
        $is_headtrainer = Auth::user()->roles->whereIn('level', [31])->first();
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
        return $allowed_update_roles;
        
    }

    /**
     *
     * Return all role id's for certain member
     *
     * @param       [request->integer]  member id
     * @return      [array]             roles ids
     *
     */
    public function get_user_roles(Request $request) {
        $member_roles = User::find($request->member_id)->roles->pluck('id');
        return $member_roles;
    }

    /**
     *
     * Return all role id's for certain member
     *
     * @param       [request->integer]  member id
     * @return      [string]            message
     *
     */
    public function update_user_role(Request $request) {
        $user = User::find($request->member_id);
        if((string)$request->new_value == '1') {
            $user->roles()->attach($request->role_id);
            return $user->first_name . ' ' . $user->last_name . ' now has new role (' . $request->role_id . ')';
        }
        else {
            $user->roles()->detach($request->role_id);
            return $user->first_name . ' ' . $user->last_name . ' no longer has role role (' . $request->role_id . ')';
        }
    }

    //update profile contactinfo (like mobile, phone and email)
    public function update_profile(Request $request) {
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
                $user->tel = $new_value;
                break;
            case 'email':
                $user->email = $new_value;
                break;
        }

        $user->save();
        return 'success';
    }

    //update the user profile picture
    public function update_profile_pic(Request $request) {
    	$base64_encoded_image = $request->imagebase64;
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
    }

    //update the user password
    public function update_pwd(Request $request) {
        
        $return_with_errors = false;
        $errors = [];
        //first check if old pwd was correct
        if(!(Hash::check($request->old_pwd, Auth::user()->password))) {
            $return_with_errors = true;
            $errors['old_pwd'] = "Oude wachtwoord is incorrect.";
        }
        //check if new password is valid
        if(strlen($request->new_pwd) < 6) {
            $return_with_errors = true;
            $errors['new_pwd_length'] = "Het nieuwe wachtwoord moet minstens uit 6 karakters bestaan";
        }
        if($request->new_pwd != $request->new_pwd_check) {
            $return_with_errors = true;
            $errors['new_pwd'] = "Het nieuwe wachtwoord moet tweemaal hetzelfde ingegeven worden.";
        }
        if($return_with_errors) {
            return redirect('/')->with('error_msg', $errors);
        }
        else {
            Auth::user()->password = Hash::make($request->new_pwd);
            Auth::user()->tmp_password = null;
            Auth::user()->save();
            return redirect('/')->with('success_msg', 'Je hebt je wachtwoord succesvol geüpdatet.');
        }
    }

    //return an Excel file with header and example row for member import Excel
    public function download_members_example() {
        return Excel::create('Ledenlijst_voorbeeld', function($excel) {
            $excel->sheet('Voorbeeld', function($sheet) {
                $sheet->row(1, array(
                     'Naam', 'Voornaam', 'VTV', 'Geboortedatum', 'M/V', 'Lid sinds', 'GSMnr', 'Telefoonnr', 'E ' . date('Y'), 'E', 'D'
                ));
                $sheet->row(1, function($row) {
                    $row->setBackground('#dddddd');
                });
                $sheet->row(2, array(
                     'Janssens', 'Jan', '0568497', '28/07/1958', 'M', '2003', '0477 15 48 59', '016 22 54 58', 'C+30/4', '10', '10'
                ));

            });
        })->download('xlsx');
    }

    /**
     *
     * Import all members from Excel and create or update their info
     *
     * @param       [request->file]     Excel file containing all members
     * @return      [view]              members overview with message
     *
     */
    public function import_members(Request $request) {
        $error_messages = array();
        //problem users = users who could not be imported and crashed the script
        $problem_users  = array();
        //warning users = users from whom some non-required data is missing
        $warning_users  = array();

    	//check if post request contains file, if yes, read out the excel file, with all the members
    	if ($request->hasFile('members_excel')) {
		    $path = $request->file('members_excel')->getRealPath();
			$data = Excel::load($path, function($reader) {
			})->get();
			if(!empty($data) && $data->count()){
				$sheet1 = $data[0];
                try {
                    $e_year = $this->get_singles_property($sheet1[0]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error_msg', 'Dit is geen geldige ledenlijst. Download het voorbeeld voor het goede formaat.');
                }
                //some default fields that won't be imported from the Excel
                $email      = null;
				$image      = 'male_avatar.png';
                $male_avatar = 'male_avatar.png';
                $female_avatar = 'female_avatar.png';
                //create a password array with some random passwords for new users
                $password_array = [];
                for($i = 0; $i < 20; $i++) {
                    $random_pwd = $this->get_random_password();
                    array_push($password_array, [$random_pwd, Hash::make($random_pwd)]);
                }

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
                    if($member->lid_sinds) {
                        $member_since = (int)$member->lid_sinds;
                    }
                    else {
                        $member_since = null;
                    }
					$first_name = $this->get_clean_first_name($member->voornaam);
					$last_name 	= $this->get_clean_last_name($member->naam);
					if($member->gsmnr) {
                        $gsm_nr     = str_replace('.', '', str_replace(' ', '', (string)$member->gsmnr));
                        $gsm_nr     = (int)$gsm_nr;
						$gsm_nr 	= '0' . str_replace('.', '', str_replace(' ', '', (string)$gsm_nr));
					}
					else {
						$gsm_nr = null;
					}
					if($member->telefoonnr) {
                        $tel_nr     = str_replace('.', '', str_replace(' ', '', (string)$member->telefoonnr));
                        $tel_nr     = (int)$tel_nr;
                        $tel_nr     = '0' . str_replace('.', '', str_replace(' ', '', (string)$tel_nr));
					}
					else {
						$tel_nr = null;
					}
                    if(strtotime($member->geboortedatum)) {
                        $birth_date = date('Y-m-d', strtotime($member->geboortedatum));
                    }
                    elseif(!$member->geboortedatum) {
                        $birth_date = null;
                        array_push($warning_users, $last_name . ' ' .$first_name);
                    }
                    else {
                        $birth_date = $this->format_date($member->geboortedatum);
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
					$gender		= strtoupper($member->mv);
                    if($gender != 'M' && $gender != 'V') {
                        $gender = null;
                        if(!in_array($last_name . ' ' .$first_name, $warning_users)) {
                            array_push($warning_users, $last_name . ' ' .$first_name);
                        }
                    }
                    elseif($gender == 'M') {
                        $image = $male_avatar;
                    }
                    elseif($gender == 'V') {
                        $image = $female_avatar;
                    }
                    
					if(strpos(strtoupper($member->{$e_year}), 'NG')) {
						$ranking_singles = 'NG (5)';
					}
					else {
                        if(is_numeric($member->e)) {
                            $ranking_singles = $this->number_to_ranking($member->e);
                        }
						else {
                            $ranking_singles = null;
                        }
					}
                    if(is_numeric($member->d)) {
                        $ranking_doubles = $this->number_to_ranking($member->d);
                    }
                    else {
                        $ranking_doubles = null;
                    }
					$level		= $this->get_level_by_birth_date($birth_date);

                    //get a random password from the passwords array
                    $random_pwd_nr = rand(0, 19);
                    $temp_password = $password_array[$random_pwd_nr][0];
                    $password = $password_array[$random_pwd_nr][1];

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
                         'tmp_password' => $temp_password,
					     'password'		=> $password]
					);

					if($user->exists) {
						//update the current user
                        $user->first_name       = $first_name;
                        $user->last_name        = $last_name;
                        if($ranking_singles != null) {
                            $user->ranking_singles  = $ranking_singles;
                        }
                        if($ranking_doubles != null) {
                            $user->ranking_doubles  = $ranking_doubles;
                        }
                        if($gsm_nr != null) {
                            $user->gsm              = $gsm_nr;
                        }
                        if($tel_nr != null) {
                            $user->tel              = $tel_nr;
                        }
                        if($birth_date != null) {
                            $user->birth_date       = $birth_date;
                        }
                        if($member->mv != null) {
                            $user->gender           = strtoupper($member->mv);
                        }
                        $user->updated_at       = date('Y-m-d H:i:s');
					}
                    //before the user is saved, check if there are no errors for this user
                    if(!$user_errors) {
                        $user->save();
                    }
                    else {
                        array_push($problem_users, $user->last_name . ' ' . $user->first_name);
                    }
				}
                //all users where updated at is more than a minute ago -> they should be deleted because they are no longer in the Excel
                $users_to_delete = User::select('id', 'updated_at', 'last_name')->where('updated_at', '<', (date('Y-m-d H:') . (date('i')-1) . ':00'))->get();
                foreach ($users_to_delete as $user) {
                    $user->forceDelete();
                }
			}
		}
        if (!empty($error_messages)) {
            //return back with errors
            return redirect()->back()->with('error_messages', $error_messages)->with('problem_users', $problem_users);
        }
        else {
            //return back with success message
            return redirect()->back()->with('success_msg', 'Alle leden werden succesvol geïmporteerd!')->with('warning_users', $warning_users);
        }
    }

    /**
     *
     * Return an unique VTV nr for members who don't have one
     *
     * @return      [integer]   vtv_nr
     *
     */
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

    /**
     *
     * Clean up a first name: first letter to uppercase, when containing spaces or dashes, second name starts with uppercase
     *
     * @param       [string]    first name 
     * @return      [string]    clean first name
     *
     */
    public function get_clean_first_name($member_first_name) {
        $first_name = mb_strtolower($member_first_name);
        $first_name_pieces = explode(' ', $first_name);
        $first_name = array();
        foreach($first_name_pieces as $piece) {
            array_push($first_name, ucfirst($piece));
        }
        $first_name = implode(' ', $first_name);
        $first_name_pieces = explode('-', $first_name);
        $first_name = array();
        foreach($first_name_pieces as $piece) {
            array_push($first_name, ucfirst($piece));
        }
        $first_name = implode('-', $first_name);
        return $first_name;
    }

    /**
     *
     * Clean up a last name: first letter to uppercase, when containing spaces or dashes, second name starts with uppercase
     *
     * @param       [string]    last name 
     * @return      [string]    clean last name
     *
     */
    public function get_clean_last_name($member_last_name) {
        $last_name = mb_strtolower($member_last_name);
        $last_name_pieces = explode(' ', $last_name);
        $last_name = array();
        foreach($last_name_pieces as $piece) {
            array_push($last_name, ucfirst($piece));
        }
        $last_name = implode(' ', $last_name);
        $last_name_pieces = explode('-', $last_name);
        $last_name = array();
        foreach($last_name_pieces as $piece) {
            array_push($last_name, ucfirst($piece));
        }
        $last_name = implode('-', $last_name);
        return $last_name;
    }

    /**
     *
     * Check if a string entered is a valid date
     *
     * @param       [string]    date
     * @return      [boolean]   valid_date
     *
     */
    function validate_date($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     *
     * Convert dd/mm/yyyy date to yyyy-mm-dd, so convert invalid date to valid one
     *
     * @param       [string]    invalid date
     * @return      [string]    valid date
     *
     */
    public function format_date($date) {
        //split date by delimter
        $new_date   = explode('/', $date);
        //reverse the order from dd,mm,yy to yy,mm,dd
        $new_date   = array_reverse($new_date);
        //join together again with - as delimiter
        $new_date   = implode('-', $new_date);
        return $new_date;
    }

    /**
     *
     * Singles ranking property is different every year, check if a valid property exists for this year, the previous one, or the next
     *
     * @param       [string]    member row to test on
     * @return      [string]    property name
     *
     */
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
        return $e_year;
    }

    /**
     *
     * Convert number ranking to full ranking
     *
     * @param       [integer]   ranking as number
     * @return      [string]    ranking in full format
     *
     */
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

    /**
     *
     * Determine level based on birthdate (age)
     *
     * @param       [date]      birthdate 
     * @return      [integer]   level
     *
     */
    public function get_level_by_birth_date($birth_date) {
    	if($birth_date) {
    		//get age
	    	$age = intval(date('Y')-date('Y', strtotime($birth_date)));
	    	if($age >= 13) {
	    		$level = 6;
	    	}
            elseif($age >= 11) {
                $level = 5;
            }
            elseif($age >= 9) {
                $level = 4;
            }
            elseif($age >= 7) {
                $level = 3;
            }
            elseif($age >= 5) {
                $level = 2;
            }
            elseif($age >= 2) {
                $level = 1;
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

    /**
     *
     * Generate random password
     *
     * @return      [string]   random password
     *
     */
    public function get_random_password() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $password[] = $alphabet[$n];
        }
        return implode($password);
    }

    /**
     *
     * Return an array with all rankings
     *
     * @return      [array]   rankings
     *
     */
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
