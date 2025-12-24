<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard;

use Illuminate\Support\Facades\URL;
use Lastdino\KanbanBoard\Models\KanbanBoardProject;
use Lastdino\KanbanBoard\Models\KanbanBoardTask;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileList extends Component
{
    use WithPagination;

    public $projectId;

    public function mount($boardId = null)
    {
        $this->projectId = $boardId ?? request()->query('boardId');
    }

    public function getProjectProperty()
    {
        return KanbanBoardProject::findOrFail($this->projectId);
    }

    public function download($mediaId)
    {
        return redirect()->to(URL::temporarySignedRoute(
            config('kanban-board.routes.prefix').'.download.signed',
            now()->addMinutes(10),
            ['media' => $mediaId]
        ));
    }

    #[Title('Files')]
    public function render()
    {
        // プロジェクトに属する全タスクのIDを取得
        $taskIds = KanbanBoardTask::whereHas('column', function ($query) {
            $query->where('board_id', $this->projectId);
        })->pluck('id');

        // タスクに関連付けられたメディアをページネーション付きで取得
        $files = Media::where('model_type', KanbanBoardTask::class)
            ->whereIn('model_id', $taskIds)
            ->latest()
            ->paginate(20);

        return view('kanban-board::livewire.kanban-board.file-list', [
            'files' => $files,
            'project' => $this->project,
        ])->title(__('kanban-board::messages.file_list'));
    }
}
