<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Lastdino\KanbanBoard\Models\KanbanBoardProject;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

use Flux\Flux;

class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $title = '';
    public $description = '';
    public $editMode = false;
    public $currentProject = null;
    public $user_id=null;
    public $users;


    public function mount()
    {
    }

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }


    #[Computed]
    public function projects()
    {
        $perPage = 25;
        return KanbanBoardProject::query()
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->search ? $query->where('title', 'like', '%' . $this->search . '%') : $query)
            ->paginate($perPage);
    }



    public function render()
    {
        return view('kanban-board::livewire.kanban-board.project-list');
    }

    public function resetFilters()
    {
        $this->reset(['search']);
    }



    public function resetForm()
    {
        $this->reset(['title', 'description', 'editMode', 'currentProject']);
    }

    public function addProject()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project=KanbanBoardProject::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => auth()->id(),
        ]);

        $project->users()->attach(auth()->id());

        $this->resetForm();
        Flux::modal('add-project')->close();
    }

    public function editProject($projectId)
    {
        $this->currentProject = KanbanBoardProject::find($projectId);
        $this->title = $this->currentProject->title;
        $this->description = $this->currentProject->description;
        $this->user_id = $this->currentProject->user_id;
        $this->users=$this->currentProject->users;
        $this->editMode = true;
        Flux::modal('add-project')->show();
    }

    public function updateProject()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|integer',
        ]);

        $this->currentProject->update([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => $this->user_id,
        ]);

        $this->resetForm();
        Flux::modal('add-project')->close();
    }

    public function openBoard($projectId)
    {
        return redirect()->route(config('kanban-board.routes.prefix').'.board', ['boardId' => $projectId]);
    }


}
