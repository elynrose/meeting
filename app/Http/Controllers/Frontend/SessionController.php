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
use App\Models\Todo;
use App\Models\Tasker;
use App\Models\Credit;
use Auth;
use App\Models\Recording;



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
        \Log::info($request);

        if($request->has('id')) {
            $session = Session::find($request->id);
        } else {
            return response()->json(['error' => 'No session ID provided.'], 400);
        }

        if ($request->audio) {

            $request->validate([
                'audio' => 'required|file|mimes:mp3,mp4,wav,ogg,m4a,webm',
            ]);

            /*Delete the old audio file
            if($session->audio_url){
                $getAudioFile = new GetAudioFile;
                $getAudioFile->deleteFileFromS3($session->audio_url);
            }
            */

            //get the max time limit
            $maxTime = $request->max_time;
            //get the recorded time
            $recordedTime = $request->recorded_time;



            //chech if recorded time is more than the max time limit
            if($recordedTime > $maxTime){
                return response()->json(['error' => 'Recording time is more than the maximum time limit.'], 400);
            } 

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
            if($session->save()){
            //Calculate and deduct the credits  
            $credits = Credit::where('email', Auth::user()->email)->first();
            $credits_per_second = ($credits->points * env('COST_PER_SECOND'));
            $credits_bal = ($credits_per_second - $recordedTime);
            $credits_count = $credits_bal / env('COST_PER_SECOND');
            //Round up the credits to two decimal places
            $credits_count = round($credits_count, 1);
            $credits->points=$credits_count;
            $credits->save();

            return response()->json(['success' => 'Audio file uploaded.'], 200);

            } else {
                return response()->json(['error' => 'Error saving the audio file.'], 400);
            }

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

           //delete all todos for this session
           $todos = Todo::where('session_id', $session->id)->get();
           foreach($todos as $todo){
               $todo->delete();

               //delete the audio files
               if($todo->audio_url){
                   $getAudioFile = new GetAudioFile;
                   $getAudioFile->deleteFileFromS3($todo->audio_url);
               }
           }
           //delete all the assiged todos
           $session->assigned_tos()->sync([]);
           //delete the session
           
           $session->delete();

        return back();
    }

    public function massDestroy(MassDestroySessionRequest $request)
    {
        $sessions = Session::find(request('ids'));

        foreach ($sessions as $session) {
            //delete all todos for this session
            $todos = Todo::where('session_id', $session->id)->get();
            foreach($todos as $todo){
                $todo->delete();

                //delete the audio files
                if($todo->audio_url){
                    $getAudioFile = new GetAudioFile;
                    $getAudioFile->deleteFileFromS3($todo->audio_url);
                }
            }
            //delete all the assiged todos
            $session->assigned_tos()->sync([]);
            //delete the session
            
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

       
        $recordings = Recording::where('session_id', $request->id)->orderBy('created_at', 'desc')->get();
        $session = Session::find($request->id);
        
        //if session user is not the current user, redirect to the dashboard
        if($session->user_id != auth()->id()){
            return redirect()->route('frontend.home');
        }

        $credits = Credit::where('email', Auth::user()->email)->first();
        //if user has no credits give them 10 credits
        if(!$credits){
            $credits = new Credit;
            $credits->email = Auth::user()->email;
            $credits->points = 10;
            $credits->save();
        }
        $credits = $credits->points;


        //if user does not have todos for this session create one
        $todos = Todo::where('session_id', $request->id)->whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->get();

        if($todos->count() == 0){
            $todo = new Todo;
            $todo->session_id = $request->id;
            $todo->item = 'Do something adventurous';
            $todo->note = 'We have created a free task for you to get started. You can add more tasks by clicking the add new recording button.';
            $todo->due_date = date('Y-m-d');
            $todo->time_due = date('H:i:s');
            $todo->completed = 0;
            //add assigned to this user
            $todo->save();
            $todo->assigned_tos()->sync(auth()->id());
        }   
        
        $todos = Todo::where('session_id', $request->id)->where('completed', 0)->whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->orderBy('due_date', 'asc')->get();
        
        $todo_completeds = Todo::where('session_id', $request->id)->where('completed', 1)->whereHas('assigned_tos', function ($query) {
            $query->where('id', auth()->id());
        })->orderBy('due_date', 'asc')->get();


        $getAudioFile = new GetAudioFile;
        
        $audio_url = $getAudioFile->getFileFromS3($session->audio_url);
        if(!$audio_url){
            $audio_url = null;
        }

        $assigned_tos = User::pluck('email', 'id', 'name');
        

        return view('frontend.sessions.recorder', compact('session', 'audio_url', 'todos', 'todo_completeds', 'assigned_tos', 'credits', 'recordings'));
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


    public function createToDoList(Request $request)
    {
        $sessionId = $request->id;
        $toDoList = $request->toDoList;
        $relatedTodos = Todo::where('session_id', $sessionId)->get();
        $relatedTodo = " Do not include these already assigned tasks: ";
        if($relatedTodos){
            foreach($relatedTodos as $todo){
                $relatedTodo .= $todo->item . ', ';
            }
        }
        // Fetch the session from the database
        $session = Session::where('id', $sessionId)->first();
        $summary = $session->summary;
        $note = $session->notes;
        $copy = 'Summary: '.$summary . ' Notes: ' . $note." ".$relatedTodo;
        $tasker = new Tasker;
        $items = $tasker->createTasks($copy);

        //Convert items to array
        $actions = json_decode($items, true);
      
        //foreach item add the session id and save to the database
        foreach($actions['actionable-items'] as $action){
            $todo = new Todo;
            $todo->session_id = $session->id;
            $todo->item = $action['item'];
            $todo->note = $action['note'];
            $todo->due_date = $action['due_date'];
            $todo->time_due = $action['time_due'];
            $todo->completed = 0;
            $todo->color = $this->getColorBasedOnDateAndStatus($action['due_date'], 0);
            //add assigned to this user
            $todo->save();
            $todo->assigned_tos()->sync(auth()->id());

        }

        // get total number of to-do's for this session
        $total_todos = Todo::where('session_id', $session->id)->count();

        //Get all todos for this session
        $todos = Todo::where('session_id', $session->id)->whereHas('assigned_tos', function ($query) {
        $query->where('id', auth()->id());
        })->orderBy('due_date', 'asc')->get();

        
       
        //Send the items back to the frontend
        $json = response()->json([
            'todo' => $todos
        ]);
        return $json;       
    }


   
    public function getColorBasedOnDateAndStatus($date, $status){
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
        return $color;
    }


    public function saveNotes(Request $request)
    {
        $sessionId = $request->id;
        $notes = $request->notes;
        \Log::info('Notes: ' . $notes);
        // Fetch the session from the database
        $session = Session::where('id', $sessionId)->first();
        $session->notes = $notes;
        $session->save();

        return response()->json([
            'success' => 'Notes saved successfully.',
        ]);
    }


    public function UpdateTodoStatus(Request $request)
    {
        $todoId = $request->id;
        $status = $request->status;

        if($status == 'completed'){
            $status = 1;
        } elseif($status == 'pending'){ 
            $status = 0;
        }
        
        // Fetch the to-do from the database
        $todo = Todo::where('id', $todoId)->first();
        $todo->completed = $status;
        $todo->save();

        return response()->json([
            'success' => 'To-do status updated successfully.',
        ]);
    }

    public function saveSummary(Request $request)
    {
        $sessionId = $request->id;
        $summary = $request->summary;
        // Fetch the session from the database
        $session = Session::where('id', $sessionId)->first();
        $session->summary = $summary;
        $session->save();

        return response()->json([
            'success' => 'Summary saved successfully.',
        ]);
    }


    public function UpdateTodoResearch(Request $request)
    {
        $todoId = $request->id;
        $isChecked = $request->is_checked;

        \Log::info('Is Checked: ' . $isChecked);
     
        // Fetch the to-do from the database
        $todo = Todo::where('id', $todoId)->first();
        \Log::info('Todo: ' . $todo->research);
        
        //If the research is set to true it means the checkbox is checked and vise versa
        if($isChecked == 'true'){
            $todo->research = 1;
        } else {
         //   $todo->research_result = null;
            $todo->research = 0;
        }
        
        $todo->save();

        return response()->json([
            'success' => 'To-do research updated successfully.',
        ]);
    }
}
