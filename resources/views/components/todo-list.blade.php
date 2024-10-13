@if(!$todos->isEmpty())
    @foreach($todos as $todo)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo->id }}">
    <i class="fas fa-grip-vertical text-muted"></i> <a href="/todos/{{$todo->id}}" data-toggle="modal" data-target="#taskModal{{ $todo->id }}">{{ \Illuminate\Support\Str::words($todo->item, 5) }}</a>
    
    @can('todo_delete')
    <form action="{{ route('frontend.todos.destroy', $todo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;" class="pull-right">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button class="trash" type="submit" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
    </form>
    @endcan
    </div>
    @endforeach
    @else

    @endif