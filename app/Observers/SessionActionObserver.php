<?php

namespace App\Observers;

use App\Models\Session;
use App\Notifications\ProcessingCompletedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\GetAudioFile;
use App\Models\Transcribe;
use App\Models\Summerizer;
use App\Models\Tasker;
use App\Models\Todo;

class SessionActionObserver
{
    public function updated(Session $model)
    {
        if ($model->isDirty('audio_url')) {
            $data  = ['action' => 'updated', 'model_name' => 'Session', 'changed_field' => 'audio_url'];
            //get the session id
            $session_id = $model->id;
            $user = $model->user;

            /*--------------------------------------------------------------------
            Get the signed audio file path from amazon and transcibe with openai api
            Log the result
            */
            // Get the audio file URL from the database
            $first_session = Session::where('id', $session_id)->first();
            \Log::info('Session ID: ' . $first_session->session_id);
            if(!$first_session){
                return json_encode(['error'=>'No new session found']);
            }
            
            $audioUrl= $first_session->audio_url;
            
            $getAudioFile = new GetAudioFile();

            // Get the pre-signed URL for the file
            $signedUrl = $getAudioFile->getFileFromS3($audioUrl);

            // Get the transcribed text

            $transcribe = new Transcribe();

            $transcribedText = $transcribe->convertMp3ToText($signedUrl);

            //Save the transcribed text to the database
            $first_session->transcription = $transcribedText;
            $first_session->status = 'Transcribed';

            //Summarise the transcribed text
            $summerizer = new Summerizer();
            $summaryText = $summerizer->summarize($transcribedText);

            if($summaryText){
                $first_session->summary = $summaryText;
            }
            
            $first_session->save();

            //Assign the transcription task to a user
            $tasker = new Tasker();
            $items = $tasker->createTasks('Copy: '.$transcribedText.' Notes:'.$first_session->notes);
              //Convert items to array
            $actions = json_decode($items, true);
      
        //foreach item add the session id and save to the database
        foreach($actions['actionable-items'] as $action){
            $todo = new Todo;
            $todo->session_id =  $first_session->id;
            $todo->item = $action['item'];
            $todo->note = $action['note'];
            $todo->due_date = $action['due_date'];
            $todo->time_due = $action['time_due'];
            $todo->completed = 0;
            //add assigned to this user
            $todo->save();
            $todo->assigned_tos()->sync(auth()->id());

        }
            // Notify the user that the transcription process is completed



            if ($user) {
            // Transcription process completed
            Notification::send($user, new ProcessingCompletedNotification($data));

            }
        }
    }

}