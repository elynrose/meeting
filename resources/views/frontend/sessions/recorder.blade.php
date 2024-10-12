@extends('layouts.frontend')
<div class="container">
@section('content')
<div class="container">
@if(session('status'))
    <div class="alert alert-success" role="alert">
     {{ session('status') }}
     </div>
@endif

<h2 class="mb-4">{{ $session->name }}</h2>


<div class="row px-3">
<ul id="thumbnail" class="small text-muted">
<li>
<img src="https://via.placeholder.com/40" width="10" alt="User 1">
<span>Jack Daw</span>
</li>
<li>
<img src="https://via.placeholder.com/40" width="10" alt="User 2">
<span>Milan Pablo</span>
</li>
<li>
<img src="https://via.placeholder.com/40" width="10" alt="User 3">
<span>Mike Sweat</span>
</li>
</ul>
</div>
<div class="row">
<!-- Recorder Card -->
<div class="col-md-4">
<div class="card">

<div class="card-body text-center">
    <div class="mt-4">
<button id="recordButton" data-id="{{ Request::segment(3) }}" class="btn btn-secondary btn-sm">Record</button>
<button id="pauseButton" class="btn btn-secondary btn-sm" disabled>Pause</button>
<button id="stopButton" class="btn btn-danger btn-sm" disabled>Stop</button>
</div>
@if($audio_url)
<div class="mt-3"><audio id="audioPlayer" width="100%" src="{{ $audio_url }}" controls class="audioPlayer"></audio></div>
@endif
<p id="status" class="mt-3">{{ $session->status ?? 'Press "Record" to start recording.'}}</p>
</div>
</div>
</div>

<!-- Summary Card -->
<div class="col-md-8">
<div class="card summary-text" id="summary">
<div class="card-body">
<nav class="nav nav-pills nav-justified">
    <a class="nav-link" href="#"><i class="fas fa-edit"></i> Edit</a>
    <a class="nav-link" href="#" data-toggle="modal" data-target="#originalTextModal"><i class="fas fa-bullhorn"></i> Original Text</a>
    <a class="nav-link" href="#" data-toggle="modal" data-target="#summaryTextModal"><i class="fas fa-book"></i> Summary</a>
    <a class="nav-link" id="tasker" href="/create-todo-list/{{ $session->id }}"><i class="fas fa-list"></i> Create Todo List</a>

</nav>
<form id="commentForm3" class="shadow">
<div class="form-group" style="position:relative;">
    <span class="small text-muted" id="spinner-circle" style="position:absolute; left:10px; top:10px; z-index:999;"><i id="saving-notes" class="fas fa-spinner fa-spin"></i> Saving</span>
<textarea class="form-control" id="notes" rows="1" placeholder="Your notes here" style="padding:25px;">@if($session->notes){{ $session->notes  }}@endif
</textarea>
</div>
</form>
</div>
</div>

</div>


<!-- Pending To-Do List Card -->
<div class="col-md-6" id="pending">
<div class="card">
<div class="card-header">
Pending
</div>
<div class="card-body todo-list">
    @if(!$todos->isEmpty())
    @foreach($todos as $todo)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo->id }}">
    <i class="fas fa-grip-vertical"></i> <a href="#" data-toggle="modal" data-target="#taskModal{{ $todo->id }}">{{ $todo->item }}</a>
    
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
    <div class="todo-item" data-id="0">
    <i class="fas fa-grip-vertical"></i> <a href="#" data-toggle="modal" data-target="#taskModal0">Nothing to do</a>
    </div>
    @endif
</div>
</div>
</div>

<!-- Completed To-Do List Card -->
<div class="col-md-6" id="completed">
<div class="card">
<div class="card-header">
Completed
</div>
<div class="card-body todo-list">
    @if(!$todo_completeds->isEmpty())
    @foreach($todo_completeds as $todo_completed)
    <div class="todo-item ui-sortable-handle" data-id="{{ $todo_completed->id }}">
    <i class="fas fa-grip-vertical"></i> <a href="#" data-toggle="modal" data-target="#taskModal{{ $todo_completed->id }}">{{ $todo_completed->item }}</a>
    
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
</div>
</div>
</div>
</div>
</div>

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
                    <div class="modal-body">
                        <p>{{ $todo->note ?? ''}}</p>
                        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($todo->due_date)->format('F j, Y') ?? '' }} @ {{ \Carbon\Carbon::parse($todo->time_due)->format('g:i A') ?? '' }}</p>
                        <div class="form-check form-check-inline">
                        <input class="form-check form-check-input" type="checkbox" id="research" data-id="{{ $todo->id }}" name="research" value="{{ $todo->research }}"  @if($todo->research) checked @else @endif>   
                        <span class="small text-muted mt-1">@if($todo->research==1 && empty($todo->research_result)) <i id="researching{{ $todo->id }}" class="fas fa-spinner fa-spin spin{{ $todo->id }}"></i><span id="research_text{{ $todo->id }}"> Working...</span> @elseif($todo->research==0 && empty($todo->research_result)) <span id="research_text{{ $todo->id }}"> Automate will attempt to do research on this topic.</span> @elseif($todo->research==1 && !empty($todo->research_result)) Your research has been completed  @elseif($todo->research==0 && !empty($todo->research_result)) The research has been completed. Click on the button below.  @endif</span>
                    </div>
                    @if($todo->research==0 && !empty($todo->research_result)) 
                    <div class="mt-3" ><a href="/pdf-download/{{ $todo->id }}" target="_blank" id="research_result{{ $todo->id }}" class="btn btn-xs btn-info research_result">Download Research</a></div>
                    @endif
                    </div>
            </div>
        </div>
    </div>
    @endforeach

