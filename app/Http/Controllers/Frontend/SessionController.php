<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySessionRequest;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\Session;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\GetAudioFile;





class SessionController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('session_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessions = Session::with(['user', 'media'])->get();

        return view('frontend.sessions.index', compact('sessions'));
    }

    public function create()
    {
        abort_if(Gate::denies('session_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.sessions.create', compact('users'));
    }

    public function store(StoreSessionRequest $request)
    {
        $session = Session::create(
            [
                'name' => $request->name,
                'user_id' => auth()->user()->id,
                'status' => 'New',
            ]
        );

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $session->id]);
        }

        return redirect()->route('frontend.session.recorder', ['id' => $session->id]);
    }


    public function upload(Request $request)
    {
        if($request->has('id')) {
            $session = Session::find($request->id);
        } else {
            return response()->json(['error' => 'No session ID provided.'], 400);
        }

        if ($request->audio) {

            $request->validate([
                'audio' => 'required|file|mimes:mp3,mp4,wav,ogg,m4a,webm',
            ]);
            

            //Get the audio file contents
            $audio = file_get_contents($request->audio);

            //create the file name
            $audioFileName = 'audio_' . time() . '.mp3';

            //save the file to the storage
            Storage::disk('s3')->put('audio/' . $audioFileName, $audio, 'public');
            
            //Get the file path
            $audioUrl = Storage::disk('s3')->url($audioFileName);
           
            //update the session with the audio file
            $session->audio_url = $audioUrl;
            $session->status = 'New';
            
            //Save to the session
            $session->save();

            return response()->json(['success' => 'Audio file uploaded.'], 200);
        } else {
            return response()->json(['error' => 'No audio file provided.'], 400);
        }
    }
    
    

    public function edit(Session $session)
    {
        abort_if(Gate::denies('session_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $session->load('user');

        return view('frontend.sessions.edit', compact('session', 'users'));
    }

    public function update(UpdateSessionRequest $request, Session $session)
    {
        $session->update($request->all());

        if ($request->input('audio', false)) {
            if (! $session->audio || $request->input('audio') !== $session->audio->file_name) {
                if ($session->audio) {
                    $session->audio->delete();
                }
                $session->addMedia(storage_path('tmp/uploads/' . basename($request->input('audio'))))->toMediaCollection('audio');
            }
        } elseif ($session->audio) {
            $session->audio->delete();
        }

        return redirect()->route('frontend.sessions.index');
    }

    public function show(Session $session)
    {
        abort_if(Gate::denies('session_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $session->load('user');

        return view('frontend.sessions.show', compact('session'));
    }

    public function destroy(Session $session)
    {
        abort_if(Gate::denies('session_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $session->delete();

        return back();
    }

    public function massDestroy(MassDestroySessionRequest $request)
    {
        $sessions = Session::find(request('ids'));

        foreach ($sessions as $session) {
            $session->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('session_create') && Gate::denies('session_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Session();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function recorder(Request $request)
    {
        abort_if(Gate::denies('session_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $session = Session::find($request->id);

        $getAudioFile = new GetAudioFile;
        $audio_url = $getAudioFile->getFileFromS3($session->audio_url);
        if(!$audio_url){
            $audio_url = null;
        }

        return view('frontend.sessions.recorder', compact('session', 'audio_url'));
    }


    public function checkUpdates(Request $request)
    {
        $sessionId = $request->id;
        
        // Fetch the latest transcription and summary from the database
        $session = Session::where('id', $sessionId)->first();
        $summary = $session->summary;
        $transcription = $session->transcription;

        return response()->json([
            'transcription' => $transcription ? $transcription : null,
            'summary' => $summary ? $summary : null,
        ]);
    }


    public function checkSessionStatus(Request $request)
    {
        $sessionId = $request->id;
        
        // Fetch the latest transcription and summary from the database
        $session = Session::where('id', $sessionId)->first();
        $status = $session->status;

        return response()->json([
            'status' => $status ? $status : null,
        ]);
    }
}
