<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Session;
use App\Models\Todo;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('todo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $todos = Todo::with(['assigned_tos', 'session'])->get();

        return view('frontend.todos.index', compact('todos'));
    }

    public function create()
    {
        abort_if(Gate::denies('todo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assigned_tos = User::pluck('email', 'id');

        $sessions = Session::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.todos.create', compact('assigned_tos', 'sessions'));
    }

    public function store(StoreTodoRequest $request)
    {
        $todo = Todo::create($request->all());
        $todo->assigned_tos()->sync($request->input('assigned_tos', []));

        return redirect()->route('frontend.todos.index');
    }

    public function edit(Todo $todo)
    {
        abort_if(Gate::denies('todo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assigned_tos = User::pluck('email', 'id');

        $sessions = Session::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $todo->load('assigned_tos', 'session');

        return view('frontend.todos.edit', compact('assigned_tos', 'sessions', 'todo'));
    }

    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo->update($request->all());
        $todo->assigned_tos()->sync($request->input('assigned_tos', []));

        return redirect()->route('frontend.todos.index');
    }

    public function show(Todo $todo)
    {
        abort_if(Gate::denies('todo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $todo->load('assigned_tos', 'session');

        return view('frontend.todos.show', compact('todo'));
    }

    public function destroy(Todo $todo)
    {
        abort_if(Gate::denies('todo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $todo->delete();

        return back();
    }

    public function massDestroy(MassDestroyTodoRequest $request)
    {
        $todos = Todo::find(request('ids'));

        foreach ($todos as $todo) {
            $todo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /*
    Write a function that colors a fa-icon based on the date and 
    #status of the todo. If the todo is overdue, the icon should be red. 
    #If the todo is due today, the icon should be yellow. If the todo is due in the future, 
    #the icon should be green. 
    */

    public function colorIcon($date, $status)
    {
        $color = '';
        if ($date < now()->addDays(5) && $status == 0) {
            $color = 'gold';
        } elseif ($date == now()->addDays(3) && $status == 0) {
            $color = 'gold';
        } elseif ($date == now() && $status == 0) {
            $color = 'red';
        } elseif ($date > now()->subDays(7) && $status == 0) {
            $color = 'green';
        } elseif ($date > now()->addDays(15) && $status == 0) {
            $color = 'green';
        } elseif ($date < now() && $status == 0) {
            $color = 'red';
        }
        return $color;
    }
}
