<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

            return view('home', ['user' => $user, 'total_adult_score' => $total_adult_score, '$total_youth_score' => $total_youth_score]);
        }
        else {
            return view('welcome');
        }
        
    }
}
