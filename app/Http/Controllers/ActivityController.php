<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;
use App\User;
use Illuminate\Support\Facades\Auth;
use Excel;
use DB;
use Validator;
use Illuminate\Support\Facades\Input;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //return view (calendar with activities)
    public function activities_overview() {
        $is_admin = false;
        $user_roles = Auth::user()->roles->pluck('level')->toArray();
        if (Auth::user() && $user_roles && min($user_roles) < 30) {
            $is_admin = true;
        }

        return view('activities_overview', ['is_admin' => $is_admin]);
    }

    /**
     *
     * Return all the activities to display on Fullcalendar (along with backgroundcolor and url)
     *
     * @param       [request]   with start and end date
     * @return      [array]     visible activities
     *
     */
    public function get_calendar_activities(Request $request) {
        $calendar_activities = Activity::select('id', 'title', 'start', 'category_id', 'is_visible')->with(['category' => function($query) {
            $query->select('id', 'color as backgroundColor');
        }])->where('is_visible', 1)->get()->toArray();

        $new_calendar_activities = array();
        foreach($calendar_activities as $act) {
            $act['backgroundColor'] = $act['category']['backgroundColor'];
            $act['borderColor'] = $act['category']['backgroundColor'];
            $act['url'] = url('/') . '/activity_details/' . $act['id'];
            array_push($new_calendar_activities, $act);
        }

        return $new_calendar_activities;
    }

    //return view with activity details
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

        return view('activities/activity_details', ['activity' => $activity, 'user_signed_up' => $user_signed_up, 'is_admin' => $is_admin]);
    }

    //handle subscribing to an activity of the authenticated user or other users
    public function sign_up_for_activity(Request $request) {
        $activity = Activity::find($request->activity_id);
        $activity_is_free = !intval($activity->price);

        //check if authenticated user wants to sign up him/herself
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
                if($this->activity_has_enough_participants($activity->id)) {
                    $activity->status = 1;
                    $activity->save();
                }
            }
        }
        //check if authenticated user is signing up others for this activity
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
                    if($this->activity_has_enough_participants($activity->id)) {
                        $activity->status = 1;
                        $activity->save();
                    }
                }
            }
        }

        return redirect('activity_details/' . $request->activity_id)->with('success_msg', 'Dankjewel voor je inschrijving!');
    }

    //handle sign out of activity
    public function sign_out_for_activity(Request $request) {
        $user = User::find($request->user_id);
        //set status to 10 (signed out)
        $user->activities()->updateExistingPivot($request->activity_id, ['status' => 10]);
        $activity = Activity::find($request->activity_id);
        if(!$this->activity_has_enough_participants($activity->id)) {
                    $activity->status = 0;
                    $activity->save();
        }
        if($request->user_id == Auth::user()->id) {
            return redirect('activity_details/' . $request->activity_id)->with('success_msg', 'Je bent uitgeschreven voor deze activiteit.');
        }
        else {
            return redirect('activity_participants/' . $request->activity_id)->with('success_msg', 'Je hebt ' . $user->first_name . ' ' . $user->last_name . ' uitgeschreven voor deze activiteit.');
        }
    }

    //return scoreboard view
    public function get_scoreboard() {
        //all adult activities that are visible and had enough participants (status = 1)
        $adult_activities = Activity::where('is_visible', 1)
                                ->where('status', 1)
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
        if(!$adult_participants->isEmpty()) {
            $adult_top_3 = $this->get_top_3_from_collection($adult_participants, 'adult');
        }
        else {
            $adult_top_3 = null;
        }

        //all youth activities that are visible and had enough participants (status = 1)
        $youth_activities = Activity::where('is_visible', 1)
                                ->where('status', 1)
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
        if(!$youth_participants->isEmpty()) {
            $youth_top_3 = $this->get_top_3_from_collection($youth_participants, 'youth');
        }
        else {
            $youth_top_3 = null;
        }
        
        return view('scoreboard/scoreboard', [  'adult_activities'      => $adult_activities,
                                                'adult_participants'    => $adult_participants,
                                                'adult_top_3'           => $adult_top_3,
                                                'youth_activities'      => $youth_activities,
                                                'youth_participants'    => $youth_participants,
                                                'youth_top_3'           => $youth_top_3
                                                ]);
    }

    /**
     *
     * Return the top 3 of members for certain category
     *
     * @param       [array]     users where the top 3 will be generated from
     * @param       [string]    youth or adult
     * @return      [array]     top 3 members
     *
     */
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

    //return Excel with the scoreboard ordered based on score
    public function export_scoreboard($adult_youth) {
        if($adult_youth == 'youth') {
            $activities = Activity::where('is_visible', 1)
                                    ->where('status', 1)
                                    ->where('start', '<', date('Y-m-d'))
                                    ->whereHas('category', function ($query) {
                                        $query->where('root', 'youth');
                                    })
                                    ->orderBy('start')
                                    ->get();

            //all users who have participated in youth activities
            $participants = User::has('youth_activities_past')
                                        ->with('youth_activities_past')
                                        ->orderBy('last_name')
                                        ->orderBy('first_name')
                                        ->get()->sortByDesc('youth_activities_past');
        }
        else {
            $activities = Activity::where('is_visible', 1)
                                    ->where('status', 1)
                                    ->where('start', '<', date('Y-m-d'))
                                    ->whereHas('category', function ($query) {
                                        $query->where('root', 'adult');
                                    })
                                    ->orderBy('start')
                                    ->get();

            //all users who have participated in adult activities
            $participants = User::has('adult_activities_past')
                                        ->with('adult_activities_past')
                                        ->orderBy('last_name')
                                        ->orderBy('first_name')
                                        ->get()->sortByDesc('adult_activities_past');
        }
        
        return Excel::create('scorebord', function($excel) use ($activities, $participants, $adult_youth) {
            $excel->sheet('deelnemers', function($sheet) use ($activities, $participants, $adult_youth) {
                $header_row = $activities->pluck('title')->toArray();
                array_unshift($header_row, 'Naam');
                array_push($header_row, 'Totaal');
                $sheet->row(1, $header_row);
                $sheet->row(1, function($row) {
                    $row->setBackground('#dddddd');
                });
                foreach ($participants as $p) {
                    $p_row_values = array();
                    array_push($p_row_values, $p->last_name . ' ' . $p->first_name);
                    $i = 0;
                    foreach ($activities as $act) {
                        if($p->activities()->where('activities.id', $act->id)->exists()) {
                            array_push($p_row_values, '1');
                        }
                        else {
                            array_push($p_row_values, '0');
                        }
                        if($i == count($activities)-1) {
                            if($adult_youth == 'youth') {
                                array_push($p_row_values, $p->total_youth_score());
                            }
                            else {
                                array_push($p_row_values, $p->total_score());
                            }
                        }
                        $i++;
                    }
                    $sheet->appendRow($p_row_values);
                }
            });
        })->download('xlsx');

    }

    /* ************************ ADMIN FUNCTIONS ********************** */

    //return the add_activity view
    public function add_activity() {
        $categories = Category::all();
        $possible_owners = User::whereHas('roles', function ($query) {
                                    $query->where('level', '<', 30);
                                })->get();
        return view('activities/add_activity', ['categories' => $categories, 'owners' => $possible_owners]);
    }

    //create the activity
    public function create_activity(Request $request) {

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];
        //if max participants is greater than 30, it is actually infinity, but it will be stored in the db as integer
        //so store it as 1000, so no restrictions will pop up on subscribing due to max participants reached
        if($max_participants > 30) {
            $max_participants = 1000;
        }

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
            //'helpers'       => 'required|integer|max:20',
            'price'         => 'required|integer|max:20',
            'owner'         => 'required',
            'extra_url'     => $request->extra_url != null ? 'url': '',
        ];

        if($request->location_type == 'else') {
            $rules['location']  = 'required';
            $rules['longitude'] = 'required';
            $rules['latitude']  = 'required';
        }

        //check if there are no scripts in the wysiwyg editor
        $safe_description = true;
        if (strpos($request->description, '<script') !== false || strpos($request->description, '<?php') !== false) {
            $safe_description = false;
            Input::replace(['description' => '']);
        }
        
        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($safe_description) {
            if (!$safe_description) {
                $validator->errors()->add('description', 'Je mag geen scripts invoeren in de beschrijving!');
            }
        });
        if ($validator->fails()) {
            return redirect('add_activity')
                        ->withErrors($validator)
                        ->withInput();
        }

        //poster
        //get the data from the base64 encoded string
        $base64_encoded_image = $request->imagebase64;
        $data = explode(';', $base64_encoded_image)[1];
        $data = explode(',', $data)[1];

        $activity_pictures_path = public_path() . '/images/activity_images/';
        $name = strtolower($request->title);
        //keep only letters, numbers and spaces
        $name = preg_replace("/[^A-Za-z0-9 ]/", "", $name);
        //remove space at the beginning and end
        $name = trim($name);
        //convert all multispaces to space
        $name = preg_replace ("/ +/", " ", $name);
        //replace all spaces with underscores
        $name = str_replace(' ', '_', $name);
        $new_file_name = time() . $name . '.png';

        //decode the data
        $data = base64_decode($data);
        //save the data
        $total_path = $activity_pictures_path . $new_file_name;
        file_put_contents($total_path, $data);

        $startdatetime = date('Y-m-d', strtotime($request->startdate));
        $startdatetime = $startdatetime . ' ' . $request->starttime  . ':00';
        $enddatetime = date('Y-m-d', strtotime($request->startdate));
        $enddatetime = $enddatetime . ' ' . $request->endtime  . ':00';

        if($request->deadline) {
            $deadlinedatetime = date('Y-m-d', strtotime($request->deadline)) . ' ' . '23:59:59';
        }
        else {
            $deadlinedatetime = null;
        }

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
            'helpers'           => 0,
            'price'             => $request->price,
            'is_visible'        => $is_visible,
            'status'            => 0,
            'made_by_id'        => $made_by,
            'owner_id'          => $request->owner,
            'category_id'       => $request->category
        ]);

        $activity->save();

        return redirect('activities_overview')->with('message', 'Activiteit succesvol toegevoegd');
    }

    //return the edit_activity view
    public function edit_activity($id) {
        $activity = Activity::find($id);
        if(!$activity) {
            abort(404);
        }
        if($activity->location == 'Sportiva (Industriepark 5, Hulshout)') {
            $activity['location_type'] = 'sportiva';
        }
        else {
            $activity['location_type'] = 'else';
        }
        $owner = User::find($activity->owner_id);
        $activity['owner_name'] = $owner->first_name . ' ' . $owner->last_name;
        $categories = Category::all();

        $possible_owners = User::whereHas('roles', function ($query) {
                                    $query->where('level', '<', 30);
                                })->get();
        return view('activities/edit_activity', ['activity' => $activity, 'categories' => $categories, 'owners' => $possible_owners]);
    }

    //update the activity
    public function update_activity(Request $request) {
        $activity = Activity::find($request->activity_id);

        $min_participants = explode(",", $request->participants)[0];
        $max_participants = explode(",", $request->participants)[1];

        //if max participants is greater than 30, it is actually infinity, but it will be stored in the db as integer
        //so store it as 1000, so no restrictions will pop up on subscribing due to max participants reached
        if($max_participants > 30) {
            $max_participants = 1000;
        }

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

        //poster
        $base64_encoded_image = $request->imagebase64;
        if($base64_encoded_image) {
            $data = explode(';', $base64_encoded_image)[1];
            $data = explode(',', $data)[1];
            $activity_pictures_path = public_path() . '/images/activity_images/';
            $name = strtolower($request->title);
            //keep only letters, numbers and spaces
            $name = preg_replace("/[^A-Za-z0-9 ]/", "", $name);
            //remove space at the beginning and end
            $name = trim($name);
            //convert all multispaces to space
            $name = preg_replace ("/ +/", " ", $name);
            //replace all spaces with underscores
            $name = str_replace(' ', '_', $name);
            $new_file_name = time() . $name . '.png';

            //decode the data
            $data = base64_decode($data);
            //save the data
            $total_path = $activity_pictures_path . $new_file_name;
            file_put_contents($total_path, $data);
            $activity->poster = $new_file_name;
        }
        
        $startdatetime = date('Y-m-d', strtotime($request->startdate));
        $startdatetime = $startdatetime . ' ' . $request->starttime  . ':00';
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
        $activity->price             = $request->price;
        $activity->is_visible        = $is_visible;
        $activity->made_by_id        = $made_by;
        $activity->owner_id          = $request->owner;
        $activity->category_id       = $request->category;

        $activity->save();

        return redirect('edit_activity/' . $activity->id)->with('success_msg', 'De activiteit werd geüpdatet.');
    }

    //update paid status
    public function update_activity_participant_status(Request $request) {
        $user = User::find($request->user_id);
        $activity = Activity::find($request->activity_id);
        $is_checked = false;
        $is_checked = ($request->is_checked == 'true');
        if($is_checked) {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 2]);
            if($this->activity_has_enough_participants($activity->id)) {
                $activity->status = 1;
                $activity->save();
            }
        }
        else {
            $user->activities()->updateExistingPivot($request->activity_id, ['status' => 1]);
            if(!$this->activity_has_enough_participants($activity->id)) {
                $activity->status = 0;
                $activity->save();
            }
        }
        return $user->activities->where('id', $request->activity_id)->first()->pivot->status;
    }

    public function activity_has_enough_participants($activity_id) {
        $activity = Activity::find($activity_id);
        if(count($activity->paid_participants) >= $activity->min_participants) {
            return true;
        }
        else {
            return false;
        }
    }

    public function update_activity_visibility(Request $request) {
        $activity = Activity::find($request->activity_id);
        $activity->is_visible = $request->is_visible;
        $activity->save();
        return "success";
    }

    public function delete_activity(Request $request) {
        $activity = Activity::find($request->activity_id);
        //delete the activity
        $activity->delete();
        //delete all users related to this activity
        $activity->users()->detach();

        return redirect('activities_list')->with('success_msg', 'De activiteit werd verwijderd');
    }

    //get activities list view for admins
    public function get_activities_list() {
        $activities = Activity::select('id', 'title', 'start', 'max_participants', 'is_visible')
                                ->with('participants')
                                ->where('start', '>', date('Y-m-d').' 00:00:00')
                                ->orderBy('start')
                                ->paginate(16,['*'], 'coming_activities_page')->appends(\Request::all());

        $past_activities = Activity::select('id', 'title', 'start', 'max_participants', 'is_visible')
                                ->with('participants')
                                ->where('start', '<', date('Y-m-d').' 00:00:00')
                                ->orderBy('start', 'desc')
                                ->paginate(16,['*'], 'past_activities_page')->appends(\Request::all());
        
        return view('activities/admin_activities_overview', ['activities' => $activities, 'past_activities' => $past_activities]);
    }

    //get activity_participants view
    public function get_activity_participants($id) {
        $activity = Activity::find($id);
        
        foreach ($activity->participants as $key => $participant) {
            $participant->pivot["signed_up_by_user"] = User::select('id', 'first_name', 'last_name')
                                                            ->where('id', $participant->pivot->signed_up_by)
                                                            ->orderBy('last_name')
                                                            ->orderBy('first_name')
                                                            ->first();
        }
        return view('activities/activity_participants_overview', ['activity' => $activity]);
    }

    /**
     *
     * Return an Excel file with all participants for certain activity
     *
     * @param       [integer]   id of the activity of which you want to download the participants
     * @return      [Excel]     an Excel file with all the participants
     *
     */
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
            $excel->sheet('deelnemers', function($sheet) use ($participants_array) {
                $sheet->fromArray($participants_array);
            });
        })->download('xlsx');
    }

    
}
