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
