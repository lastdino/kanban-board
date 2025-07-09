<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardTask;
use Lastdino\KanbanBoard\Models\KanbanBoardComment;

class TaskComments extends Component
{
    public $taskId;
    public $newComment = '';

    public function mount($taskId)
    {
        $this->taskId = $taskId;
    }

    public function render()
    {
        try {
            // コメントテーブルの存在チェック
            if (!\Illuminate\Support\Facades\Schema::hasTable('kanban_board_comments')) {
                return view('kanban-board::livewire.kanban-board.task-comments', [
                    'task' => KanbanBoardTask::findOrFail($this->taskId),
                    'comments' => collect([]),
                    'tableExists' => false
                ]);
            }

            $task = KanbanBoardTask::with(['comments.user'])->findOrFail($this->taskId);

            return view('kanban-board::livewire.kanban-board.task-comments', [
                'task' => $task,
                'comments' => $task->comments,
                'tableExists' => true
            ]);
        } catch (\Exception $e) {
            // エラーが発生した場合は空のコレクションを返す
            return view('kanban-board::livewire.kanban-board.task-comments', [
                'task' => KanbanBoardTask::findOrFail($this->taskId),
                'comments' => collect([]),
                'tableExists' => false
            ]);
        }
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|min:1|max:1000',
        ]);

        KanbanBoardComment::create([
            'task_id' => $this->taskId,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        $this->reset('newComment');
    }

    public function deleteComment($commentId)
    {
        $comment = KanbanBoardComment::find($commentId);

        if ($comment && $comment->user_id === auth()->id()) {
            $comment->delete();
        }
    }
}
