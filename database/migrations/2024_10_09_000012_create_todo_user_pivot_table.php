<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('todo_user', function (Blueprint $table) {
            $table->unsignedBigInteger('todo_id');
            $table->foreign('todo_id', 'todo_id_fk_10175827')->references('id')->on('todos')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_10175827')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
