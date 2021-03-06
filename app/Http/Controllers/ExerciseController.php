<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Tag;
use App\Image;
use Auth;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;

class ExerciseController extends Controller
{
    //return exercises_overview view (with newest, most viewed and all)
    public function exercises_overview() {
    	$exercises = Exercise::where('approved', 1)->paginate(16);
    	$tag_types = Tag::all()->groupBy('type');
    	$newest_exercise = null;
    	$most_viewed_exercises = null;
    	if(Request()->page == 1 || Request()->page == null) {
    		//extract the newest to show on top
	    	$newest_exercise = Exercise::where('approved', 1)->orderBy('created_at', 'desc')->first();
	    	//extract the 6 most viewed exercises to show in a 'Most Viewed' section
	    	$most_viewed_exercises = Exercise::where('approved', 1)->orderBy('views', 'desc')->limit(4)->get();
    	}

    	$exercises_to_approve = Exercise::where('approved', 0)->get();
    	return view('exercises/exercises_overview', ['exercises' => $exercises, 'newest_exercise' => $newest_exercise, 'most_viewed_exercises' => $most_viewed_exercises, 'tag_types' => $tag_types, 'exercises_to_approve' => $exercises_to_approve]);
    }

    //return the exercise_details view
    public function exercise_details($id) {
    	$exercise = Exercise::find($id);
        $video_id = substr($exercise->video_url, strrpos($exercise->video_url, '/') + 1);
        $video_thumbnail = '';
        if(strpos($exercise->video_url, 'youtu')) {
            $video_thumbnail = 'https://img.youtube.com/vi/' . $video_id . '/mqdefault.jpg';
        }
        elseif(strpos($exercise->video_url, 'vimeo')) {
            $vimeo = unserialize(file_get_contents("http://vimeo.com/api/v2/video/" . $video_id . ".php"));
            $video_thumbnail = $vimeo[0]['thumbnail_medium'];
        }  
    	$is_headtrainer = Auth::user()->roles->whereIn('level', [31])->first();
    	session_start();
    	$client_viewed_exercises = array();

        //exercises already viewed during this session are stored in a session
    	if(isset($_SESSION['client_viewed_exercises']) && !empty($_SESSION['client_viewed_exercises'])) {
    		//set array to the session array:
    		$client_viewed_exercises = $_SESSION['client_viewed_exercises'];
    		//check of this exercise id already exists in the array, if not add it and increment the views
    		if(!in_array($id, $client_viewed_exercises)) {
    			$exercise->views += 1;
    			$exercise->save();
    			array_push($client_viewed_exercises, $id);
    			$_SESSION['client_viewed_exercises'] = $client_viewed_exercises;
    		}
    	}
    	else {
    		//if the session does not exist yet, the exercise hasn't been watched, zo add it to the array, set the session and increment the views
    		$exercise->views += 1;
    		$exercise->save();
    		array_push($client_viewed_exercises, $id);
    		$_SESSION['client_viewed_exercises'] = $client_viewed_exercises;
    	}
    	if($exercise->approved || $is_headtrainer) {
    		return view('exercises/exercise_details', ['exercise' => $exercise, 'video_thumbnail' => $video_thumbnail]);
    	}
    	else {
    		abort(404);
    	}
    }

    /**
     *
     * Return exercises that are tagged with the checked tags
     *
     * @param       [request]   contains the checked tags
     * @return      [array]     exercises, pagination html
     *
     */
    public function get_filtered_exercises(Request $request) {
    	$tag_ids = json_decode($request->tag_ids);
    	
		$filtered_exercises = Exercise::where('approved', 1)->with('images');
		foreach ($tag_ids as $tag_id) {
			$filtered_exercises = $filtered_exercises->whereHas('tags', function ($query) use ($tag_id) {
				$query->where('tags.id', $tag_id);
			});
		}
		$filtered_exercises = $filtered_exercises->paginate(12);
		$pagination_html = (string)$filtered_exercises->links();
		return ['filtered_exercises' => $filtered_exercises, 'pagination_html' => $pagination_html];
    }

    //return the add_exercise view
    public function add_exercise() {
		$tag_types = Tag::all()->groupBy('type');
    	return view('exercises/add_exercise', ['tag_types' => $tag_types]);
    }

