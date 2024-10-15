let mediaRecorder;
let audioChunks = [];
let isPaused = false;
let stream = null;
let recordedTime = 0;
let maxRecordingTime = 60 * 1000; // 60 seconds in milliseconds
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
        showUploadButton(audioBlob);
    });
}

// Countdown function
function startCountdown() {
    countdownInterval = setInterval(() => {
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
    statusText.textContent = 'Recording completed. Review the audio below.';
}

// Show upload button and handle upload
function showUploadButton(audioBlob) {
    uploadButton.style.display = 'inline-block';
    uploadButton.onclick = () => {
        const userConfirmed = confirm('Do you want to upload the recording?');
        if (userConfirmed) {
            uploadAudio(audioBlob);
        } else {
            statusText.textContent = 'Recording not uploaded.';
        }
    };
}

// Upload the recorded audio
function uploadAudio(audioBlob) {
    const formData = new FormData();
    const sessionId = {{ Request::segment(3) }};
    formData.append('audio', audioBlob, 'audio_recording.wav');
    formData.append('id', sessionId);

    $.ajax({
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        url: '{{ route("frontend.session.upload") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function() {
            statusText.textContent = 'Audio uploaded successfully!';
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
