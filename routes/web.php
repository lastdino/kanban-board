<?php

use Illuminate\Support\Facades\Route;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\ProjectList;

Route::middleware(config('kanban-board.routes.middleware'))
    ->prefix(config('kanban-board.routes.prefix'))
    ->name(config('kanban-board.routes.prefix').'.')
    ->group(function () {
        Route::get('/project_list', ProjectList::class)->name('project_list');
        Route::get('/board', Board::class)->middleware('kanban.project.access')->name('board');
        Route::get('/download/{media}', function (Request $request, $media) {
            $mediaItem = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($media);
            $path = $mediaItem->getPath(); // 物理パス

            return response()->download($path, $mediaItem->name);
        })->middleware('signed')->name('download.signed');
    });
