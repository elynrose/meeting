<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Session;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;

class HomeController
{
    public function index()
    {
        $sessions = Session::where('user_id', auth()->id())->get()->map(function ($session) {
            $session->todos_total = Todo::where('session_id', $session->id)->whereHas('assigned_tos', function ($query) {
                $query->where('id', auth()->id());
            })->count();
            $session->todos_pending= Todo::where('session_id', $session->id)->where('completed', 0)->whereHas('assigned_tos', function ($query) {
                $query->where('id', auth()->id());
            })->count();
            $session->todos_completed = Todo::where('session_id', $session->id)->where('completed', 1)->whereHas('assigned_tos', function ($query) {
                $query->where('id', auth()->id());
            })->count();
            return $session;
            });
        
        $totalTodos = $sessions->where('user_id', Auth::user()->id)->sum('todos_total');
        $performance = $totalTodos > 0 ? ($sessions->where('user_id', Auth::user()->id)->sum('todos_completed') / $totalTodos) * 100 : 0;

        //->whereHas('assigned_tos', function ($query) { $query->where('id', auth()->id());})

        if($sessions->count() > 0){

        $assigned_count = Todo::whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->count();

        
        $assigned = Todo::whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->get();
        
        return view('frontend.home', compact('sessions', 'performance', 'assigned', 'assigned_count'));

      } else {
        $users = User::all();
        return view('frontend.sessions.create', compact('users'));
      }
    }
}
