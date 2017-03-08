<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function add_activity() {
        $categories = Category::all();
        //dd($categories);
        return view('activities/add_activity', ['categories' => $categories]);
    }

    public function create_activity(Request $request) {

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];

        //onderstaande bepalen adhv persoon die activiteit gemaakt heeft
        $youth_adult = "youth";

        if($request->is_visible == "on") {
            $is_visible = 1;
        }
        else {
            $is_visible = 0;
        }

        $made_by = Auth::user()->id;

        dd($request, $made_by);

        $this->validate($request, [
            'title'         => 'required|string',
            'description'   => 'required',
            'poster'        => 'required',
            'extra_url'     => $request->extra_url,
            'startdate'     => 'required|date',
            'deadline'      => 'required|date|before:startdate',
            'location'      => 'required|string',
            'min_participants'  => 'required|integer|max:29',
            'max_participants'  => 'required|integer|max:30',
            'helpers'           => 'required|integer|max:20',
            'price'             => 'required|integer|max:20',
            'owner_id'          => 'required',
        ]);

        $activity = new Activity([
            'title'         => $request->title,
            'description'   => $request->description,
            'poster'        => 'poster.jpg',
            'extra_url'     => $request->extra_url,
            'startdate'     => $request->start_date,
            'deadline'      => $request->deadline,
            'location'      => $request->location,
            'min_participants'  => $min_participants,
            'max_participants'  => $max_participants,
            'helpers'           => $request->helpers,
            'price'             => $request->price,
            'youth_adult'       => $youth_adult,
            'is_visible'        => $is_visible,
            'made_by_id'        => $made_by,
            'owner_id'          => $request->owner,
        ]);

        //dd($activity);
    }
}
