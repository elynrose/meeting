<?php

namespace App\Observers;

use App\Models\Todo;
use App\Notifications\AssignedToEmailNotification;
use Illuminate\Support\Facades\Notification;

class AssignedToActionObserver
{
    public function updated(Todo $model)
    {
        if ($model->isDirty('assigned_to')) {
            $data  = ['action' => 'updated', 'model_name' => 'Todo', 'changed_field' => 'assigned_to'];
            if ($model->assigned_to) {
                $user = $model->assignedTo;
                Notification::send($user, new AssignedToEmailNotification($data));
                \Log::info('Todo assigned to ' . $user->name);
            }
        }
    }

}
