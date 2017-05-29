<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Tag;
use App\Image;
use Auth;
use Session;

class ExerciseController extends Controller
{
    public function exercises_overview() {
    	//dd('exercises_overview');
    	$exercises = Exercise::where('approved', 1)->get()->shuffle();
    	$tag_types = Tag::all()->groupBy('type');
    	//extract the newest to show on top
    	$newest_exercise = Exercise::where('approved', 1)->orderBy('created_at', 'desc')->first();
    	//dd($newest_exercise->images);
    	//extract the 6 most viewed exercises to show in a 'Most Viewed' section
    	$most_viewed_exercises = Exercise::orderBy('views', 'desc')->limit(4)->get();
    	//dd($most_viewed_exercises);

    	$exercises_to_approve = Exercise::where('approved', 0)->get();
    	return view('exercises/exercises_overview', ['exercises' => $exercises, 'newest_exercise' => $newest_exercise, 'most_viewed_exercises' => $most_viewed_exercises, 'tag_types' => $tag_types, 'exercises_to_approve' => $exercises_to_approve]);
    }

    public function exercise_details($id) {
    	$exercise = Exercise::find($id);
    	$is_headtrainer = Auth::user()->roles->whereIn('level', [31])->first();
    	//dd($exercise->tags);
    	//handle exercise views
    	$client_ip = \Request::ip();
    	//Session::put('client_ip', $client_ip);
    	//$test = Session::get('client_ip');
    	session_start();
    	//$_SESSION['client_ip'] = $client_ip;
    	$session_set = $_SESSION['client_ip'];
    	//dd($session_set);
    	//if the session was not set yet, set it and add the views for this exercise
    	if(!$session_set) {
    		$_SESSION['client_ip'] = $client_ip;
    		$exercise->views += 1;
    		$exercise->save();
    	}
    	if($exercise->approved || $is_headtrainer) {
    		return view('exercises/exercise_details', ['exercise' => $exercise]);
    	}
    	else {
    		abort(404);
    	}
    }

    public function add_exercise() {
		$tag_types = Tag::all()->groupBy('type');
    	return view('exercises/add_exercise', ['tag_types' => $tag_types]);
    }

    public function create_exercise(Request $request) {

    	$this->validate($request, [
    							'title'			=> 'required|string',
    							'description'	=> 'required|max:1000',
    							'tags'			=> 'required|array|min:1',
    							'image'			=> 'required|array|min:1',
    							'image.*'		=>	'max:500'
    		]);

        $exercise = new Exercise([
            'name'         	=> $request->title,
            'description'  	=> $request->description,
            'views'			=> 0,
            'made_by'      	=> Auth::user()->id,
            'approved'		=> 0
        ]);
        $exercise->save();

        //tags
        foreach ($request->tags as $tag) {
        	$exercise->tags()->attach($tag);
        }

        //images
        if ($request->hasFile('image')) {
    		$already_added_imgs = array();
    		$allowed_extensions = ["jpeg", "png"];
    		$order = 0;
            foreach ($request->image as $image) {
            	$img_name_and_size = $image->getClientOriginalName() . $image->getClientSize();
            	//first check if the file is in the name and size array (which only contains not deleted images)
            	if(in_array($img_name_and_size, $request->name_and_size)) {
            		//also check if the image wasn't already uploaded
            		if(!in_array($img_name_and_size, $already_added_imgs)) {
            			//add image
            			if (in_array($image->guessClientExtension(), $allowed_extensions)) {
		                    //create new file name
		                    $name = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
		                    //keep only letters, numbers and spaces
		                    $name = preg_replace("/[^A-Za-z0-9 ]/", "", $name);
		                    //remove space at the beginning and end
		                    $name = trim($name);
		                    //convert all multispaces to space
		                    $name = preg_replace ("/ +/", " ", $name);
		                    //replace all spaces with underscores
		                    $name = str_replace(' ', '_', $name);

		                    $new_file_name = time() . $name . '.' . $image->getClientOriginalExtension();
		                    //dump($new_file_name);
		                    $image->move(base_path() . '/public/images/exercise_images/', $new_file_name);
		                    array_push($already_added_imgs, $img_name_and_size);
		                    $order++;
		                    $image = new Image([
					            'title'			=> $exercise->name . ' ' . $order,
					            'path'			=> $new_file_name,
					            'order'			=> $order,
					            'exercise_id'	=> $exercise->id
					        ]);
					        $image->save();
		                }
            		}
            	}
            }
        }
        return redirect('exercises_overview')->with('success_msg', 'Je hebt de oefening toegevoegd.  Wanneer de hoofdtrainer hem geaccepteerd heeft, verschijnt hij bij op het overzicht.');
    	dd($request);
    }

    public function deny_exercise($id) {
    	//only if authenticated user is headtrainer
    	if(Auth::user()->isHeadtrainer()) {
    		//update status to 10, which means denied
    		$exercise = Exercise::find($id);
    		$exercise->approved = 10;
    		$exercise->save();
    	}
    	return redirect('exercises_overview');
    }

    public function approve_exercise($id) {
    	//only if authenticated user is headtrainer
    	if(Auth::user()->isHeadtrainer()) {
    		//update status to 1, which means approved
    		$exercise = Exercise::find($id);
    		$exercise->approved = 1;
    		$exercise->save();
    	}
    	return redirect('exercises_overview');
    }
}
