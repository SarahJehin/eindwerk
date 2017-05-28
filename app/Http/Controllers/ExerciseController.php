<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Tag;
use App\Image;
use Auth;

class ExerciseController extends Controller
{
    public function exercises_overview() {
    	//dd('exercises_overview');
    	$exercises = Exercise::where('approved', 1)->get()->shuffle();
    	$tag_types = Tag::all()->groupBy('type');
    	//dd($tag_types);
    	//dd($exercises);
    	//extract the newest to show on top
    	$newest_exercise = Exercise::where('approved', 1)->orderBy('created_at', 'desc')->first();
    	//dd($newest_exercise->images);
    	//extract the 6 most viewed exercises to show in a 'Most Viewed' section
    	return view('exercises/exercises_overview', ['exercises' => $exercises, 'newest_exercise' => $newest_exercise, 'tag_types' => $tag_types]);
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
}
