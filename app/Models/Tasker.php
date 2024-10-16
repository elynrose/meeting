<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Auth;

class Tasker extends Model
{
    use HasFactory;

    public function createTasks($text)
    {
        try {
            $convertToActionableJsonList = Http::withHeaders([
                'Authorization' => "Bearer " . env('OPENAI_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo', // or 'gpt-4'
                'messages' => [
                    [
                        'role' => 'system',
                       'content' => 'You are a proactive assistant who specializes in breaking down complex user input into well-defined, actionable tasks. Each task should be practical, easy to understand, and organized in JSON format with the following fields:\n
- "item": A succinct and specific action that the user should take, phrased as an imperative.\n
- "note": Offer additional context, insights, or recommendations to enhance the task. If relevant, suggest research avenues, best practices, or helpful resources.\n
- "due_date": Set a realistic deadline for each task, taking into account any implied urgency, using the format "YYYY-MM-DD".\n
- "time_due": Suggest an optimal time for task completion, formatted as "HH:MM:SS". Consider time sensitivity or convenience for the user when proposing this.\n
Your goal is to help '.Auth::user()->name.' achieve maximum efficiency by creating tasks that are relevant, manageable, and clearly prioritized. Additionally, ensure any time-sensitive or high-priority tasks are emphasized first. The output should be a valid JSON object with the key "actionable-items". Current date: '.date("Y-m-d").'. Example output:\n
{
  "actionable-items": [
    {
      "item": "Prepare weekly report",
      "note": "Summarize project milestones and pending issues. Include charts if available.",
      "due_date": "2024-10-20",
      "time_due": "09:00:00"
    },
    {
      "item": "Plan client presentation",
      "note": "Outline the agenda for the upcoming meeting. Gather relevant case studies for reference.",
      "due_date": "2024-10-21",
      "time_due": "13:00:00"
    }
  ]
}'

                    ],
                    [
                        'role' => 'user',
                        'content' => "Convert the following text into multiple actionable items for ".Auth::user()->name.": ". $text
                    ]
                ]
            ]);

            if ($convertToActionableJsonList->successful()) {
                $list = $convertToActionableJsonList->json()['choices'][0]['message']['content'];
               
            //Confirm that the response is in json format
                if (json_decode($list)) {
                    return $list;
                } else {
                    Log::error('Error in converting to actionable items: ' . $list);
                    return null;
                }    
            } else {
                Log::error('Error in converting to actionable items: ' . $list->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception in task method: ' . $e->getMessage());
            return null;
        }
    }
}
