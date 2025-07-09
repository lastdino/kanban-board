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
        // タスクフォロワーのテーブル
        Schema::create('kanban_board_task_followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_reviewer')->default(false);
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('kanban_board_tasks')->onDelete('cascade');
            $table->unique(['task_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_task_followers');
    }
};
