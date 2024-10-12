<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class Summerizer extends Model
{
    use HasFactory;

    public function summarize($transcriptionText){

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
                    'content' => "Summarize the following text: \n" . $transcriptionText
                ]
            ]
        ]);

        // Handle the summary response
        if ($summaryResponse->successful()) {
            $summaryText = $summaryResponse->json()['choices'][0]['message']['content'];
            // Process the summary (e.g., display, save, etc.)
            return trim($summaryText);
        } else {
            // Handle any errors with the summarization
            dd($summaryResponse->body());
        }
    }
}
