<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardColumn as Column;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;

/**
 * Class Board
 *
 * Handles operations related to a Kanban board such as task and column management,
 * including reordering, moving between columns, sorting, and task/column CRUD.
 */
class Board extends Component
{
    public $boardId;
    public $showNewTaskModal = false;
    public $selectedColumn = null;
    public $newTaskTitle = '';
    public $newTaskDescription = '';
    public $selectedBadges = [];

    public function mount($boardId = null)
    {
        $this->boardId = $boardId ?? 1; // デフォルトボード
    }

    #[Title('かんばんボード')]
    public function render()
    {
        return view('livewire.kanban-board');
    }

    #[Computed]
    public function columns()
    {
        return Column::with(['tasks.badges'])
            ->where('board_id', $this->boardId)
            ->orderBy('position')
            ->get()
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'title' => $column->title,
                    'tasks_count' => $column->tasks->count(),
                    'cards' => $column->tasks->sortBy('position')->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'description' => $task->description,
                            'position' => $task->position,
                            'badges' => $task->badges->map(function ($badge) {
                                return [
                                    'title' => $badge->title,
                                    'color' => $badge->color,
                                ];
                            })->toArray()
                        ];
                    })->values()->toArray()
                ];
            })->toArray();
    }


    /**
     * タスクを指定された位置に移動する
     * 同一カラム内での移動と異なるカラム間での移動の両方に対応
     *
     * @param int $taskId 移動するタスクのID
     * @param int $toColumnId 移動先のカラムID
     * @param int $newPosition 移動先での新しい位置
     * @return void
     */
    public function moveTaskToPosition($taskId, $toColumnId, $newPosition)
    {
        $task = Task::find($taskId);

        if (!$task) {
            return;
        }

        $oldPosition = $task->position;
        $oldColumnId = $task->column_id;

        // 同じカラム内での移動
        if ($oldColumnId == $toColumnId) {
            $this->reorderTasksInSameColumn($toColumnId, $taskId, $oldPosition, $newPosition);
        } else {
            // 異なるカラム間での移動
            $this->moveTaskBetweenColumns($taskId, $oldColumnId, $oldPosition, $toColumnId, $newPosition);
        }
    }

    /**
     * 同じカラム内でのタスク並び替えを処理する
     * タスクを上下に移動する際、他のタスクの位置を適切に調整する
     *
     * @param int $columnId カラムのID
     * @param int $taskId 移動するタスクのID
     * @param int $oldPosition タスクの現在の位置
     * @param int $newPosition タスクの新しい位置
     * @return void
     */
    private function reorderTasksInSameColumn($columnId, $taskId, $oldPosition, $newPosition)
    {
        if ($oldPosition == $newPosition) {
            return;
        }

        $task = Task::find($taskId);

        if ($oldPosition < $newPosition) {
            // 下に移動：間のタスクを上に詰める
            Task::where('column_id', $columnId)
                ->where('position', '>', $oldPosition)
                ->where('position', '<=', $newPosition)
                ->where('id', '!=', $taskId)
                ->decrement('position');
        } else {
            // 上に移動：間のタスクを下にずらす
            Task::where('column_id', $columnId)
                ->where('position', '>=', $newPosition)
                ->where('position', '<', $oldPosition)
                ->where('id', '!=', $taskId)
                ->increment('position');
        }

        $task->update(['position' => $newPosition]);
    }

    /**
     * 異なるカラム間でのタスク移動を処理する
     * 元のカラムと移動先カラムの両方でタスクの位置を調整する
     *
     * @param int $taskId 移動するタスクのID
     * @param int $fromColumnId 移動元のカラムID
     * @param int $oldPosition 移動元での位置
     * @param int $toColumnId 移動先のカラムID
     * @param int $newPosition 移動先での新しい位置
     * @return void
     */
    private function moveTaskBetweenColumns($taskId, $fromColumnId, $oldPosition, $toColumnId, $newPosition)
    {
        $task = Task::find($taskId);
        //$oldPosition = $task->position;

        // 元のカラムの後続タスクを詰める
        Task::where('column_id', $fromColumnId)
            ->where('position', '>', $oldPosition)
            ->decrement('position');

        // 新しいカラムのタスクをずらす
        Task::where('column_id', $toColumnId)
            ->where('position', '>=', $newPosition)
            ->increment('position');

        // タスクを新しいカラムに移動
        $task->update([
            'column_id' => $toColumnId,
            'position' => $newPosition
        ]);
    }


    public function moveColumnToPosition($ColumnId,$newPosition){
        $Column = Column::find($ColumnId);

        if ($Column->position < $newPosition) {
            // 下に移動：間のColumnを上に詰める
            Column::where('board_id', $Column->board_id)
                ->where('position', '>', $Column->position)
                ->where('position', '<=', $newPosition)
                ->where('id', '!=', $ColumnId)
                ->decrement('position');
        } else {
            // 上に移動：間のColumnを下にずらす
            Column::where('board_id', $Column->board_id)
                ->where('position', '>=', $newPosition)
                ->where('position', '<', $Column->position)
                ->where('id', '!=', $ColumnId)
                ->increment('position');
        }

        $Column->update(['position' => $newPosition]);
    }


    /**
     * タスクをタイトル順にソート
     */
    public function sortTasksByTitle($columnId)
    {
        $tasks = Task::where('column_id', $columnId)
            ->orderBy('title')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->update(['position' => $index + 1]);
        }
    }

    /**
     * タスクを作成日順にソート
     */
    public function sortTasksByCreated($columnId)
    {
        $tasks = Task::where('column_id', $columnId)
            ->orderBy('created_at')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->update(['position' => $index + 1]);
        }
    }

    public function openNewTaskModal($columnId)
    {
        $this->selectedColumn = $columnId;
        $this->showNewTaskModal = true;
        $this->reset(['newTaskTitle', 'newTaskDescription', 'selectedBadges']);
    }

    public function closeNewTaskModal()
    {
        $this->showNewTaskModal = false;
        $this->reset(['selectedColumn', 'newTaskTitle', 'newTaskDescription', 'selectedBadges']);
    }

    public function createTask()
    {
        $this->validate([
            'newTaskTitle' => 'required|min:3|max:255',
            'newTaskDescription' => 'nullable|max:1000',
        ]);

        $maxPosition = Task::where('column_id', $this->selectedColumn)->max('position') ?? 0;

        $task = Task::create([
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription,
            'column_id' => $this->selectedColumn,
            'position' => $maxPosition + 1,
        ]);

        foreach ($this->selectedBadges as $badgeId) {
            $task->badges()->attach($badgeId);
        }

        $this->closeNewTaskModal();
    }

    public function deleteTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $columnId = $task->column_id;
            $position = $task->position;

            $task->delete();

            // 後続のタスクの位置を調整
            Task::where('column_id', $columnId)
                ->where('position', '>', $position)
                ->decrement('position');
        }
    }
}
