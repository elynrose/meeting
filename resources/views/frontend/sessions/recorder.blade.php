@extends('layouts.frontend')

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
<div class="card-header">
Recorder
</div>
<div class="card-body text-center">
<button id="recordButton" data-id="{{ Request::segment(3) }}" class="btn btn-secondary">Record</button>
<button id="pauseButton" class="btn btn-secondary" disabled>Pause</button>
<button id="stopButton" class="btn btn-danger" disabled>Stop</button>
<p id="status" class="mt-3">Press "Record" to start recording.</p>
</div>
</div>
</div>

<!-- Summary Card -->
<div class="col-md-8">
<div class="card summary-text" id="summary">
<div class="card-header">
Summary
</div>
<div class="card-body">
<a href="#" id="readMore" class="read-more">Read more</a> | <a href="#">Edit</a>
<p id="summaryText">
Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.


</p>
</div>
</div>
</div>

<!-- Pending To-Do List Card -->
<div class="col-md-6" id="pending">
<div class="card">
<div class="card-header">
To-Do List (Pending)
</div>
<div class="card-body todo-list">
<div class="todo-item" data-id="1">
<a href="#" data-toggle="modal" data-target="#taskModal1">Sample task 1</a>
</div>
<div class="todo-item" data-id="2">
<a href="#" data-toggle="modal" data-target="#taskModal2">Sample task 2</a>
</div>
<div class="todo-item" data-id="3">
<a href="#" data-toggle="modal" data-target="#taskModal3">Sample task 3</a>
</div>
</div>
</div>
</div>

<!-- Completed To-Do List Card -->
<div class="col-md-6" id="completed">
<div class="card">
<div class="card-header">
To-Do List (Completed)
</div>
<div class="card-body todo-list">
<!-- Completed tasks will be moved here -->
</div>
</div>
</div>
</div>
</div>

<!-- Modal Template for Task 1 -->
<div class="modal fade" id="taskModal1" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel1" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="taskModalLabel1">Task 1</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<p>This is some detail about task 1.</p>
<form id="commentForm1">
<div class="form-group">
<label for="comment1">Add a comment:</label>
<textarea class="form-control" id="comment1" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>
</div>

<!-- Modal Template for Task 2 -->
<div class="modal fade" id="taskModal2" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel2" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="taskModalLabel2">Task 2</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<p>This is some detail about task 2.</p>
<form id="commentForm2">
<div class="form-group">
<label for="comment2">Add a comment:</label>
<textarea class="form-control" id="comment2" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>
</div>

<!-- Modal Template for Task 3 -->
<div class="modal fade" id="taskModal3" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel3" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="taskModalLabel3">Task 3</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<p>This is some detail about task 3.</p>
<form id="commentForm3">
<div class="form-group">
<label for="comment3">Add a comment:</label>
<textarea class="form-control" id="comment3" rows="3"></textarea>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
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
    const taskId = $(ui.item).data('id');
    const newStatus = $(this).closest('.col-md-6').attr('id') === 'completed' ? 'completed' : 'pending';
    // AJAX request to update task status
    $.ajax({
    url: '/update-task-status',
    method: 'POST',
    data: {
    id: taskId,
    status: newStatus,
    _token: '{{ csrf_token() }}' // Laravel CSRF protection
    },
    success: function(response) {
    console.log('Task status updated');
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
    
    // Toggle Read More / Read Less functionality
    document.getElementById('readMore').addEventListener('click', function(e) {
    e.preventDefault();
    const summaryText = document.getElementById('summary');
    const readMoreLink = document.getElementById('readMore');
    
    if (summaryText.classList.contains('expanded')) {
    summaryText.classList.remove('expanded');
    readMoreLink.textContent = 'Read more'
    } else {
    summaryText.classList.add('expanded');
    readMoreLink.textContent = 'Read less';
    }
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
            formData.append('audio', audioBlob, 'audio_recording.wav'); // Ensure the file has a name
            formData.append('id', {{Request::segment(3)}}); // Ensure the file has a name


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                url: '{{ route('frontend.session.upload') }}', // Use the correct route
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically transforming the data
                contentType: false, // Let the browser set the content type automatically
                success: function(response) {
                    console.log(response);
                    statusText.textContent = 'Audio uploaded successfully!';
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
@endsection