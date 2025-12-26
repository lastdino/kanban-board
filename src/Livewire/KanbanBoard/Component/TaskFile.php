<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Flux\Flux;
use Illuminate\Support\Facades\URL;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskFile extends Component
{
    use WithFileUploads;

    #[Reactive]
    public $taskId;

    public $files = [];

    public $up_files;

    public $project;

    public function mount()
    {
        $this->files = Task::find($this->taskId)->getMedia('task');
        $this->project = Task::find($this->taskId)->column->board;
    }

    #[On('refresh-file')]
    #[On('show-modal')]
    public function getFiles()
    {
        $this->files = Task::find($this->taskId)->getMedia('task');
    }

    public function temporaryURL($id)
    {
        $url = URL::temporarySignedRoute(
            config('kanban-board.routes.prefix').'.download.signed',
            now()->addMinutes(5), // 10分間有効
            ['media' => $id]
        );

        return $url;
    }

    #[On('uploaded-file')]
    public function Save()
    {
        $task = Task::find($this->taskId);
        foreach ($this->up_files as $file) {
            $task->addMedia($file->getRealPath())
                ->usingName($file->getClientOriginalName())
                ->toMediaCollection('task');

            $task->comments()->create([
                'content' => __('kanban-board::messages.file_uploaded_comment', ['filename' => $file->getClientOriginalName()]),
                'user_id' => auth()->id(),
            ]);
        }
        $this->dispatch('refresh-file')->self();
        $this->dispatch('show-modal', id: $this->taskId);
        Flux::toast(variant: 'success', text: '登録しました。');
    }

    public function delete($id)
    {
        $mediaItem = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($id);
        $mediaItem->delete();
        $this->dispatch('refresh-file')->self();
        Flux::toast(variant: 'success', text: '削除しました。');
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.task-file');
    }
}
