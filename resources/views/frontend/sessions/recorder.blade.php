@extends('layouts.frontend')
<div class="container-fluid">
@section('content')
<div class="container">
@if(session('status'))
    <div class="alert alert-success" role="alert">
     {{ session('status') }}
     </div>
@endif

<h2 class="mb-4">{{ $session->name }}</h2>



@component('components.assigned-to', ['assigned_tos' => $assigned_tos, 'session' => $session])
@endcomponent

@component('components.audio-recorder', ['audio_url' => $audio_url])
@endcomponent

@component('components.summary-notes', ['session' => $session])
@endcomponent

<!-- Pending To-Do List Card -->
<div class="col-md-6" id="pending">
<div class="card">
<div class="card-header">
Pending
</div>
<div class="card-body todo-list">
<div class="row">
    <div class="col-md-9 col-sm-8">
    <a id="tasker" href="/create-todo-list/{{ $session->id }}"><i class="fas fa-plus"></i> Suggest Tasks</a>
    </div>
    <div class="col-md-3 col-sm-4">
      <label for="clear_all"> <input type="checkbox" id="clear_all" name="clear_all"> Clear All </label>
    </div>
</div>
<hr>
  @component('components.todo-list', ['todos' => $todos])
  @endcomponent
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
   @component('components.todo-list-completed', ['todo_completeds' => $todo_completeds])
   @endcomponent
</div>
</div>
</div>


<div class="col-md-12">
    @if($recordings)
    <div class="card-header">History</div>

   
    <div class="card">
   
    <div class="card-body" style="background:black; color:white; font-family:arial; height:200pz; overflow-y:scroll;font-size:1em;">  
        @foreach($recordings as $recording)
    <p><span class="text-muted">{{ $recording->created_at->diffForHumans() }}</span>:  {{ $recording->summary }}</p>    
   @endforeach  </div>     
  
    </div>

</div>

@endif
</div>


</div>


