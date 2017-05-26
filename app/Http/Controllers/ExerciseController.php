<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Tag;

class ExerciseController extends Controller
{
    public function exercises_overview() {
    	dd('exercises_overview');
    }

    public function add_exercise() {
    	//
    	//$tags = Tag::groupBy('type')->get();
    	//$tags = Tag::selectRaw('count(*) AS cnt, type')->groupBy('type')->orderBy('cnt', 'DESC')->limit(5)->get();
    	/*
    	$dates_by_month = $winterhour->dates->groupBy(function($item) {
		    return((new \DateTime($item->date))->format('Y-m'));
		});*/
		$tag_types = Tag::all()->groupBy('type');
    	//dd($tags);
    	return view('exercises/add_exercise', ['tag_types' => $tag_types]);
    }

    public function create_exercise(Request $request) {
    	dd($request);
    }
}
