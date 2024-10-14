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
                        'content' => 'You are a helpful assistant tasked with converting user input into a structured list of actionable items in JSON format. You also check for anything important that relates to '.Auth::user()->name.'. The JSON output should be structured under the key "actionable-items", and each item should include the following fields:\n
"item": A concise description of the action to be taken.\n
"note": Additional context or details about the task. If the task is researchable, suggest key points or next steps.\n
"due_date": Suggest a due date based on the user\'s expectations, formatted strictly as "YYYY-MM-DD".\n
"time_due": Suggest a time for the task to be completed, based on the user\'s input or general expectations, strictly formatted as "HH:MM:SS".\n
Ensure that all fields are provided for each task. Example Output:{\n
  "actionable-items": [\n
    {\n
      "item": "Buy groceries",\n
      "note": "Purchase fruits and vegetables. Research local grocery deals if possible.",\n
      "due_date": "2022-12-31",\n
      "time_due": "12:00:00"\n
    }\n
    {
      "item": "Buy groceries",\n
      "note": "Purchase fruits and vegetables. Research local grocery deals if possible.",\n
      "due_date": "2022-12-31",\n
      "time_due": "12:00:00"\n
    }\n
  ]\n
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
