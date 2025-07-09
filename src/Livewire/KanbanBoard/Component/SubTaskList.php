<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Illuminate\Database\Eloquent\Model;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;

class SubTaskList extends Component
{
    public $item;
    public $title;
    public $completed;

    public $search;

    public function mount(){
    }

    #[Computed]
    public function MainTask(){
        $board = $this->item->column->board;
        return Task::query()
            ->whereHas('column', function ($query) use ($board) {
                $query->where('board_id', $board->id);
            })
            ->with(['column', 'badges']) // 必要に応じてリレーションを追加
            ->orderBy('position')
            ->where('parent_id',null)
            ->tap(fn ($query) => $this->search ? $query->where('title','LIKE', '%' . $this->search . '%') : $query)
            ->get();
    }

    public function updated($property){
        match ($property) {
            'completed' => $this->TaskCompleted(),
            default => null,
        };
    }

    public function TaskCompleted(){
        $oldPosition = $this->item->position;

        $this->item->update(['is_completed' => $this->completed]);

        if ($this->completed) {
            // タスクが完了した場合：完了済みタスクの一番下に移動
            $maxCompletedPosition = Task::where('column_id', $this->item->column_id)
                ->where('is_completed', true)
                ->max('position') ?? 0;

            $newPosition = $maxCompletedPosition + 1;

            // 完了したタスクより上にある未完了タスクを上に詰める
            Task::where('column_id', $this->item->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', false)
                ->decrement('position');

        } else {
            // タスクが未完了に戻された場合：未完了タスクの一番下に移動
            $maxUncompletedPosition = Task::where('column_id', $this->item->column_id)
                ->where('is_completed', false)
                ->max('position') ?? 0;

            $newPosition = $maxUncompletedPosition + 1;

            // 未完了に戻されたタスクより下にある完了済みタスクを下にずらす
            Task::where('column_id', $this->item->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', true)
                ->increment('position');
        }

        // タスクの位置を更新
        $this->item->update(['position' => $newPosition]);

        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.sub-task-list');
    }
}
