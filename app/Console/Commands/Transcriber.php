<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Session;
use App\Models\GetAudioFile;
use App\Models\Transcribe;
use App\Models\Summerizer;

class Transcriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transcriber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
           /*--------------------------------------------------------------------
            Get the signed audio file path from amazon and transcibe with openai api
            Log the result
            */
            // Get the audio file URL from the database
            $first_session = Session::where('status', 'New')->first();
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

            
            


    }
}
