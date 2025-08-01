<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Flux\Flux;

class TaskComments extends Component
{
    #[Reactive]
    public $taskId;

    public $comments=[];
    public $commentContent;
    public $replycommentId;


    public function mount()
    {
        $this->comments=Task::find($this->taskId)->comments;
    }


    public function addComment()
    {
        if (!$this->commentContent) {
            return;
        }

        $this->validate([
            'commentContent' => 'required',
        ]);


        $task = Task::find($this->taskId);

        if($this->replycommentId){
            $task->comments()->create([
                'content' => $this->commentContent,
                'user_id' => auth()->id(),
                'reply_id' => $this->replycommentId
            ]);
        }else{
            $task->comments()->create([
                'content' => $this->commentContent,
                'user_id' => auth()->id()
            ]);
        }
        $this->comments = $task->comments;
        $this->commentContent = '';
        $this->replycommentId = null;
    }

    public function replyComment($commentId)
    {
        $this->replycommentId = $commentId;
    }

    #[On('show-modal')]
    public function reload($id){
        $this->comments=Task::find($id)->comments;
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.task-comments');
    }
}
