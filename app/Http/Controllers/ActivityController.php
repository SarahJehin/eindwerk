<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Activity;

class ActivityController extends Controller
{
    public function add_activity() {
        $categories = Category::all();
        //dd($categories);
        return view('activities/add_activity', ['categories' => $categories]);
    }
}
