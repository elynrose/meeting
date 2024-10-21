<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Session;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;
use LaravelDaily\LaravelCharts\Classes\LaravelTodoChart;


class HomeController
{
    public function index()
    {
        $sessions = Session::whereHas('todos.assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->get()->map(function ($session) {
            $session->todos_total = Todo::where('session_id', $session->id)->whereNull('deleted_at')->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count();

            $session->todos_pending = Todo::where('session_id', $session->id)->where('completed', 0)->whereNull('deleted_at')->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count();

            $session->todos_completed = Todo::where('session_id', $session->id)->where('completed', 1)->whereNull('deleted_at')->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count();

            return $session;
        });

        $performance = [
            'total_todos' => Todo::whereNull('deleted_at')->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count(),
            
            'total_completed' => Todo::where('completed', 1)->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count(),

            'total_pending' => Todo::where('completed', 0)->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->count(),
        ];


        if($sessions->count() > 0){

        $assigned_count = Todo::whereIn('session_id', $sessions->pluck('id'))->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();

        
        $assigned = Todo::where(function ($query) {
            $query->whereHas('assigned_tos', function ($query) {
            $query->where('user_id', auth()->id());
            })->orWhereHas('session', function ($query) {
            $query->where('user_id', auth()->id());
            });
        })->get();


        $settings8 = [
            'chart_title'           => 'Todos Completed',
            'chart_type'            => 'line',
            'report_type'           => 'group_by_date',
            'model'                 => 'App\Models\Todo',
            'group_by_field'        => 'created_at',
            'group_by_period'       => 'day',
            'aggregate_function'    => 'sum',
            'aggregate_field'       => 'completed',
            'filter_field'          => 'created_at',
            'filter_days'           => '30',
            'group_by_field_format' => 'Y-m-d H:i:s',
            'column_class'          => 'col-md-12',
            'entries_number'        => '5',
            'translation_key'       => 'todos',
        ];

        $chart8 = new LaravelTodoChart($settings8);

        
        return view('frontend.home', compact('sessions', 'performance', 'assigned', 'assigned_count', 'chart8'));

      } else {
        $users = User::all();
        return view('frontend.sessions.create', compact('users'));
      }
    }
}