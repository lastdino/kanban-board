<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kanban_board_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->text('content');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('kanban_board_tasks')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('kanban_board_comments')->onDelete('cascade');
            $table->index('user_id');
            $table->index('task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_comments');
    }
};
