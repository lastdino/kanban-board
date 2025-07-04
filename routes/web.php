<?php

use Illuminate\Support\Facades\Route;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;


Route::middleware(config('kanban-board.routes.middleware'))
    ->prefix(config('kanban-board.routes.prefix'))
    ->name(config('kanban-board.routes.prefix'). '.')
    ->group(function () {
        Route::get('/board', Board::class)->name('board');
    });
