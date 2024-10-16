$(function() {
    // Initialize sortable functionality for todo lists
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

// Audio recording functionality
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

// Document ready function
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

    // Periodically check for updates every 30 seconds
    setInterval(checkForUpdates, 30000);
});

// Save notes functionality
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
                id: sessionId,
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

// Task suggestion functionality
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
            // Append each of the new todos items to the list
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error creating to-do list:', xhr.responseText);
        }
    });
});

// Update research status functionality
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

// Auto-submit form functionality
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
            location.reload();
        },
        error: function(xhr, status, error) {
            $('.autopost .alert').addClass('alert-danger').text('Error submitting form:', xhr.responseText).show();
            console.error('Error submitting form:', xhr.responseText);
        }
    });
});

// Edit todo functionality
$('.modal-editor-form').hide();
$('.edit-todo').on('click', function(e) {
    e.preventDefault();
    const todoId = $(this).attr('id');
    $('#modal-editor-a-' + todoId).hide();
    $('#modal-editor-b-' + todoId).show();
});

// Add CKEditor to the textarea with class ckeditor
ClassicEditor.create(document.querySelector('.ckeditor'))
    .catch(error => {
        console.error(error);
    });