@endif


<!-- Modal Template for Summary -->
<div class="modal fade" id="summaryTextModal" tabindex="-1" role="dialog" aria-labelledby="summaryTextModal" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="summaryTextModalTitle">Summary</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body" id="summaryText">
<p id="summaryText" class="summaryDiv">
@if($session->summary)
{{ $session->summary }}
@else
No summary available.
@endif

</p>

</div>
</div>
</div>

   
</div>


<!-- Modal for Original Text -->
<div class="modal fade" id="originalTextModal" tabindex="-1" role="dialog" aria-labelledby="originalTextModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="originalTextModalLabel">Original Text</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="transcriptionText">{{ $session->transcription ?? 'No original text available.' }}</p>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
@section('scripts')
  @parent
  <script>
    // Re-enable Drag and Drop
$(function() {
    $("#pending .todo-list, #completed .todo-list").sortable({
    connectWith: ".todo-list", // Allow dragging between lists
    receive: function(event, ui) {
    const todoId = $(ui.item).data('id');
    const newStatus = $(this).closest('.col-md-6').attr('id') === 'completed' ? 'completed' : 'pending';
    // AJAX request to update task status
    $.ajax({
    url: '/update-todo-status',
    method: 'POST',
    data: {
    id: todoId,
    status: newStatus,
    _token: '{{ csrf_token() }}' // Laravel CSRF protection
    },
    success: function(response) {
    location.reload();
    },
    error: function() {
    console.error('Error updating task status');
    }
    });
    }
    }).disableSelection();
    });
    
    // AJAX form submission for each task's comment
    $('#commentForm1').on('submit', function(e) {
    e.preventDefault();
    const comment = $('#comment1').val();
    $.ajax({
    url: '/submit-comment',
    method: 'POST',
    data: {
    taskId: 1,
    comment: comment,
    _token: '{{ csrf_token() }}'
    },
    success: function(response) {
    alert('Comment added for task 1');
    },
    error: function() {
    alert('Error adding comment');
    }
    });
    });
    
    $('#commentForm2').on('submit', function(e) {
    e.preventDefault();
    const comment = $('#comment2').val();
    $.ajax({
    url: '/submit-comment',
    method: 'POST',
    data: {
    taskId: 2,
    comment: comment,
    _token: '{{ csrf_token() }}'
    },
    success: function(response) {
    alert('Comment added for task 2');
    },
    error: function() {
    alert('Error adding comment');
    }
    });
    });
    
    $('#commentForm3').on('submit', function(e) {
    e.preventDefault();
    const comment = $('#comment3').val();
    $.ajax({
    url: '/submit-comment',
    method: 'POST',
    data: {
    taskId: 3,
    comment: comment,
    _token: '{{ csrf_token() }}'
    },
    success: function(response) {
    alert('Comment added for task 3');
    },
    error: function() {
    alert('Error adding comment');
    }
    });
    });
    
    
    let mediaRecorder;
let audioChunks = [];

