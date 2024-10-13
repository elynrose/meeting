@if(!$todo_completeds->isEmpty())
    @foreach($todo_completeds as $todo_completed)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo_completed->id }}">
    <i class="fas fa-grip-vertical"></i> <a href="/todos/{{$todo_completed->id}}">{{ \Illuminate\Support\Str::words($todo_completed->item, 5) }}</a>
    
    @can('todo_delete')
    <form action="{{ route('frontend.todos.destroy', $todo_completed->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;" class="pull-right">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button class="trash" type="submit" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
    </form>
    @endcan
    </div>
    @endforeach
    @else

    @endif