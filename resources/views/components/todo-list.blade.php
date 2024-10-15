@if(!$todos->isEmpty())
    @foreach($todos as $todo)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo->id }}">
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

    <i class="fas fa-circle text-muted" style="color:{{ $color }}!important;"></i> <a href="/todos/{{$todo->id}}" data-toggle="modal" data-target="#taskModal{{ $todo->id }}">{{ \Illuminate\Support\Str::words($todo->item, 5) }} <div class="small px-3">
        @if($dt->diffInDays($todo->due_date) == 0)
            Due in a few hours
        @else
            Due in {{ $dt->diffInDays($todo->due_date) }} days
        @endif
    </div></a>
    
    </div>
    @endforeach
    @else

    @endif