// Get the record, pause, and stop buttons
const recordButton = document.getElementById('recordButton');
const pauseButton = document.getElementById('pauseButton');
const stopButton = document.getElementById('stopButton');
const statusText = document.getElementById('status');
// When the "Record" button is clicked
recordButton.addEventListener('click', async () => {
    const sessionId = {{ Request::segment(3) }};
    
    // Check if the session status is "New"
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        url: `/check-session-status/${sessionId}`,
        type: 'POST',
        success: async function(response) {
            if (response.status !== 'New') {
                // Ask the user if they want to erase the previous recording and start over
                const userConfirmed = confirm('A previous recording exists. Do you want to erase it and start over?');
                if (!userConfirmed) {
                    return;
                }
            } else {
                statusText.textContent = 'Starting new recording...';
            }
            
            // Proceed with recording
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.start();
                statusText.textContent = 'Recording...';

                // Enable and disable the buttons as needed
                recordButton.disabled = true;
                pauseButton.disabled = false;
                stopButton.disabled = false;

                // Collect audio data chunks as they are available
                mediaRecorder.addEventListener('dataavailable', event => {
                    audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'audio_recording.wav');
                    formData.append('id', sessionId);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        url: '{{ route('frontend.session.upload') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response);
                            statusText.textContent = 'Processing...';
                        },
                        error: function(xhr, status, error) {
                            console.error('Upload error:', xhr.responseText);
                            statusText.textContent = 'Error uploading audio.';
                        }
                    });
                });
            } catch (error) {
                console.error('Error accessing microphone:', error);
                statusText.textContent = 'Error accessing microphone.';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking session status:', xhr.responseText);
            statusText.textContent = 'Error checking session status.';
        }
    });
});

// When the "Pause" button is clicked
pauseButton.addEventListener('click', () => {
    if (mediaRecorder.state === 'recording') {
        mediaRecorder.pause();
        statusText.textContent = 'Paused recording.';
        pauseButton.textContent = 'Resume';
    } else if (mediaRecorder.state === 'paused') {
        mediaRecorder.resume();
        statusText.textContent = 'Resumed recording.';
        pauseButton.textContent = 'Pause';
    }
});

// When the "Stop" button is clicked
stopButton.addEventListener('click', () => {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        statusText.textContent = 'Stopped recording. Uploading audio...';

        // Reset buttons
        recordButton.disabled = false;
        pauseButton.disabled = true;
        stopButton.disabled = true;
        pauseButton.textContent = 'Pause'; // Reset pause button text
    }
});
</script>
<script>
$(document).ready(function() {
    const statusText = document.getElementById('status');
    // Extract session ID from the URL
    const sessionId = {{ Request::segment(3) }};

    // Function to check for updates
    function checkForUpdates() {
        $.ajax({
            url: `/check-updates/${sessionId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {

                statusText.textContent = '';
                // Update the content of the divs if there are any changes
                if (data.transcription) {
                    $('#transcriptionText').text(data.transcription);
                }
                if (data.summary) {
                    $('#summaryText').text(data.summary);
                }
                console.log('Checked for updates:', data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching updates:', xhr.responseText);
            }
        });
    }

    // Periodically check for updates every 5 seconds
    setInterval(checkForUpdates, 30000);
});
</script>

<script>
//When the user types into #notes, wait for 5 seconds of inactivity before saving the notes
let timeoutId;
$('#spinner-circle').hide();
document.getElementById('notes').addEventListener('input', function() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() {
        const notes = document.getElementById('notes').value;
        $('#spinner-circle').show();
        $.ajax({
            url: '/save-notes',
            method: 'POST',
            data: {
                id: {{ Request::segment(3) }},
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#spinner-circle').hide();
            },
            error: function(xhr, status, error) {
                $('#spinner-circle').fadeOut().hide();
                alert('Error saving notes. Please try again.');
                //console.error('Error saving notes:', xhr.responseText);
            }
        });
    }, 5000);
});
</script>

<script>
/* when #tasker is clicked, send a jquery ajax post with the session id to  createToDoList route */
$('#tasker').on('click', function(e) {
    e.preventDefault();
    const sessionId = {{ Request::segment(3) }};
    $.ajax({
        url: `/create-todo-list/${sessionId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error creating to-do list:', xhr.responseText);
        }
    });
});
</script>

<script>
   // $('.research_result').hide();

/* When research is checked, send a jquery ajax request to update todo for the session, set research to 1 */
$('input[name="research"]').on('change', function() {
    const todoId = $(this).data('id');
    const isChecked = $(this).is(':checked');

    if(isChecked || !isChecked) {
        if (!confirm('Unchecking this will delete any previous research done on this topic. Are you sure you want to proceed?')) {
            $(this).prop('checked', true);
            return;
        }
    }
    $.ajax({
        url: '/update-todo-research',
        method: 'POST',
        data: {
            id: todoId,
            is_checked: isChecked,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (isChecked) {
                $('#researching' + todoId).show();
                $('.spin' + todoId).show();
                $('#research_text' + todoId).text(' Working...');
            } else {
                $('#researching' + todoId).hide();
                $('.spin' + todoId).hide();
                $('#research_text' + todoId).text('Automate will attempt to do research on this topic.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating research status:', xhr.responseText);
        }
    });
});

    </script>

@endsection