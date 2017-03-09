<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;
use App\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{


    public function activities_overview() {
        $activities = Activity::all();

        return view('activities_overview', ['activities' => $activities]);

    }

    public function add_activity() {
        $categories = Category::all();
        //dd($categories);
        //onderstaande moet nog aangepast worden (waar rol = jeugdbestsuur)
        $possible_owners = User::all();
        return view('activities/add_activity', ['categories' => $categories, 'owners' => $possible_owners]);
    }

    public function create_activity(Request $request) {

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];

        //onderstaande bepalen adhv persoon die activiteit gemaakt heeft
        //0 = adult, 1 = youth
        $youth_adult = 1;

        if($request->is_visible == "on") {
            $is_visible = 1;
        }
        else {
            $is_visible = 0;
        }

        $made_by = Auth::user()->id;
        //$location = "Sportiva";

        //dd($request, $made_by);

        $this->validate($request, [
            'title'         => 'required|string',
            'description'   => 'required',
            'poster'        => 'required',
            'startdate'     => 'required|date',
            'deadline'      => 'required|date|before:startdate',
            'location'      => 'required|string',
            'helpers'       => 'required|integer|max:20',
            'price'         => 'required|integer|max:20',
            'owner'         => 'required',
        ]);

        $activity = new Activity([
            'title'         => $request->title,
            'description'   => $request->description,
            'poster'        => 'poster.jpg',
            'extra_url'     => $request->extra_url,
            'startdate'     => $request->startdate,
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
            'category_id'       => $request->category
        ]);

        //dd($activity);

        $activity->save();

        return redirect('activities_overview')->with('message', 'Activiteit succesvol toegevoegd');
    }
}
