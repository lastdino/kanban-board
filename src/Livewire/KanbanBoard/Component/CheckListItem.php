<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Livewire\Component;

class CheckListItem extends Component
{
    public $item;

    public $editing = false;

    public $not_edit = false;

    public $content;

    public $completed;

    public $project;

    public function mount()
    {
        $this->project = $this->item->task->column->board;
    }

    public function updated($property)
    {
        match ($property) {
            'completed' => $this->CheckListCompleted(),
            default => null,
        };
    }

    public function CheckListCompleted()
    {
        $this->item->update(['is_completed' => $this->completed]);
        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function startEditing()
    {
        $this->editing = true;
    }

    public function contentSave()
    {
        $this->editing = false;

        $this->item->update(['content' => $this->content]);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.check-list-Item');
    }
}
