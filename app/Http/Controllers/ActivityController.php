<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;
use App\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function activities_overview() {
        $activities = Activity::all();

        return view('activities_overview', ['activities' => $activities]);

    }

    public function activity_details($id) {
        $activity = Activity::where('id', $id)->with('category')->first();

        return view('activities/activity_details', ['activity' => $activity]);

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
            'deadline'      => $request->startdate != null ? 'date|before:' . $formatted_day_after . '|after:today': '',
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


        $activity = new Activity([
            'title'         => $request->title,
            'description'   => $request->description,
            'poster'        => $new_file_name,
            'extra_url'     => $request->extra_url,
            'startdate'     => $startdatetime,
            'deadline'      => $request->deadline,
            'location'      => $location,
            'latitude'      => $latitude,
            'longitude'     => $longitude,
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
