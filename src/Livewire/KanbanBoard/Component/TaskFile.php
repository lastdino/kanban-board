<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithFileUploads;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Flux\Flux;

class TaskFile extends Component
{
    use WithFileUploads;

    #[Reactive]
    public $taskId;

    public $files=[];
    public $up_files;


    public function mount()
    {
        $this->files=Task::find($this->taskId)->getMedia('task');
    }

    #[On('refresh-file')]
    #[On('show-modal')]
    public function getFiles(){
        $this->files=Task::find($this->taskId)->getMedia('task');
    }

    public function temporaryURL($id){
        $url = URL::temporarySignedRoute(
            config('kanban-board.routes.prefix'). '.download.signed',
            now()->addMinutes(5), // 10分間有効
            ['media' => $id]
        );
        return $url;
    }

    #[On('uploaded-file')]
    public function Save(){
        foreach ($this->up_files as $file){
            Task::find($this->taskId)->addMedia($file->getRealPath())
                ->usingName($file->getClientOriginalName())
                ->toMediaCollection('task');
        }
        $this->dispatch('refresh-file')->self();
        Flux::toast(variant: 'success', text: '登録しました。',);
    }

    public function delete($id){
        $mediaItem = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($id);
        $mediaItem->delete();
        $this->dispatch('refresh-file')->self();
        Flux::toast(variant: 'success', text: '削除しました。',);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.task-file');
    }
}
