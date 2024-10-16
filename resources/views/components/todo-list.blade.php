@if(!$todos->isEmpty())
    @foreach($todos as $todo)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo->id }}" data-order="{{ $todo->ordering }}">

    @can('todo_delete')
                    <form class="small pull-right" action="{{ route('frontend.todos.destroy', $todo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;" class="pull-right" data-id="{{ $todo->id }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button class="trash" type="submit" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                    </form>
                    @endcan  


    @php
        $date = $todo->due_date;
        $status = $todo->status;
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
        $dt = Carbon\Carbon::now();
    @endphp

    <i class="fas @if (!$todo->priority) fa-circle @else  fa-flag  @endif text-muted" style="color:@if (!$todo->priority)  {{ $color }}!important; @else red!important; @endif"></i> <a href="/todos/{{$todo->id}}" data-toggle="modal" data-target="#taskModal{{ $todo->id }}">{{ \Illuminate\Support\Str::words($todo->item, 5) }} <div class="small px-3">
        @if($dt->diffInDays($todo->due_date) == 0)
            Due soon
        @else
            Due in {{ $dt->diffInDays($todo->due_date) }} days
        @endif
    </div></a>
    
    </div>

    @endforeach
    @else

    @endif

        <!--Text box for adding a new task-->
        <form action="{{ route('frontend.todos.store') }}" method="POST" class="mt-4" id="add-todo-form">
        <div class="input-group">
        <input type="text" name="item" class="form-control col-md-12" placeholder="Add a new task" required>
     <div class="input-group-append">
                <button type="submit" class="btn btn-primary btn-sm">Add</button>
            </div> </div>
            @csrf
            <div class="input-group mt-3">  
            <input type="date" name="due_date" value="" class="form-control date" required>
            <input type="time" name="time_due" value="{{ now()->format('H:i:s') }}" class="form-control time" required>
            <input type="hidden" name="completed" value="0">
            <input type="hidden" name="session_id" value="{{ Request::segment(3) }}">
          
        </div>
        <div class="input-group mt-3">
            <textarea name="note" id="note" class="form-control" placeholder="Your notes here" required></textarea>
        </div>
    </form>

    <script>
/* Write javascript for when the delete button is pressed, delete the item and hide the div */
$(function(){
    $('.trash').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.ajax({
            url: `/todos/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $(this).closest('.todo-item').fadeOut();
            },
            error: function(xhr, status, error) {
                alert('Error deleting todo. Please try again.');
                //console.error('Error deleting todo:', xhr.responseText);
            }
        });
    });
});
    </script>