    //create the exercise
    public function create_exercise(Request $request) {
    	$validator = Validator::make($request->all(), [
    							'title'			=> 'required|string',
    							'description'	=> 'required|max:1000',
    							'tags'			=> 'required|array|min:1',
    							'image'			=> 'required|array|min:1',
    							'image.*'		=> 'max:500',
    							'video_url'		=> $request->deadline != null ? 'url' : '',
    		]);

        $valid_url = true;
        if($request->video_url) {
            //if a video url was passed, check if it was a youtube or vimeo url, else it is not valid
            $valid_url = false;
            if (strpos($request->video_url, 'youtu') || strpos($request->video_url, 'vimeo')) {
                $valid_url = true;
            }
        }
        //check if there are no scripts in the wysiwyg editor
        $safe_description = true;
        if (strpos($request->description, '<script') !== false || strpos($request->description, '<?php') !== false) {
            $safe_description = false;
            Input::replace(['description' => '']);
        }

        $validator->after(function ($validator) use ($valid_url, $safe_description) {
            if (!$valid_url) {
                $validator->errors()->add('video_url', 'Je kan alleen een YouTube of Vimeo url ingeven.');
            }
            if (!$safe_description) {
                $validator->errors()->add('description', 'Je mag geen scripts invoeren in de beschrijving!');
            }
        });
        if ($validator->fails()) {
            return redirect('add_exercise')
                        ->withErrors($validator)
                        ->withInput();
        }

    	//check whether it was a youtube or vimeo url
    	if($request->video_url) {
    		$media = '';
    		$video_url = $request->video_url;
    		if (strpos($video_url, 'youtu')) {
			    $media = 'youtube';
			    //make sure to use youtube instead of youtu
			    $new_video_url = str_replace('youtu.be', 'youtube.com', $video_url);
			    //replace watch by embed
			    $new_video_url = str_replace('watch?v=', 'embed/', $new_video_url);
			    //dump($new_video_url);
			    if(strpos($new_video_url, "&")) {
			    	//remove everything after '&'
				    $new_video_url = substr($new_video_url, 0, strpos($new_video_url, "&"));
				    dump($new_video_url);
			    }
			    
			}
			elseif(strpos($video_url, 'vimeo')){
				$media = 'vimeo';
				if(!strpos($video_url, 'player')) {
					//replace url so it matches the embed link
					$new_video_url = str_replace('vimeo.com', 'player.vimeo.com/video', $video_url);
				}
				if(strpos($new_video_url, "&")) {
			    	//remove everything after '&'
				    $new_video_url = substr($new_video_url, 0, strpos($new_video_url, "&"));
			    }
			}
    	}

        $exercise = new Exercise([
            'name'         	=> $request->title,
            'description'  	=> $request->description,
            'video_url'		=> $new_video_url,
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
    }

    //return edit_exercise view
    public function edit_exercise($id) {
    	$exercise = Exercise::find($id);
		$tag_types = Tag::all()->groupBy('type');
    	return view('exercises/edit_exercise', ['exercise' => $exercise, 'tag_types' => $tag_types]);
    }

    //update the exercise
    public function update_exercise(Request $request) {
    	$this->validate($request, [
    							'title'			=> 'required|string',
    							'description'	=> 'required|max:1000',
    							'tags'			=> 'required|array|min:1',
    							'name_and_size'	=> 'required|array|min:1',
    							'image.*'		=>	'max:500'
    		]);

    	$exercise = Exercise::find($request->exercise_id);
    	$exercise->name = $request->title;
    	$exercise->description = $request->description;
    	$exercise->save();

        //tags
		//detach all tags and attach the updated ones
		$exercise->tags()->detach();
        foreach ($request->tags as $tag) {
        	$exercise->tags()->attach($tag);
        }
        //images
        $existing_images = $request->existing_images;
        if(!$existing_images) {
        	$existing_images = array();
        }
        foreach ($exercise->images as $image) {
    		if(!in_array($image->id, $existing_images)) {
    			//if an attached image is no longer in the existing images, delete it
    			$image->delete();
    		}
    	}

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
        return redirect('edit_exercise/' . $exercise->id)->with('success_msg', 'Je hebt de oefening bijgewerkt.');
    }

    //delete the passed exercise
    public function delete_exercise($id) {
    	$exercise = Exercise::find($id);
    	//remove detach all tags
    	$exercise->tags()->detach();
    	//soft delete the exercise
    	$exercise->delete();

		return redirect('exercises_overview')->with('success_msg', 'De oefening werd verwijderd.');
    }

    //deny (disapprove) the passed exercise
    public function deny_exercise($id) {
    	//only if authenticated user is headtrainer
    	if(Auth::user()->isHeadtrainer()) {
    		//update status to 10, which means denied
    		$exercise = Exercise::find($id);
    		$exercise->approved = 10;
    		$exercise->save();
    		//soft delete the exercise
    		$exercise->delete();
    	}
    	return redirect('exercises_overview');
    }

    //approve the passed exercise
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
