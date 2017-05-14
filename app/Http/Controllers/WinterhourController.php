<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winterhour;

class WinterhourController extends Controller
{
    //

    public function get_winterhours_overview() {
    	$winterhour_groups = Winterhour::all();
    	return view('winterhours/winterhours_overview', ['winterhour_groups' => $winterhour_groups]);
    }

    public function add_winterhour() {
    	return view('winterhours/add_winterhour');
    }
}
