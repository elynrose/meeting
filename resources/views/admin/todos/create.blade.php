@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.todo.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.todos.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="item">{{ trans('cruds.todo.fields.item') }}</label>
                <input class="form-control {{ $errors->has('item') ? 'is-invalid' : '' }}" type="text" name="item" id="item" value="{{ old('item', '') }}">
                @if($errors->has('item'))
                    <div class="invalid-feedback">
                        {{ $errors->first('item') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.item_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.todo.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note') }}</textarea>
                @if($errors->has('note'))
                    <div class="invalid-feedback">
                        {{ $errors->first('note') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="due_date">{{ trans('cruds.todo.fields.due_date') }}</label>
                <input class="form-control date {{ $errors->has('due_date') ? 'is-invalid' : '' }}" type="text" name="due_date" id="due_date" value="{{ old('due_date') }}">
                @if($errors->has('due_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('due_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.due_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="time_due">{{ trans('cruds.todo.fields.time_due') }}</label>
                <input class="form-control timepicker {{ $errors->has('time_due') ? 'is-invalid' : '' }}" type="text" name="time_due" id="time_due" value="{{ old('time_due') }}">
                @if($errors->has('time_due'))
                    <div class="invalid-feedback">
                        {{ $errors->first('time_due') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.time_due_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('send_reminder') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="send_reminder" value="0">
                    <input class="form-check-input" type="checkbox" name="send_reminder" id="send_reminder" value="1" {{ old('send_reminder', 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="send_reminder">{{ trans('cruds.todo.fields.send_reminder') }}</label>
                </div>
                @if($errors->has('send_reminder'))
                    <div class="invalid-feedback">
                        {{ $errors->first('send_reminder') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.send_reminder_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="assigned_tos">{{ trans('cruds.todo.fields.assigned_to') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('assigned_tos') ? 'is-invalid' : '' }}" name="assigned_tos[]" id="assigned_tos" multiple>
                    @foreach($assigned_tos as $id => $assigned_to)
                        <option value="{{ $id }}" {{ in_array($id, old('assigned_tos', [])) ? 'selected' : '' }}>{{ $assigned_to }}</option>
                    @endforeach
                </select>
                @if($errors->has('assigned_tos'))
                    <div class="invalid-feedback">
                        {{ $errors->first('assigned_tos') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.assigned_to_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="completed">{{ trans('cruds.todo.fields.completed') }}</label>
                <input class="form-control {{ $errors->has('completed') ? 'is-invalid' : '' }}" type="number" name="completed" id="completed" value="{{ old('completed', '0') }}" step="1">
                @if($errors->has('completed'))
                    <div class="invalid-feedback">
                        {{ $errors->first('completed') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.completed_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="session_id">{{ trans('cruds.todo.fields.session') }}</label>
                <select class="form-control select2 {{ $errors->has('session') ? 'is-invalid' : '' }}" name="session_id" id="session_id" required>
                    @foreach($sessions as $id => $entry)
                        <option value="{{ $id }}" {{ old('session_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('session'))
                    <div class="invalid-feedback">
                        {{ $errors->first('session') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.todo.fields.session_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection