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
            
            $badges = array();
            //adult
            $total_adult_activities = count($user->adult_activities_past);
            if($total_adult_activities >= 5) {
                //first badge is earned
                array_push($badges, ['amount_activities' => 5,
                                     'title' => 'Deelgenomen aan 5 activiteiten!',
                                     'bg_color' => '#e8780a']);
            }
            if($total_adult_activities >= 10) {
                //first badge is earned
                array_push($badges, ['amount_activities' => 10,
                                     'title' => 'Deelgenomen aan 10 activiteiten!',
                                     'bg_color' => '#5abfcc']);
            }
            if($total_adult_activities >= 15) {
                //first badge is earned
                array_push($badges, ['amount_activities' => 15,
                                     'title' => 'Deelgenomen aan 15 activiteiten!',
                                     'bg_color' => '#751a8b']);
            }
            //youth
            $total_youth_activities = count($user->youth_activities_past);
            if($total_youth_activities >= 3) {
                //first badge earned
                array_push($badges, ['amount_activities' => 3,
                                     'title' => 'Deelgenomen aan 3 jeugdactiviteiten!',
                                     'bg_color' => '#395696']);
            }
            if($total_youth_activities >= 6) {
                //second badge earned
                array_push($badges, ['amount_activities' => 6,
                                     'title' => 'Deelgenomen aan 6 jeugdactiviteiten!',
                                     'bg_color' => '#e51853']);
            }
            if($total_youth_activities >= 9) {
                //third badge earned
                array_push($badges, ['amount_activities' => 9,
                                     'title' => 'Deelgenomen aan 9 jeugdactiviteiten!',
                                     'bg_color' => '#307924']);
            }

            $winterhours = array();
            //foreach winterhour get the first three playdates for authenticated user
            foreach ($user->winterhours->where('status', 4) as $winterhour) {
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

            return view('home', ['user' => $user, 'badges' => $badges, 'winterhours' => $winterhours]);
        }
        else {
            session_start();
            unset($_SESSION['client_viewed_exercises']);
            return view('welcome');
        }
        
    }
}
