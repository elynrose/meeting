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
    
    
    
//Recording and transmitting audio data from the browser to the server using JavaScript
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
            
            $.ajax({
                url: 'session/upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.log("Upload error:", xhr.responseText);
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
}
});