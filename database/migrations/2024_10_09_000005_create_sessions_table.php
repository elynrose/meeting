<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('audio_url')->nullable();
            $table->longText('transcription')->nullable();
            $table->longText('summary')->nullable();
            $table->longText('notes')->nullable();
            $table->string('language')->nullable();
            $table->integer('task_created')->nullable();
            $table->integer('total_tasks')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
