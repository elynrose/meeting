<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Transcribe extends Model
{
    use HasFactory;

    public function convertMp3ToText($signedUrl)
    {
        // Download the audio file from S3 signed URL
        $audioFileContents = @file_get_contents($signedUrl);
        if ($audioFileContents === false) {
            Log::error('Failed to download audio file from URL: ' . $signedUrl);
            return;
        }
        Log::info('Converting audio file to text');

        // Download the audio file from S3 signed URL
        $audioFileContents = file_get_contents($signedUrl);

        Log::info('Audio file contents: ' . $audioFileContents);

        // Save the file temporarily to local storage (optional but recommended)
        $tempFilePath = storage_path('app/public/audio.mp3');
        $openAiKey = env('OPENAI_KEY');
        if (!$openAiKey) {
            Log::error('OpenAI API key is not set.');
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $openAiKey,
        ])->attach(
            'file', file_get_contents($tempFilePath), 'audio.mp3' // Attach the file binary data
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1', // Specify model for transcription
        ]);

        Log::info('Transcription response: ' . $response->body());
        
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
