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
            /*
            $test = User::whereIn('id', [1,2,3])->sum('id');
            $test2 = User::where('id', Auth::user()->id)->first()->activities->sum('min_participants');
            //dit hieronder werkt, nog aan te passen ipv sum(status) -> sum(extra_points) en dan nog + ne count
            $test4 = DB::table('activity_user')
                        ->where('user_id', Auth::user()->id)
                        ->where('activity_user.status', 2)
                        ->join('activities', 'activities.id', '=', 'activity_user.activity_id')
                        ->where('activities.start', '<', date('Y-m-d') . ' 00:00:00')
                        ->sum('activity_user.status');
            $test5 = User::where('id', Auth::user()->id)
                                    ->has('activities_as_participant_past')
                                    ->with('activities_as_participant_past')
                                    ->first()
                                    ->activities_as_participant_past
                                    ->count();
                                    */
            $current_score = User::where('id', Auth::user()->id)->first()->total_score();
            //dd($current_score);
            //dd($user->activities_as_participant_coming);
            return view('home', ['user' => $user, 'current_score' => $current_score]);
        }
        else {
            return view('welcome');
        }
        
    }
}
