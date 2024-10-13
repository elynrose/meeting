<?php

namespace App\Observers;

use App\Models\Todo;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class AssignedToActionObserver
{
    public function updated(Todo $todo)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Todo'];
        if ($todo->assigned_to) {
            $user = $todo->assignedTo;
            Notification::send($user, new DataChangeEmailNotification($data));
            \Log::info('Todo assigned to ' . $user->name);
        }
    }
}
