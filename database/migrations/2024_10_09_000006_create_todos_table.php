<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodosTable extends Migration
{
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item')->nullable();
            $table->longText('note')->nullable();
            $table->date('due_date')->nullable();
            $table->time('time_due')->nullable();
            $table->integer('research')->default(0)->nullable();
            $table->longText('research_result')->nullable();
            $table->boolean('send_reminder')->default(0)->nullable();
            $table->integer('completed')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
