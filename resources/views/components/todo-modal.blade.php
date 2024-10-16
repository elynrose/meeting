
@if(!$todos->isEmpty())
    @foreach($todos as $todo)
    <!-- Modal Template for Tasks -->
    <div class="modal fade" id="taskModal{{ $todo->id }}" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel{{ $todo->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $todo->id }}">{{ $todo->item ?? 'To-do title here' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body" id="modal-editor-a-{{$todo->id}}">
                   
                   

                        <p>{!! $todo->note ?? ''  !!}</p>
                        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($todo->due_date)->format('F j, Y') ?? '' }} @ {{ \Carbon\Carbon::parse($todo->time_due)->format('g:i A') ?? '' }}</p>
                        @if(!$todo->assigned_tos->isEmpty())
                        <p class="mt-3" id="assigned_tos{{ $todo->id }}"><strong>Assigned To:</strong>
                            @foreach($todo->assigned_tos as $id => $assigned)
                            <p class="badge badge-info">{{ $assigned->name }}</p>
                            @endforeach
                        @else
                            <p class="badge badge-info">No one assigned</p>
                        @endif
</p>
                        <div class="form-check form-check-inline">
                        <input class="form-check form-check-input" type="checkbox" id="research" data-id="{{ $todo->id }}" name="research" value="{{ $todo->research }}"  @if($todo->research) checked @else @endif>   
                        <span class="small text-muted mt-1">@if($todo->research==1 && empty($todo->research_result)) <i id="researching{{ $todo->id }}" class="fas fa-spinner fa-spin spin{{ $todo->id }}"></i><span id="research_text{{ $todo->id }}"> Working...</span> @elseif($todo->research==0 && empty($todo->research_result)) <span id="research_text{{ $todo->id }}"> Automate will attempt to do research on this topic.</span> @elseif($todo->research==1 && !empty($todo->research_result)) Your research has been completed  @elseif($todo->research==0 && !empty($todo->research_result)) The research has been completed. Click on the button below.  @endif</span>
                    </div>

                    @if(!empty($todo->research_result)) 
                    <div class="mt-3" ><a href="/pdf-download/{{ $todo->id }}" target="_blank" id="research_result{{ $todo->id }}" class="btn btn-xs btn-info research_result">Download Research</a></div>
                    @endif
                    </div>

                    <div class="modal-body modal-editor-form" id="modal-editor-b-{{$todo->id}}">
                        @component('components.todo-edit-form', ['todo' => $todo, 'assigned_tos' => $assigned_tos])
                        @endcomponent
                    </div>
                    <div class="modal-footer">
                    <p><a href="#" class="edit-todo px-2" id="{{ $todo->id }}"><i class="fas fa-edit"></i></a>
                    </p>
                    @can('todo_delete')
                    <form class="small" action="{{ route('frontend.todos.destroy', $todo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;" class="pull-right">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button class="trash" type="submit" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                    </form>
                    @endcan      
                              </div>
            </div>
        </div>
    </div>
    @endforeach

@endif
