<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Livewire\Component;

class TaskCard extends Component
{
    public $task;

    public $completed;

    public $project;

    public function mount()
    {
        $this->project = $this->task->column->board;
    }

    public function updated($property)
    {
        match ($property) {
            'completed' => $this->TaskCompleted(),
            default => null,
        };
    }

    public function TaskCompleted()
    {
        $oldPosition = $this->task->position;

        $this->task->update(['is_completed' => $this->completed]);

        if ($this->completed) {
            // タスクが完了した場合：完了済みタスクの一番下に移動
            $maxCompletedPosition = Task::where('column_id', $this->task->column_id)
                ->where('is_completed', true)
                ->max('position') ?? 0;

            $newPosition = $maxCompletedPosition + 1;

            // 完了したタスクより上にある未完了タスクを上に詰める
            Task::where('column_id', $this->task->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', false)
                ->decrement('position');

        } else {
            // タスクが未完了に戻された場合：未完了タスクの一番下に移動
            $maxUncompletedPosition = Task::where('column_id', $this->task->column_id)
                ->where('is_completed', false)
                ->max('position') ?? 0;

            $newPosition = $maxUncompletedPosition + 1;

            // 未完了に戻されたタスクより下にある完了済みタスクを下にずらす
            Task::where('column_id', $this->task->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', true)
                ->increment('position');
        }

        // タスクの位置を更新
        $this->task->update(['position' => $newPosition]);

        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.task-card');
    }
}
