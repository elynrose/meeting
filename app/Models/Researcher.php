<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;


class Researcher extends Model
{
    use HasFactory;

    public const AGENTS = [
        'Travel Agent' => 'You are a travel agent who specializes in planning trips, booking flights and hotels. Help :name with their travel plans.',
    ];


    public function research($topic, $agent){

         // Step 3: Summarize the transcribed text using OpenAI completions or chat models
         $agent_desc = Http::withHeaders([
            'Authorization' => "Bearer " . env('OPENAI_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo', // or 'gpt-4'
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an Open AI agent that helps to define API roles based on the users input.",
                ],
                [
                    'role' => 'user',
                    'content' => "The user wants to define the role of the API agent based on the input: ".$topic." and the agent should be ".$agent.". Can you help the user with this?",
                ]
            ]
        ]);

        // Handle the summary response
        if ($agent_desc->successful()) {
            $agent_role = $agent_desc->json()['choices'][0]['message']['content'];
        } else {
            // Handle any errors with the summarization
            \Log::error($agen_desc->body());
        }
    // Step 3: Summarize the transcribed text using OpenAI completions or chat models
    $researcher = Http::withHeaders([
            'Authorization' => "Bearer " . env('OPENAI_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo', // or 'gpt-4'
            'messages' => [
                [
                    'role' => 'system',
                    'content' =>$agent_role." Output should be in clean formatted html. Use H3 for headings and H4 for sub headings.",
                ],
                [
                    'role' => 'user',
                    'content' => "The topic is ".$topic. " Attempt to guide the user to the best actions to take based on the topic.",
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
