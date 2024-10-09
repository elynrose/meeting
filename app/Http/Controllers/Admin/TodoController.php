<?php

namespace App\Http\Controllers\Admin;

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

        return view('admin.todos.index', compact('todos'));
    }

    public function create()
    {
        abort_if(Gate::denies('todo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assigned_tos = User::pluck('email', 'id');

        $sessions = Session::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.todos.create', compact('assigned_tos', 'sessions'));
    }

    public function store(StoreTodoRequest $request)
    {
        $todo = Todo::create($request->all());
        $todo->assigned_tos()->sync($request->input('assigned_tos', []));

        return redirect()->route('admin.todos.index');
    }

    public function edit(Todo $todo)
    {
        abort_if(Gate::denies('todo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assigned_tos = User::pluck('email', 'id');

        $sessions = Session::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $todo->load('assigned_tos', 'session');

        return view('admin.todos.edit', compact('assigned_tos', 'sessions', 'todo'));
    }

    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo->update($request->all());
        $todo->assigned_tos()->sync($request->input('assigned_tos', []));

        return redirect()->route('admin.todos.index');
    }

    public function show(Todo $todo)
    {
        abort_if(Gate::denies('todo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $todo->load('assigned_tos', 'session');

        return view('admin.todos.show', compact('todo'));
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
}
