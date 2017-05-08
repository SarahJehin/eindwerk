<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Excel;
use Hash;

class UserController extends Controller
{
    //

    public function import_members(Request $request) {

    	//check if post request contains file, if yes, read out the excel file, with all the members
    	if ($request->hasFile('members_excel')) {
		    //
		    $path = $request->file('members_excel')->getRealPath();
			$data = Excel::load($path, function($reader) {
			})->get();
			$test = array();
			if(!empty($data) && $data->count()){
				$sheet1 = $data[0];
				$password	= Hash::make('sportiva');
				foreach ($sheet1 as $key => $member) {
					$vtv_nr 	= $member->vtv;
					$email		= null;
					$first_name = ucfirst(mb_strtolower($member->voornaam));
					$last_name 	= ucfirst(mb_strtolower($member->naam));
					$gsm_nr 	= '0' . str_replace(' ', '', (string)$member->gsmnr);
					$birth_date = $member->datum;
					$gender		= strtoupper($member->mv);
					if($member->e_2017 == 'NG') {
						$ranking_singles = 'NG (5)';
						$ranking_doubles = 'NG (5)';
					}
					else {
						$ranking_singles = $this->number_to_ranking($member->e);
						$ranking_doubles = $this->number_to_ranking($member->d);
					}
					$image		= null;
					$level		= $this->get_level_by_birth_date($birth_date);
					$tel_nr 	= '0' . str_replace(' ', '', (string)$member->telefoonnr);
					echo($member->naam . ' ' . $member->voornaam . ' (' .$vtv_nr . '):  ' . $ranking_singles . $ranking_doubles . '<br>');
					//check if a user with this vtv nr already exists, if not instantiate a new one
					//actually better updateOrCreate //but maybe not because level should not be overrided, nor should password
					$user = User::firstOrNew(
					    ['vtv_nr' => $vtv_nr], 
					    ['email' 		=> $email,
					     'first_name'	=> $first_name,
					     'last_name'	=> $last_name,
					     'gsm'			=> $gsm_nr,
					     //'tel_nr'		=> $tel_nr,
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
						echo('user, already exists, only some attributes will be updated');
						$user->ranking_singles 	= $ranking_singles;
						$user->ranking_doubles 	= $ranking_doubles;
						$user->gsm_nr			= $gsm_nr;
						$user->tel_nr			= $tel_nr;
					}
					else {
						//new user was initialized, save to create
						echo('completely new');
					}
					dump($user);
					$user->save();
				}
				/*
				foreach ($data as $key => $value) {
					array_push($test, $value);
					//$insert[] = ['title' => $value->title, 'description' => $value->description];
				}
				dd($test);*/
				/*
				if(!empty($insert)){
					DB::table('items')->insert($insert);
					dd('Insert Record successfully.');
				}
				*/
				//dd($insert);
			}
		}
		dd('test');
    	dd($request);
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
}
