<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Session;


class Transcribe extends Model
{
    use HasFactory;

    public function convertMp3ToText($filePath)
    {
        // Sending the file to OpenAI as a multipart form request
        $session = Session::where('status', 'New')->first();

        $signedUrl = $session->audio_url;

        // Download the audio file from S3 signed URL
        $audioFileContents = file_get_contents($signedUrl);

        // Save the file temporarily to local storage (optional but recommended)
        $tempFilePath = storage_path('app/public/audio.mp3');
        file_put_contents($tempFilePath, $audioFileContents);

        // Sending the file to OpenAI as a multipart form request
        $response = Http::withHeaders([
            'Authorization' => "Bearer " . env('OPENAI_KEY'),
        ])->attach(
            'file', file_get_contents($tempFilePath), 'audio.mp3' // Attach the file binary data
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1', // Specify model for transcription
        ]);

        // Handle the response
        if ($response->successful()) {
            $transcription = $response->json();
            // Process transcription data
            return trim($transcription['text']);

        } else {
            // Handle errors
            return $response->json();
        }
       
    }

}
