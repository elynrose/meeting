<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Researcher;



class Research extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:research';

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
        //Get the first todo item that has research set to 1
        $todo = Todo::where('research', 1)->first();
        if(!$todo){
            return json_encode(['error'=>'No new research found']);
        }

        //Get the research result
        $researcher = new Researcher();
        $topic = "Title: ".$todo->item." Details:".$todo->note;
        $article = $researcher->research($topic);
        //Save the research result to the database
        $todo->research_result = $article;
        $todo->research = 0;
        $todo->save();

    }
}
