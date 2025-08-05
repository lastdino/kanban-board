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
        // KanbanBoardProjectsテーブル
        Schema::create('kanban_board_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });

        // KanbanBoardColumnsテーブル
        Schema::create('kanban_board_columns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('board_id');
            $table->string('color')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('board_id')->references('id')->on('kanban_board_projects')->onDelete('cascade');
            $table->index(['board_id', 'position']);
        });

        // KanbanBoardBadgesテーブル
        Schema::create('kanban_board_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id');
            $table->string('title');
            $table->string('color');
            $table->timestamps();

            $table->foreign('board_id')->references('id')->on('kanban_board_projects')->onDelete('cascade');
        });

        // KanbanBoardTasksテーブル
        Schema::create('kanban_board_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('column_id');
            $table->integer('position')->default(1);
            $table->integer('sub_position')->nullable();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->string('label_color')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('column_id')->references('id')->on('kanban_board_columns')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('kanban_board_tasks')->onDelete('cascade');
            $table->index(['column_id', 'position']);
            $table->index('assigned_user_id');
            $table->index('parent_id');
            $table->index('start_date');
            $table->index('due_date');
            $table->index('reminder_at');
        });

        // badge_taskピボットテーブル
        Schema::create('kanban_board_badge_task', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('badge_id');
            $table->unsignedBigInteger('task_id');
            $table->timestamps();

            $table->foreign('badge_id')->references('id')->on('kanban_board_badges')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('kanban_board_tasks')->onDelete('cascade');
            $table->unique(['badge_id', 'task_id']);
        });

        // project_userピボットテーブル
        Schema::create('kanban_board_project_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('kanban_board_projects')->onDelete('cascade');
            $table->unique(['project_id', 'user_id']);
            $table->index('user_id');
        });

        // チェックリスト項目テーブル
        Schema::create('kanban_board_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('content');
            $table->boolean('is_completed')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('kanban_board_tasks')->onDelete('cascade');
            $table->index(['task_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_badge_task');
        Schema::dropIfExists('kanban_board_tasks');
        Schema::dropIfExists('kanban_board_badges');
        Schema::dropIfExists('kanban_board_columns');
        Schema::dropIfExists('kanban_board_projects');
    }
};
