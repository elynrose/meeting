<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Session;

class HomeController
{
    public function index()
    {
        $sessions = Session::all();
        return view('frontend.home', compact('sessions'));
    }
}
