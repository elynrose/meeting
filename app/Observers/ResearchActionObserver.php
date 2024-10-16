<?php

namespace App\Observers;

use App\Models\Todo;
use App\Notifications\ResearchEmailNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Researcher;

class ResearchActionObserver
{
    public function updated(Todo $model)
    {
        if ($model->isDirty('research')) {
            $data  = ['action' => 'updated', 'model_name' => 'Todo', 'changed_field' => 'research'];
            if ($model->research) {
            // Run research on the topic
            $topic = 'Title: '.$model->item.' Summary: '.$model->summary.' Notes: '.$model->notes;
            //Get the research result
            $researcher = new Researcher();
            $article = $researcher->research($topic);
            if(!$article){
                return json_encode(['error'=>'No research result found']);
                \Log::info('No research result found');
            } else {
            //Save the research result to the database
            $model->research_result = $article;
            $model->research = 0;
            $model->save();
            }

            
            // Send an email to the user
            $user = $model->assignedUsers()->first();
            Notification::send($user, new ResearchEmailNotification($data, $article));
            }
        }

    }

}
