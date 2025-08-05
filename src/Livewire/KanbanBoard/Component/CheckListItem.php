<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;

class CheckListItem extends Component
{
    public $item;
    public $editing = false;
    public $not_edit=false;
    public $content;
    public $completed;
    public $project;

    public function mount(){
        $this->project=$this->item->task->column->board;
    }

    public function updated($property){
        match ($property) {
            'completed' => $this->item->update(['is_completed' => $this->completed]),
            default => null,
        };
    }

    public function startEditing()
    {
        $this->editing = true;
    }

    public function contentSave()
    {
        $this->editing = false;

        $this->item->update(['content'=>$this->content]);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.check-list-Item');
    }
}
