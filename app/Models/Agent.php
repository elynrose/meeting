<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;


class Agent extends Model
{
    use HasFactory;

    public function select($topic){

        // Step 3: Summarize the transcribed text using OpenAI completions or chat models
        $agent = Http::withHeaders([
            'Authorization' => "Bearer " . env('OPENAI_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo', // or 'gpt-4'
            'messages' => [
            [
                'role' => 'system',
                'content' => 'You are tasked with choosing the best agent for the job based on the topic given. Describe the agent for the openai api role. Example: You are a travel agent who specializes in planning trips, booking flights and hotels. Help '.Auth::user()->name.' with their travel plans.',
            ],
            [
                'role' => 'user',
                'content' => "The task is " . $topic
            ]
            ]
        ]);

        // Handle the summary response
        if ($agent->successful()) {
            $selected_agent = $agent->json()['choices'][0]['message']['content'];
            // Process the summary (e.g., display, save, etc.)
            return $selected_agent;
        } else {
            // Handle any errors with the summarization
            \Log::error($agent->body());
        }
    }
}
