@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.todo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.todos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.id') }}
                        </th>
                        <td>
                            {{ $todo->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.item') }}
                        </th>
                        <td>
                            {{ $todo->item }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.note') }}
                        </th>
                        <td>
                            {{ $todo->note }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.due_date') }}
                        </th>
                        <td>
                            {{ $todo->due_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.time_due') }}
                        </th>
                        <td>
                            {{ $todo->time_due }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.send_reminder') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $todo->send_reminder ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.assigned_to') }}
                        </th>
                        <td>
                            @foreach($todo->assigned_tos as $key => $assigned_to)
                                <span class="label label-info">{{ $assigned_to->email }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.completed') }}
                        </th>
                        <td>
                            {{ $todo->completed }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.todo.fields.session') }}
                        </th>
                        <td>
                            {{ $todo->session->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.todos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection