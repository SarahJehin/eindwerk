<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;
use App\User;
use Illuminate\Support\Facades\Auth;
use Excel;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    //normal users
    public function activities_overview() {
        $activities = Activity::all();

        $is_admin = false;
        $user_roles = Auth::user()->roles->pluck('level')->toArray();
        if (Auth::user() && $user_roles && min($user_roles) < 30) {
            $is_admin = true;
        }

        return view('activities_overview', ['activities' => $activities, 'is_admin' => $is_admin]);
    }

    public function activity_details($id) {
        $activity = Activity::where('id', $id)->with('category')->first();
        $is_admin = false;
        
        $user_signed_up = false;
        if($activity->participants->contains(Auth::user()->id)) {
            //check if user has already signed up for this activity
            //status of the currently logged in user for this activity
            $status = $activity->participants->where('id', Auth::user()->id)->first()->pivot->status;
            if($status == 1 || $status == 2) {
                $user_signed_up = true;
            }
        }
        //check if currently logged in user is admin (has roles board or youth_board), if yes: show edit buttons:
        $user_roles = Auth::user()->roles->pluck('level')->toArray();
        if (Auth::user() && $user_roles && min($user_roles) < 30) {
            $is_admin = true;
        }
        //dd($activity->participants);
        
        /*
        if ($activity->participants->contains(Auth::user()->id)) {
            $user_signed_up = true;
        }
        */
        //dd($activity->participants->where('id', 1)->first()->pivot->status);

        //dd($activity->participants[0]->pivot->status);
        return view('activities/activity_details', ['activity' => $activity, 'user_signed_up' => $user_signed_up, 'is_admin' => $is_admin]);
    }

    public function sign_up_for_activity(Request $request) {
        $activity = Activity::find($request->activity_id);
        $activity_is_free = !intval($activity->price);
        //dd($request);
        if($request->sign_up_me == 'on') {
            $user = User::find(Auth::user()->id);
            
            //check if the record already exists, if yes, update, if not create
            if($activity->users->contains(Auth::user()->id)) {
                $user->activities()->updateExistingPivot($request->activity_id, ['status' => 1]);
            }
            else {
                $user->activities()->attach($request->activity_id, ['signed_up_by' => $user->id, 'status' => 1]);
            }
            //if it's a free activity update status to '2', which means paid
            if($activity_is_free) {
                $user->activities()->updateExistingPivot($request->activity_id, ['status' => 2]);
            }
        }
        if($request->sign_up_others == 'on') {
            $participants = $request->participant;
            array_shift($participants);
            
            foreach ($participants as $key => $participant_id) {
                $user = User::find($participant_id);
                //check if the record already exists, if yes, update, if not create
                if($activity->participants->contains($participant_id)) {
                    $user->activities()->updateExistingPivot($request->activity_id, ['status' => 1]);
                }
                else {
                    $user->activities()->attach($request->activity_id, ['signed_up_by' => Auth::user()->id, 'status' => 1]);
                }
                //if it's a free activity update status to '2', which means paid
                if($activity_is_free) {
                    $user->activities()->updateExistingPivot($request->activity_id, ['status' => 2]);
                }
            }
        }

        return redirect('activity_details/' . $request->activity_id)->with('success_msg', 'Dankjewel voor je inschrijving!');
    }

    public function sign_out_for_activity(Request $request) {
        $user = User::find($request->user_id);
        //set status to 10 (signed out)
        $user->activities()->updateExistingPivot($request->activity_id, ['status' => 10]);
        if($request->user_id == Auth::user()->id) {
            return redirect('activity_details/' . $request->activity_id)->with('success_msg', 'Je bent uitgeschreven voor deze activiteit.');
        }
        else {
            return redirect('activity_participants/' . $request->activity_id)->with('success_msg', 'Je hebt ' . $user->first_name . ' ' . $user->last_name . ' uitgeschreven voor deze activiteit.');
        }
    }


    public function get_scoreboard() {
        //all adult activities
        $adult_activities = Activity::where('is_visible', 1)
                                ->where('start', '<', date('Y-m-d'))
                                ->whereHas('category', function ($query) {
                                    $query->where('root', 'adult');
                                })
                                ->orderBy('start')
                                ->get();
        //all users who have participated in adult activities
        $adult_participants = User::has('adult_activities_past')
                                    ->with('adult_activities_past')
                                    ->orderBy('last_name')
                                    ->orderBy('first_name')
                                    ->get();
        //top 3 for adults
        //$adult_top_3 = $this->get_top_3('adult');
        $adult_top_3 = $this->get_top_3_from_collection($adult_participants, 'adult');
        //dd($adult_top_3);

        //all youth activities
        $youth_activities = Activity::where('is_visible', 1)
                                ->where('start', '<', date('Y-m-d'))
                                ->whereHas('category', function ($query) {
                                    $query->where('root', 'youth');
                                })
                                ->orderBy('start')
                                ->get();
        //all users who have participated in youth activities
        $youth_participants = User::has('youth_activities_past')
                                    ->with('youth_activities_past')
                                    ->orderBy('last_name')
                                    ->orderBy('first_name')
                                    ->get();
        //top 3 of youth
        $youth_top_3 = $this->get_top_3_from_collection($youth_participants, 'youth');

        return view('scoreboard/scoreboard', [  'adult_activities'      => $adult_activities,
                                                'adult_participants'    => $adult_participants,
                                                'adult_top_3'           => $adult_top_3,
                                                'youth_activities'      => $youth_activities,
                                                'youth_participants'    => $youth_participants,
                                                'youth_top_3'           => $youth_top_3
                                                ]);
    }

    public function get_top_3_from_collection($users_collection, $youth_adult) {
        $array_to_sort = array();
        foreach ($users_collection as $user) {
            if($youth_adult == 'youth') {
                $score = $user->total_youth_score();
            }
            elseif ($youth_adult == 'adult') {
                $score = $user->total_score();
            }
            else {
                return null;
            }
            array_push($array_to_sort, ['score' => $score,
                                        'id' => $user->id,
                                        'last_name' => $user->last_name,
                                        'first_name' => $user->first_name]);
        }
        $sort = array();
        foreach($array_to_sort as $k=>$v) {
            $sort['score'][$k] = $v['score'];
            $sort['last_name'][$k] = $v['last_name'];
            $sort['first_name'][$k] = $v['first_name'];
        }
        //first sort by score descending, then by last name ascending, then by first name ascending
        array_multisort($sort['score'], SORT_DESC, $sort['last_name'], SORT_ASC, $sort['first_name'], SORT_ASC, $array_to_sort);
        //get first 3 results
        $top3_array = array_slice($array_to_sort, 0, 3, true);
        //get users for top 3
        $top3 = array();
        foreach ($top3_array as $user) {
            array_push($top3, User::find($user['id']));
        }
        //if there are less than 3 users, top3 doesn't exist and podium should not be displayed
        if(count($top3) < 3) {
            $top3 = null;
        }
        return $top3;
    }


    /* ************************ ADMIN FUNCTIONS ********************** */




    //admins
    public function add_activity() {
        $categories = Category::all();
        //dd($categories);
        //onderstaande moet nog aangepast worden (waar rol = jeugdbestsuur)
        $possible_owners = User::whereHas('roles', function ($query) {
                                    $query->where('level', '<', 30);
                                })->get();
        return view('activities/add_activity', ['categories' => $categories, 'owners' => $possible_owners]);
    }

    public function create_activity(Request $request) {

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];

        if($request->is_visible == "on") {
            $is_visible = 1;
        }
        else {
            $is_visible = 0;
        }

        $made_by = Auth::user()->id;

        if($request->location_type == 'sportiva') {
            $location = "Sportiva (Industriepark 5, Hulshout)";
            $latitude = 51.083253;
            $longitude = 4.805906;
        }
        else {
            $location = $request->location;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
        }

        //get day before startdate, in order to check whether the enddate is equal to or after startdate
        $startdate = strtotime($request->startdate);
        $day_after_start = strtotime("tomorrow", $startdate);
        $formatted_day_after = date('Y-m-d', $day_after_start);

        $rules = [
            'category'      => 'required',
            'title'         => 'required|string',
            'description'   => 'required',
            'poster'        => 'required',
            'startdate'     => 'required|date',
            'starttime'     => 'required|date_format:H:i',
            'endtime'       => 'nullable|date_format:H:i|after:starttime',
            'deadline'      => $request->deadline != null ? 'date|before:' . $formatted_day_after . '|after:today': '',
            //'location'      => 'required|string',
            'helpers'       => 'required|integer|max:20',
            'price'         => 'required|integer|max:20',
            'owner'         => 'required',
            'extra_url'     => $request->extra_url != null ? 'url': '',
        ];

        if($request->location_type == 'else') {
            $rules['location']  = 'required';
            $rules['longitude'] = 'required';
            $rules['latitude']  = 'required';
        }
        
        $this->validate($request, $rules);


        $allowed_extensions = ["jpeg", "png"];
        if ($request->hasFile('poster')) {
            if ($request->poster->isValid()) {
                if (in_array($request->poster->guessClientExtension(), $allowed_extensions)) {
                    //create new file name
                    $name = strtolower($request->title);
                    //keep only letters, numbers and spaces
                    $name = preg_replace("/[^A-Za-z0-9 ]/", "", $name);
                    //remove space at the beginning and end
                    $name = trim($name);
                    //convert all multispaces to space
                    $name = preg_replace ("/ +/", " ", $name);
                    //replace all spaces with underscores
                    $name = str_replace(' ', '_', $name);

                    $new_file_name = time() . $name . '.' . $request->poster->getClientOriginalExtension();
                    //echo($new_file_name);
                    $request->poster->move(base_path() . '/public/images/activity_images/', $new_file_name);
                }
            }
        }

        /*
        if (isset($request->poster)) {

            //later on image will be resized with Intervention, while keeping the aspect ratio
            $destinationPath = base_path() . '/public/images/project_pictures/' . $new_file_name;
            $dimension = getimagesize($destinationPath);
            $max_width = "500";
            $max_height = "400";
            if ($dimension[0] > $max_width) {
                $save_percent = round(100 / $dimension[0] * $max_width) / 100;
                $max_height = round($save_percent * $dimension[1]);
                Image::make($destinationPath)
                    ->resize($max_width, $max_height)->save($destinationPath);
            }
            if ($dimension[1] > $max_height) {
                $save_percent = round(100 / $dimension[1] * $max_height) / 100;
                $max_width = round($save_percent * $dimension[0]);
                Image::make($destinationPath)
                    ->resize($max_width, $max_height)->save($destinationPath);
            }

            //resizing with default canvas size, while maintaining aspect ratio:
            // create new image with transparent background color
            $background = Image::canvas(200, 200);
            // read image file and resize it to 200x200
            // but keep aspect-ratio and do not size up,
            // so smaller sizes don't stretch
            $image = Image::make('foo.jpg')->resize(200, 200, function ($c) {
                $c->aspectRatio();
                $c->upsize();});
            // insert resized image centered into background
            $background->insert($image, 'center');
            // save or do whatever you like
            $background->save('bar.png');

        }
        */

        $startdatetime = date('Y-m-d', strtotime($request->startdate));
        $startdatetime = $startdatetime . ' ' . $request->starttime  . ':00';
        //echo($startdatetime);
        $enddatetime = date('Y-m-d', strtotime($request->startdate));
        $enddatetime = $enddatetime . ' ' . $request->endtime  . ':00';

        $deadlinedatetime = date('Y-m-d', strtotime($request->deadline)) . ' ' . '23:59:59';


        $activity = new Activity([
            'title'         => $request->title,
            'description'   => $request->description,
            'poster'        => $new_file_name,
            'extra_url'     => $request->extra_url,
            'start'         => $startdatetime,
            'deadline'      => $deadlinedatetime,
            'end'           => $enddatetime,
            'location'      => $location,
            'latitude'      => $latitude,
            'longitude'     => $longitude,
            'min_participants'  => $min_participants,
            'max_participants'  => $max_participants,
            'helpers'           => $request->helpers,
            'price'             => $request->price,
            //'youth_adult'       => $youth_adult,
            'is_visible'        => $is_visible,
            'status'            => 1,
            'made_by_id'        => $made_by,
            'owner_id'          => $request->owner,
            'category_id'       => $request->category
        ]);

        //dd($activity);

        $activity->save();

        return redirect('activities_overview')->with('message', 'Activiteit succesvol toegevoegd');
    }

    public function edit_activity($id) {
        $activity = Activity::find($id);
        if($activity->location == 'Sportiva (Industriepark 5, Hulshout)') {
            $activity['location_type'] = 'sportiva';
        }
        else {
            $activity['location_type'] = 'else';
        }
        $owner = User::find($activity->owner_id);
        $activity['owner_name'] = $owner->first_name . ' ' . $owner->last_name;
        $categories = Category::all();
        //onderstaande moet nog aangepast worden (waar rol = jeugdbestsuur)
        $possible_owners = User::all();
        return view('activities/edit_activity', ['activity' => $activity, 'categories' => $categories, 'owners' => $possible_owners]);
    }

    public function update_activity(Request $request) {
        $activity = Activity::find($request->activity_id);

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];

        if($request->is_visible == "on") {
            $is_visible = 1;
        }
        else {
            $is_visible = 0;
        }

        $made_by = Auth::user()->id;

        if($request->location_type == 'sportiva') {
            $location = "Sportiva (Industriepark 5, Hulshout)";
            $latitude = 51.083253;
            $longitude = 4.805906;
        }
        else {
            $location = $request->location;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
        }

        //get day before startdate, in order to check whether the enddate is equal to or after startdate
        $startdate = strtotime($request->startdate);
        $day_after_start = strtotime("tomorrow", $startdate);
        $formatted_day_after = date('Y-m-d', $day_after_start);

        $rules = [
            'category'      => 'required',
            'title'         => 'required|string',
            'description'   => 'required',
            'startdate'     => 'required|date',
            'starttime'     => 'required|date_format:H:i',
            'endtime'       => 'nullable|date_format:H:i|after:starttime',
            'deadline'      => $request->deadline != null ? 'date|before:' . $formatted_day_after . '|after:today': '',
            'helpers'       => 'required|integer|max:20',
            'price'         => 'required|integer|max:20',
            'owner'         => 'required',
            'extra_url'     => $request->extra_url != null ? 'url': '',
        ];

        if($request->location_type == 'else') {
            $rules['location']  = 'required';
            $rules['longitude'] = 'required';
            $rules['latitude']  = 'required';
        }
        
        $this->validate($request, $rules);

        $allowed_extensions = ["jpeg", "png"];
        if ($request->hasFile('poster')) {
            if ($request->poster->isValid()) {
                if (in_array($request->poster->guessClientExtension(), $allowed_extensions)) {
                    //create new file name
                    $name = strtolower($request->title);
                    //keep only letters, numbers and spaces
                    $name = preg_replace("/[^A-Za-z0-9 ]/", "", $name);
                    //remove space at the beginning and end
                    $name = trim($name);
                    //convert all multispaces to space
                    $name = preg_replace ("/ +/", " ", $name);
                    //replace all spaces with underscores
                    $name = str_replace(' ', '_', $name);

                    $new_file_name = time() . $name . '.' . $request->poster->getClientOriginalExtension();
                    //echo($new_file_name);
                    $request->poster->move(base_path() . '/public/images/activity_images/', $new_file_name);

                    $activity->poster = $new_file_name;
                }
            }
        }
        
        $startdatetime = date('Y-m-d', strtotime($request->startdate));
        $startdatetime = $startdatetime . ' ' . $request->starttime  . ':00';
        //echo($startdatetime);
        $enddatetime = date('Y-m-d', strtotime($request->startdate));
        $enddatetime = $enddatetime . ' ' . $request->endtime  . ':00';

        $deadlinedatetime = date('Y-m-d', strtotime($request->deadline)) . ' ' . '23:59:59';

        $activity->title             = $request->title;
        $activity->description       = $request->description;
        $activity->extra_url         = $request->extra_url;
        $activity->start             = $startdatetime;
        $activity->deadline          = $deadlinedatetime;
        $activity->end               = $enddatetime;
        $activity->location          = $location;
        $activity->latitude          = $latitude;
        $activity->longitude         = $longitude;
        $activity->min_participants  = $min_participants;
        $activity->max_participants  = $max_participants;
        $activity->helpers           = $request->helpers;
        $activity->price             = $request->price;
        $activity->is_visible        = $is_visible;
        $activity->made_by_id        = $made_by;
        $activity->owner_id          = $request->owner;
        $activity->category_id       = $request->category;

        //dd($activity);

        $activity->save();

        return redirect('edit_activity/' . $activity->id)->with('success_msg', 'De activiteit werd geüpdatet.');
    }

    public function delete_activity(Request $request) {
        $activity = Activity::find($request->activity_id);
        //delete the activity
        $activity->delete();
        //delete all users related to this activity
        $activity->users()->detach();

        return redirect('activities_list')->with('success_msg', 'De activiteit werd verwijderd');
    }

    public function get_activities_list() {
        $activities = Activity::select('id', 'title', 'start', 'max_participants', 'is_visible')
                                ->with('participants')
                                ->where('start', '>', date('Y-m-d').' 00:00:00')
                                ->orderBy('start')
                                ->get();

        $past_activities = Activity::select('id', 'title', 'start', 'max_participants', 'is_visible')
                                ->with('participants')
                                ->where('start', '<', date('Y-m-d').' 00:00:00')
                                ->orderBy('start', 'desc')
                                ->get();
        //dd($activities);
        
        return view('activities/admin_activities_overview', ['activities' => $activities, 'past_activities' => $past_activities]);
    }

    public function get_activity_participants($id) {
        $activity = Activity::find($id);
        
        foreach ($activity->participants as $key => $participant) {
            $participant->pivot["signed_up_by_user"] = User::select('id', 'first_name', 'last_name')
                                                            ->where('id', $participant->pivot->signed_up_by)
                                                            ->orderBy('last_name')
                                                            ->orderBy('first_name')
                                                            ->first();
        }
        //dd($activity->participants[0]->pivot->signed_up_by_user);
        return view('activities/activity_participants_overview', ['activity' => $activity]);
    }

    public function download_participants_as_excel($activity_id) {
        $activity = Activity::where('id', $activity_id)->with(array('participants'=>function($query){
            $query->select('users.id','first_name', 'last_name', 'activity_user.status as betaald');
        }))->first();
        
        foreach ($activity->participants as $key => $participant) {
            if($participant->betaald == 2) {
                $participant->betaald = 'JA';
            }
            else {
                $participant->betaald = 'NEEN';
            }
        }
        $participants_full_array = $activity->participants->toArray();
        $participants_array = array();
        foreach ($participants_full_array as $participant) {
            array_push($participants_array, ['achternaam'   => $participant['last_name'],
                                             'voornaam'     => $participant['first_name'],
                                             'betaald'      => $participant['betaald']]);
        }
        //export participants as Excel file
        return Excel::create('deelnemers_' . $activity->title, function($excel) use ($participants_array) {
            $excel->sheet('mySheet', function($sheet) use ($participants_array) {
                $sheet->fromArray($participants_array);
            });
        })->download('xlsx');
    }

    
}
