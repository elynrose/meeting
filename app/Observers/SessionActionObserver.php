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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Recording;

class SessionActionObserver
{
    /**
     * Handle the Session "updated" event.
     *
     * @param  \App\Models\Session  $model
     * @return void
     */
    public function updated(Session $model)
    {
        // Check if the 'audio_url' field has been changed
        if ($model->isDirty('audio_url')) {
            $data = ['action' => 'updated', 'model_name' => 'Session', 'changed_field' => 'audio_url'];

            // Get the session id and user id
            $session_id = $model->id;
            $user = $model->user_id;
            \Log::info('Data Session ID: ' . $session_id . ' User ID: ' . $user);

            // Get the session from the database
            $first_session = Session::where('id', $session_id)->first();

            // If no session found, return an error
            if (!$first_session) {
                return json_encode(['error' => 'No new session found']);
            }

            // Get the audio file URL from the session
            $audioUrl = $first_session->audio_url;

            // Get the pre-signed URL for the file from S3
            $getAudioFile = new GetAudioFile();
            $signedUrl = $getAudioFile->getFileFromS3($audioUrl);

            // Save the signed URL and update the session status to 'Processing'
            $first_session->audio_url = $signedUrl;
            $first_session->status = 'Processing';
            $first_session->save();

            // Download the audio file from the signed URL
            $audioFileContents = file_get_contents($signedUrl);
            \Log::info('Audio file contents: ' . $audioFileContents);

            // Save the audio file temporarily to local storage
            $tempFilePath = storage_path('app/public/audio.mp3');
            file_put_contents($tempFilePath, $audioFileContents);

            // Send the audio file to OpenAI for transcription
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . env('OPENAI_KEY'),
            ])->attach(
                'file', file_get_contents($tempFilePath), 'audio.mp3'
            )->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
            ]);

            \Log::info('Transcription response: ' . $response->body());

            // Handle the transcription response
            if ($response->successful()) {
                $transcription = $response->json();
                $transcribedText = trim($transcription['text']);
            } else {
                return $response->json();
            }

            // If no transcribed text, return
            if (!$transcribedText) {
                return;
            }

            // Save the transcribed text to the session and update status to 'Transcribed'
            $first_session->transcription = $transcribedText;
            $first_session->status = 'Transcribed';
            $first_session->save();

           
           
           
            /* Summarize the transcribed text
            $summerizer = new Summerizer();
            $summaryText = $summerizer->summarize($transcribedText);
            */

            if(empty($transcribedText)){
                return;
            }
    
            // Step 3: Summarize the transcribed text using OpenAI completions or chat models
            $summaryResponse = Http::withHeaders([
                'Authorization' => "Bearer " . env('OPENAI_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo', // or 'gpt-4'
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that summarizes text. Results'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Summarize the following text: \n" . $transcribedText
                    ]
                ]
            ]);
    
            // Handle the summary response
            if ($summaryResponse->successful()) {
                $summaryText = $summaryResponse->json()['choices'][0]['message']['content'];
                // Process the summary (e.g., display, save, etc.)
            } 
            else {
                // Handle any errors with the summarization
                \Log::error('Error summarizing text: ' . $summaryResponse->body());
                return null;
            }
            // Save the summary text to the session
            if ($summaryText) {
                $first_session->summary = $summaryText;
            }

            Recording::create(
                [
                    'audio_url'=> $audioUrl,
                    'session_id' => $first_session->id,
                    'transcription' => $transcribedText,
                    'summary' => $summaryText
                ]
            );
            $first_session->save();
            

            /* Create tasks from the transcribed text
            $tasker = new Tasker();
            $items = $tasker->createTasks(Auth::user()->name . '\'s input: ' . $transcribedText . ' Notes:' . $first_session->notes);
            $actions = json_decode($items, true);

            // Save each task to the database
            foreach ($actions['actionable-items'] as $action) {
                $todo = new Todo;
                $todo->session_id = $first_session->id;
                $todo->item = $action['item'];
                $todo->note = $action['note'];
                $todo->due_date = $action['due_date'];
                $todo->time_due = $action['time_due'];
                $todo->completed = 0;
                $todo->save();
                $todo->assigned_tos()->sync(auth()->id());
            }

            // Notify the user that the transcription process is completed
            if ($user) {
                // Notification::send($user, new ProcessingCompletedNotification($data));
            }
                */
        }
    }
}
