<?php

namespace Lastdino\KanbanBoard;

use Illuminate\Support\ServiceProvider;
use Lastdino\KanbanBoard\Helpers\UserDisplayHelper;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Component\SubTaskList;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Component\TaskCard;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Component\TaskModal;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Component\CheckListItem;

use Livewire\Livewire;

class KanbanBoardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        $this->publishes([
            __DIR__ . '/../config/kanban-board.php' => config_path('kanban-board.php'),
        ],'kanban-config');



        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/kanban-board'),
        ], 'kanban-views');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'kanban-migrations');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/kanban-board'),
        ],'kanban-lang');

        $this->loadLivewireComponents();

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'kanban-board');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'kanban-board');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/kanban-board.php',
            'kanban-board'
        );

        $this->app->singleton('kanban-board.user-display', function () {
            return new UserDisplayHelper();
        });

    }

    // custom methods for livewire components
    protected function loadLivewireComponents(): void
    {
        Livewire::component('kanban-board.board', Board::class);
        Livewire::component('kanban-board.component.task-modal', TaskModal::class);
        Livewire::component('kanban-board.component.check-list-item', CheckListItem::class);
        Livewire::component('kanban-board.component.sub-task-list', SubTaskList::class);
        Livewire::component('kanban-board.component.task-card', TaskCard::class);

    }
}
