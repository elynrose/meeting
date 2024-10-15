@if(!$todo_completeds->isEmpty())
    @foreach($todo_completeds as $todo_completed)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo_completed->id }}">
    @php
        $date = $todo_completed->due_date;
        $status = $todo_completed->status;
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

    <i class="fas fa-circle text-muted" style="color:{{ $color }}!important;"></i> <a href="/todos/{{$todo_completed->id}}" data-toggle="modal" data-target="#taskModal{{ $todo_completed->id }}">{{ \Illuminate\Support\Str::words($todo_completed->item, 5) }}<div class="small px-3">Due in {{ $dt->diffInDays($todo_completed->due_date) }} days</div></a> 

    </div>
    @endforeach
    @else

    @endif