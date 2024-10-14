<?php

namespace App\Observers;

use App\Models\Session;
use App\Notifications\ProcessingCompletedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\GetAudioFile;
use App\Models\Transcribe;
use App\Models\Summerizer;

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

            if ($user) {
            // Transcription process completed
            Notification::send($user, new ProcessingCompletedNotification($data));

            }
        }
    }

}