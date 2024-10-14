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
        $sessions = Session::whereHas('todos.assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->with(['todos' => function ($query) {
            $query->whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
            })->orderBy('due_date', 'asc');
        }])->get()->map(function ($session) {
            $session->todos_total = $session->todos->count();
            $session->todos_pending = $session->todos->where('completed', 0)->count();
            $session->todos_completed = $session->todos->where('completed', 1)->count();
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
