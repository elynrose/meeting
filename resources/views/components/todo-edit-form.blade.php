                        
                        <form method="POST" class="autopost" action="{{ route("frontend.todos.update", [$todo->id]) }}" enctype="multipart/form-data">
                            <div class="alert"></div>
                            @method('PUT')
                            @csrf
                            <div class="form-group">
                                <label for="item">{{ trans('cruds.todo.fields.item') }}</label>
                                <input class="form-control" type="text" name="item" id="item" value="{{ old('item', $todo->item) }}">
                                @if($errors->has('item'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('item') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.todo.fields.item_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="note">{{ trans('cruds.todo.fields.note') }}</label>
                                <textarea class="form-control ckeditor" name="note" id="note">{{ old('note', $todo->note) }}</textarea>
                                @if($errors->has('note'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('note') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.todo.fields.note_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="due_date">{{ trans('cruds.todo.fields.due_date') }}</label>
                                <input class="form-control date" type="text" name="due_date" id="due_date" value="{{ old('due_date', $todo->due_date) }}">
                                @if($errors->has('due_date'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('due_date') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.todo.fields.due_date_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="time_due">{{ trans('cruds.todo.fields.time_due') }}</label>
                                <input class="form-control timepicker" type="text" name="time_due" id="time_due" value="{{ old('time_due', $todo->time_due) }}">
                                @if($errors->has('time_due'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('time_due') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.todo.fields.time_due_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <div>
                                    <input type="hidden" name="send_reminder" value="0">
                                    <input type="checkbox" name="send_reminder" id="send_reminder" value="1" {{ $todo->send_reminder || old('send_reminder', 0) === 1 ? 'checked' : '' }}>
                                    <label for="send_reminder">{{ trans('cruds.todo.fields.send_reminder') }}</label>
                                </div>
                                @if($errors->has('send_reminder'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('send_reminder') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.todo.fields.send_reminder_helper') }}</span>
                            </div>
                            <div class="form-group">
                            <input type="hidden" name="assigned_tos[]" value="{{ auth()->user()->id }}">

                               
                                 <label for="assigned_tos">{{ trans('cruds.todo.fields.assigned_to') }}</label>
                                <div style="padding-bottom: 4px">
                                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                                </div>
                                <select class="form-control select2" name="assigned_tos[]" id="assigned_tos" multiple>
                                    @foreach($assigned_tos as $id => $assigned_to)
                                       
                                            <option value="{{ $id }}" {{ (in_array($id, old('assigned_tos', [])) || $todo->assigned_tos->contains($id)) ? 'selected' : '' }}>{{ $assigned_to }}</option>
                                       
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
                                <input type="hidden" name="session_id" value="{{ Request::segment(3) }}">
                                <button class="btn btn-danger" type="submit">
                                    {{ trans('global.save') }}
                                </button>
                            </div>
                        </form>
                    