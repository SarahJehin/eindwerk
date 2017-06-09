<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Winterhour;
use DB;

class HomeController extends Controller
{
    //return login blade when not logged in, and home when logged in
    public function index()
    {
        //check if authenticated
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            //get current ranking on scoreboard
            $current_ranking = User::where('id', Auth::user()->id)
                                    ->has('activities_as_participant_past')
                                    ->with('activities_as_participant_past')
                                    ->first();
            
            $total_adult_score = Auth::user()->total_score();
            
            $total_youth_score = null;
            //check if this is a youth member (younger than or equal to 18)
            if(date('Y')-date('Y', strtotime(Auth::user()->birth_date)) <= 18) {
                $total_youth_score = Auth::user()->total_youth_score();
            }

            $badges = array();
            //adult
            $total_adult_activities = count($user->adult_activities_past);
            if($total_adult_activities >= 5) {
                //first badge is earned
                array_push($badges, ['amount_activities' => 5,
                                     'title' => 'Deelgenomen aan 5 activiteiten!',
                                     'bg_color' => '#d07821']);
            }
            //youth
            $total_youth_activities = count($user->youth_activities_past);
            if($total_youth_activities >= 3) {
                //first badge earned
                array_push($badges, ['amount_activities' => 3,
                                     'title' => 'Deelgenomen aan 3 jeugdactiviteiten!',
                                     'bg_color' => '#395696']);
                if($total_youth_activities >= 5) {
                    //second badge earned
                }
            }

            $winterhours = array();
            //foreach winterhour get the first three playdates for me
            foreach ($user->winterhours->where('status', 4) as $winterhour) {
                //get the first 3 dates where the authenticated user plays
                //$winterhours['title'] = $winterhour->title;
                //$winterhours['dates'] = array();
                

                $amount_of_dates = 0;
                $dates = array();
                foreach ($winterhour->dates as $date) {
                    $assigned_ids = $date->assigned_participants->pluck('id')->toArray();
                    if(in_array(Auth::user()->id, $assigned_ids)) {
                        array_push($dates, $date);
                        $amount_of_dates++;
                        if($amount_of_dates > 2) {
                            array_push($winterhours, ['title' => $winterhour->title, 'dates' => $dates]);
                            break;
                        }
                    }
                }
            }
            //dd($winterhours);
            //dd($user->winterhours);

            return view('home', ['user' => $user, 'total_adult_score' => $total_adult_score, 'total_youth_score' => $total_youth_score, 'badges' => $badges, 'winterhours' => $winterhours]);
        }
        else {
            session_start();
            //session_unset();
            unset($_SESSION['client_viewed_exercises']);
            return view('welcome');
        }
        
    }
}
