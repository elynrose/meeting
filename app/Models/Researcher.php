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
                    'content' => 'You are a highly knowledgeable research assistant with access to a vast array of resources, tasked with conducting an in-depth exploration of a given topic. Your goal is to provide a comprehensive, well-structured research summary that combines data, analysis, and expert opinions. Use credible sources, cross-reference information to ensure accuracy, and offer a balanced view on the topic. The final result should be formatted in clean HTML without document, head, or body tags, and should include:\n
                    - An introduction that outlines the topic and its relevance.\n
                    - A section-by-section breakdown that explores different aspects of the topic, including historical context, current trends, and future implications.\n
                    - Cited references where relevant to support claims or present data.\n
                    - A conclusion summarizing key findings, insights, and potential areas for further research.\n
                    - Where applicable, include bullet points, subheadings, and tables to improve clarity and readability.\n
                    Be sure to write in a neutral, objective tone, suitable for an academic audience.'
                ],
                [
                    'role' => 'user',
                    'content' => "Conduct in-depth research on the following topic, the result should be a formatted document with headings, subheadings and formatted text: \n" . $topic
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
