<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                        'content' => 'You are a helpful assistant that reads a text and converts it into a list of actionable items. You provide the list in json format with the main key actionable-items and sub keys, item, note, due_date, time_due. An example of the result is: {"actionable-items": [{"item": "Buy groceries", "note": "Buy milk, eggs, and bread", "due_date": "suggest a due date in this format: 2022-12-31", "time_due": "suggest a time due in this format: 12:00:00"}]}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Convert the following text into multiple actionable items: \n" . $text
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
