<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;


class Researcher extends Model
{
    use HasFactory;

    public function research($topic){

        // Step 3: Summarize the transcribed text using OpenAI completions or chat models
        $researcher = Http::withHeaders([
            'Authorization' => "Bearer " . env('OPENAI_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo', // or 'gpt-4'
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful research assistant that scours the internet for information on a given topic. The result should be in clean html code without document, head and body elements.',
                ],
                [
                    'role' => 'user',
                    'content' => "Do an indepth research of the following topic: \n" . $topic
                ]
            ]
        ]);

        // Handle the summary response
        if ($researcher->successful()) {
            $article = $researcher->json()['choices'][0]['message']['content'];
            // Process the summary (e.g., display, save, etc.)
            return $article;
        } else {
            // Handle any errors with the summarization
            \Log::error($researcher->body());
        }
    }
}
