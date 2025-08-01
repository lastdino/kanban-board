<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardColumn as Column;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Lastdino\KanbanBoard\Models\kanbanBoardProject as Project;

use Flux\Flux;

/**
 * Class Board
 *
 * Handles operations related to a Kanban board such as task and column management,
 * including reordering, moving between columns, sorting, and task/column CRUD.
 */
class Board extends Component
{
    #[Url]
    public $boardId;
    public $project;

    public $NotInvitedUsers;
    public $users;

    public $search = '';
    public $sortBy = 'id';
    public $sortDirection = 'asc';

    public $column_title='';

    public $selectedUser;

    public $admin='';


    public $task_assigned_user='';

    public $column_title_edit;

    public function mount($boardId = null)
    {
        $this->boardId = $boardId ?? 1; // デフォルトボード
        $this->project=Project::find($this->boardId);
        $this->NotInvitedUsers=$this->project->NotInvitedUsers();
        $this->users=$this->project->users;
        $this->admin=$this->project->admin;
    }

    #[Title('かんばんボード')]
    public function render()
    {
        return view('kanban-board::livewire.kanban-board.board');
    }

    #[Computed]
    public function columns()
    {
        return Column::with('tasks')->where('board_id', $this->boardId)
            ->orderBy('position')
            ->get();
    }


    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function invite(){
        Flux::modal('invite')->close();
        $this->project->users()->attach($this->selectedUser);
        Flux::toast(heading: '招待', text: '招待しました。', variant: 'success',);

        $this->NotInvitedUsers=$this->project->NotInvitedUsers();
        $this->users=$this->project->users;
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

        if ($task->is_completed) {
            return;
        }

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

        if ($task->is_completed) {
            $maxCompletedPosition = Task::where('column_id', $toColumnId)
                ->where('is_completed', true)
                ->max('position') ?? 0;
            $newPosition = $maxCompletedPosition + 1;
        }else{
            // 新しいカラムのタスクをずらす
            Task::where('column_id', $toColumnId)
                ->where('position', '>=', $newPosition)
                ->increment('position');
        }

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

    public function setLabelColor($color,$column)
    {
        if($column['color'] == $color){
            $color = null;
        }
        Column::find($column['id'])->update(['color'=>$color]);
        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function removeColumn($id){
        $db=Column::find($id);
        $db->delete();
    }

    public function addColumn(){

        $this->validate([
            'column_title' => 'required',
        ]);

        $maxPosition = Column::where('board_id', $this->boardId)->max('position') ?? 0;

        $task = Column::create([
            'title'=>$this->column_title,
            'board_id'=>$this->boardId,
            'position' => $maxPosition + 1,
        ]);

        $this->refreshColumns();
    }

    public function editColumnTitle($columnId,$oldTitle){
        $this->column_title_edit=$columnId;
        $this->column_title=$oldTitle;
    }

    public function updateColumnTitle($columnId)
    {
        $this->validate([
            'column_title' => 'required',
        ]);
        Column::find($columnId)->update(['title' => $this->column_title]);
        $this->column_title_edit=null;
        // 最新データを取得し直す（省略してもOKなら不要）
        $this->refreshColumns();
    }

    #[On('refresh-columns')]
    public function refreshColumns()
    {
        // Computedプロパティをリセット
        unset($this->columns);
    }
}
