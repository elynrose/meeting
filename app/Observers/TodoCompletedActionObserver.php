<?php

namespace App\Observers;

use App\Models\Todo;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class TodoCompletedActionObserver
{
    public function updated(Todo $model)
        {
                if(!$model->isDirty('completed')==1) {
                
                $data  = ['action' => 'updated', 'model_name' => 'Todo', 'changed_field' => 'completed'];
                $users = $model->assignedUsers()->get(); // Ensure it returns a relationship instance
                if ($users->isNotEmpty()) {
                    Notification::send($users, new DataChangeEmailNotification($data));
                }

            } else  {
                $data  = ['action' => 'updated', 'model_name' => 'Todo', 'changed_field' => 'completed'];
                $users = $model->assignedUsers()->get(); // Ensure it returns a relationship instance
                if ($users->isNotEmpty()) {
                    Notification::send($users, new DataChangeEmailNotification($data));
                }
            }
        }
}
