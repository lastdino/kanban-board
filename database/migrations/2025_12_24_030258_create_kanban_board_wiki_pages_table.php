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
        Schema::create('kanban_board_wiki_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // どのプロジェクトのWikiか
            $table->string('title');
            $table->longText('content')->nullable();
            $table->unsignedBigInteger('user_id'); // 作成者
            $table->integer('position')->default(0); // 並び順
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('kanban_board_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_wiki_pages');
    }
};