@component('components.todo-modal', ['todos' => $todos, 'assigned_tos' => $assigned_tos])
@endcomponent

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
    //location.reload();
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

    // If #clear_all is checked, ask user if they want to delete all the tasks
    $('#clear_all').on('change', function() {
        if ($(this).is(':checked')) {
            const userConfirmed = confirm('Are you sure you want to delete all tasks?');
            if (userConfirmed) {
                const taskIds = [];
                $('.todo-item').each(function() {
                    taskIds.push($(this).data('id'));
                });

                $.ajax({
                    url: '{{ route("frontend.todos.deleteAll") }}',
                    method: 'POST',
                    data: {
                        ids: taskIds,
                        _token: '{{ csrf_token() }}' // Laravel CSRF protection
                    },
                    success: function(response) {
                        console.log(response);
                        alert('All tasks deleted successfully');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting tasks');
                        console.log('Error deleting tasks' + xhr.responseText);
                    }
                });
            } else {
                $(this).prop('checked', false);
            }
        }
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
    $('#tasker').html('<i class="fas fa-spinner fa-spin"></i> Suggesting Tasks...');
    const sessionId = {{ Request::segment(3) }};
    $.ajax({
        url: `/create-todo-list/${sessionId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
        //Append each of the new todos items to the list
        
        //Loop through the todos and append each to the list
        $('#pending .todo-list').empty();
      /*  response.todo.forEach(function(todo) {
            const todoItem = `
                 <div class="todo-item ui-sortable-handle" data-id="${ todo.id }">
                 <i class="fas fa-circle text-muted" style="color:${todo.color}!important;"></i> <a href="/todos/${ todo.id }" data-toggle="modal" data-target="#taskModal${ todo.id }">${ todo.item }
                  <div class="small px-3">${ todo.due_date }</div></a>
                </div>
                `;
          
            $('#pending .todo-list').append(todoItem);
        });
      
        $('#tasker').html('<i class="fas fa-plus"></i> Suggest Tasks');
        */
        location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error creating to-do list:', xhr.responseText);
        }
    });
});
</script>

<script>
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


//Write js that submits any form with class autopost when its submit button is clicked, use ajax to submit the form without refreshing the page.
$('.autopost .alert').hide();
$('.autopost').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');
    const formData = new FormData(form[0]);
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('.autopost .alert').removeClass('alert-success').text('');
            $('.autopost .alert').addClass('alert-success').text('Form submitted successfully.').show();
           // location.reload()
        },
        error: function(xhr, status, error) {
            $('.autopost .alert').addClass('alert-danger').text('Error submitting form:', xhr.responseText).show();
            console.error('Error submitting form:', xhr.responseText);
        }
    });
});

//when the edit-todo link is clicked, show the form and hide the paragraph
//hide the form first
$('.modal-editor-form').hide();
$('.edit-todo').on('click', function(e) {
    e.preventDefault();
    const todoId = $(this).attr('id');
    $('#modal-editor-a-' + todoId).hide();
    $('#modal-editor-b-' + todoId).show();
});



    </script>

<script>
let mediaRecorder;
let audioChunks = [];
let isPaused = false;
let stream = null;
let recordedTime = 0;
let maxRecordingTime = {{ $credits * env('COST_PER_SECOND') }} * 1000; // 60 seconds in milliseconds
let countdownInterval = null;
let remainingTime = maxRecordingTime / 1000; // Initial remaining time in seconds

const recordButton = document.getElementById('recordButton');
const pauseButton = document.getElementById('pauseButton');
const stopButton = document.getElementById('stopButton');
const statusText = document.getElementById('status');
const audioPlayer = document.getElementById('audioPlayer');
const uploadButton = document.getElementById('uploadButton');

// Check if previous recording exists and start recording
recordButton.addEventListener('click', async () => {
    const sessionId = {{ Request::segment(3) }};
    $.ajax({
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        url: `/check-session-status/${sessionId}`,
        type: 'POST',
        success: async function(response) {
            if (response.status !== 'New') {
                const userConfirmed = confirm('A previous recording exists. Do you want to erase it and start over?');
                if (!userConfirmed) {
                    statusText.textContent = 'Recording cancelled. Previous recording retained.';
                    return;
                } else {
                    statusText.textContent = 'Previous recording erased. Starting new recording...';
                }
            } else {
                statusText.textContent = 'Starting new recording...';
            }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                startRecording();
            } catch (error) {
                console.error('Error accessing microphone:', error);
                statusText.textContent = 'Error accessing microphone.';
            }
        },
        error: function(xhr) {
            console.error('Error checking session status:', xhr.responseText);
            statusText.textContent = 'Error checking session status.';
        }
    });
});

// Start recording and countdown
function startRecording() {
    mediaRecorder = new MediaRecorder(stream);
    audioChunks = [];
    remainingTime = maxRecordingTime / 1000;

    mediaRecorder.start();
    statusText.textContent = `Recording... ${remainingTime} seconds remaining`;
    toggleButtons(true);

    startCountdown();

    mediaRecorder.addEventListener('dataavailable', event => audioChunks.push(event.data));

    mediaRecorder.addEventListener('stop', () => {
        clearInterval(countdownInterval);
        const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
        playRecordedAudio(audioBlob);
        showUploadButton(audioBlob, recordedTime); // Ensure upload button is shown after stop
    });
}

// Countdown function
function startCountdown() {
    countdownInterval = setInterval(() => {
        recordedTime = maxRecordingTime / 1000 - remainingTime;
        updateProgressBar();

        if (!isPaused) {
            remainingTime--;
            statusText.textContent = `Recording... ${remainingTime} seconds remaining`;

            if (remainingTime <= 0) {
                clearInterval(countdownInterval);
                mediaRecorder.stop();
                statusText.textContent = 'Max recording time reached.';
            }
        }
    }, 1000);
}

// Pause or resume recording
pauseButton.addEventListener('click', () => {
    if (isPaused) {
        mediaRecorder.resume();
        startCountdown();
        statusText.textContent = `Recording resumed... ${remainingTime} seconds remaining`;
        pauseButton.textContent = 'Pause';
    } else {
        mediaRecorder.pause();
        clearInterval(countdownInterval);
        statusText.textContent = 'Recording paused...';
        pauseButton.textContent = 'Resume';
    }
    isPaused = !isPaused;
});

// Stop recording
stopButton.addEventListener('click', () => {
    clearInterval(countdownInterval);
    mediaRecorder.stop();
    statusText.textContent = 'Recording stopped.';
    toggleButtons(false);
});

// Play recorded audio
function playRecordedAudio(audioBlob) {
    const audioUrl = URL.createObjectURL(audioBlob);
    audioPlayer.src = audioUrl;
    audioPlayer.style.display = 'block';
    audioPlayer.load();
    statusText.textContent = 'Recording completed. Review the audio.';
}

// Function to show the upload button and enable audio upload
function showUploadButton(audioBlob, recordedTime) {
    console.log('Audio Blob is ready:', {
        type: audioBlob.type,
        size: audioBlob.size
    });

    uploadButton.style.display = 'inline-block'; // Show the upload button
    uploadButton.disabled = false; // Ensure the button is enabled

    // Store the audioBlob and recordedTime globally so that it can be accessed by the upload event listener
    uploadButton.audioBlob = audioBlob;
    uploadButton.recordedTime = recordedTime;

    console.log('Upload button displayed and ready for interaction.');
}

// Add the event listener here to ensure it only gets added once
uploadButton.addEventListener('click', () => {
    const audioBlob = uploadButton.audioBlob;
    const recordedTime = uploadButton.recordedTime;

    if (!audioBlob) {
        statusText.textContent = 'No recording available to upload.';
        return;
    }

    const userConfirmed = confirm(`Do you want to upload the recording? (Recorded time: ${recordedTime} seconds)`);
    if (userConfirmed) {
        statusText.textContent = 'Uploading now...';
        uploadAudio(audioBlob); // Upload the recorded audio
    } else {
        statusText.textContent = 'Recording not uploaded.';
    }
});

//Add a method that updates a bootstrap progress bar to show the time left for recording
function updateProgressBar() {
    const progressBar = document.getElementById('time_left');
    const progress = (remainingTime / (maxRecordingTime / 1000)) * 100;
    progressBar.style.width = `${progress}%`;
}

// Upload the recorded audio
function uploadAudio(audioBlob) {
    const formData = new FormData();
    const sessionId = {{ Request::segment(3) }};
    formData.append('audio', audioBlob, 'audio_recording.wav');
    formData.append('id', sessionId);
    // Add seconds recorded to the form data
    formData.append('recorded_time', recordedTime);
    // Add max time allowed to the form data
    formData.append('max_time', maxRecordingTime / 1000);

    $.ajax({
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        url: '{{ route("frontend.session.upload") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            console.log(data);
            //parse the response and display the message
            location.reload();
        },
        error: function(xhr) {
            console.error('Upload failed:', xhr.responseText);
            statusText.textContent = 'Error uploading audio.';
        }
    });
}

// Toggle buttons (enable/disable)
function toggleButtons(isRecording) {
    recordButton.disabled = isRecording;
    pauseButton.disabled = !isRecording;
    stopButton.disabled = !isRecording;
}

</script>



@endsection