<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\TodoReminder;
use App\Models\Todo;
use Illuminate\Support\Facades\Mail;


class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Send reminders to users about todos that are in three days

        $todos = Todo::where('due_date', now()->addDays(3))->get();

        foreach ($todos as $todo) {
            $todo->user->notify(new TodoReminder($todo));
        }

        $todos_today = Todo::where('due_date', now())->get();

        foreach ($todos_today as $todo_today) {
            $todo->user->notify(new TodoReminder($todo_today));
        }
    
}
}