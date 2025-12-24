<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Illuminate\Support\Facades\Auth;
use Lastdino\KanbanBoard\Models\KanbanBoardProject;
use Lastdino\KanbanBoard\Models\KanbanBoardWikiPage;
use Lastdino\KanbanBoard\Models\KanbanBoardWikiTemplate;
use Livewire\Attributes\Url;
use Livewire\Component;

class Wiki extends Component
{
    #[Url]
    public $boardId;

    public KanbanBoardProject $project;

    public $selectedPageId = null;

    public $isEditing = false;

    public $title = '';

    public $content = '';

    public $templateName = '';

    public $isGlobalTemplate = false;

    public $showTemplateModal = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
    ];

    public function mount()
    {
        $this->project = KanbanBoardProject::findOrFail($this->boardId);

        $firstPage = $this->project->wikiPages()->first();
        if ($firstPage) {
            $this->selectPage($firstPage->id);
        }
    }

    public function selectPage($id)
    {
        $this->selectedPageId = $id;
        $page = KanbanBoardWikiPage::findOrFail($id);
        $this->title = $page->title;
        $this->content = $page->content;
        $this->isEditing = false;
    }

    public function createPage()
    {
        $this->selectedPageId = null;
        $this->title = '';
        $this->content = '';
        $this->isEditing = true;
    }

    public function editPage()
    {
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->selectedPageId) {
            $page = KanbanBoardWikiPage::findOrFail($this->selectedPageId);
            $page->update([
                'title' => $this->title,
                'content' => $this->content,
            ]);
        } else {
            $page = $this->project->wikiPages()->create([
                'title' => $this->title,
                'content' => $this->content,
                'user_id' => Auth::id(),
                'position' => $this->project->wikiPages()->count(),
            ]);
            $this->selectedPageId = $page->id;
        }

        $this->isEditing = false;
        $this->dispatch('notify', __('kanban-board::messages.notify_saved'));
    }

    public function deletePage($id)
    {
        $page = KanbanBoardWikiPage::findOrFail($id);
        $page->delete();

        if ($this->selectedPageId == $id) {
            $this->selectedPageId = null;
            $this->title = '';
            $this->content = '';

            $nextPage = $this->project->wikiPages()->first();
            if ($nextPage) {
                $this->selectPage($nextPage->id);
            }
        }
    }

    public function cancel()
    {
        if ($this->selectedPageId) {
            $this->selectPage($this->selectedPageId);
        } else {
            $this->isEditing = false;
        }
    }

    public function applyTemplate($templateId)
    {
        $template = KanbanBoardWikiTemplate::findOrFail($templateId);
        $this->content = $template->content;

        if (empty($this->title)) {
            $this->title = $template->name;
        }
    }

    public function saveAsTemplate()
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
        ]);

        KanbanBoardWikiTemplate::create([
            'project_id' => $this->isGlobalTemplate ? null : $this->project->id,
            'name' => $this->templateName,
            'content' => $this->content,
            'user_id' => Auth::id(),
        ]);

        $this->templateName = '';
        $this->isGlobalTemplate = false;
        $this->showTemplateModal = false;
        $this->dispatch('notify', __('kanban-board::messages.template_saved'));
    }

    public function deleteTemplate($templateId)
    {
        $template = KanbanBoardWikiTemplate::findOrFail($templateId);
        $template->delete();
        $this->dispatch('notify', __('kanban-board::messages.delete'));
    }

    public function render()
    {
        $templates = KanbanBoardWikiTemplate::where(function ($query) {
            $query->where('project_id', $this->project->id)
                ->orWhereNull('project_id');
        })->orderBy('name')->get();

        return view('kanban-board::livewire.kanban-board.wiki', [
            'templates' => $templates,
        ])
            ->title('Wiki - '.$this->project->title);
    }
}
