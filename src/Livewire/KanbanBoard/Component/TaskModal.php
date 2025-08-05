<?php

namespace Lastdino\KanbanBoard\Livewire\KanbanBoard\Component;

use Illuminate\Database\Eloquent\Model;
use Lastdino\KanbanBoard\Livewire\KanbanBoard\Board;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Lastdino\KanbanBoard\Models\KanbanBoardTask as Task;
use Lastdino\KanbanBoard\Models\kanbanBoardCheckListItem as CheckListItem;
use Lastdino\KanbanBoard\Models\KanbanBoardBadge as Badge;
use Lastdino\KanbanBoard\Models\KanbanBoardProject as Project;
use Flux\Flux;

class TaskModal extends Component
{
    public $boardId;

    public $project;

    public $editing = false;

    public $task = [];
    public $title;
    public $description;
    public $start_date;
    public $due_date;
    public $reminder_at;
    public $label_color=null;
    public $assigned_user=null;
    public $checkItem;
    public $subTitle='';
    public $completed;

    public $new=false;
    public $columnId;

    public $search;

    public $tags=[];
    public $tag_name;
    public $search_tag;
    public $tag_color;

    public $review_user=[];
    public $follow_user=[];
    public $name;



    public function mount()
    {
    }

    #[Computed]
    public function MainTask(){
        if(!empty($this->task)){
            return Task::query()
                ->whereHas('column', function ($query) {
                    $query->where('board_id', $this->boardId);
                })
                ->with(['column', 'badges']) // 必要に応じてリレーションを追加
                ->orderBy('position')
                ->where('parent_id',null)
                ->where('id','<>',$this->task->id)
                ->tap(fn ($query) => $this->search ? $query->where('title','LIKE', '%' . $this->search . '%') : $query)
                ->get();
        }
        return [];
    }

    #[Computed]
    public function Badges(){
        if(!empty($this->task)){
            return Badge::query()
                ->where('board_id', $this->boardId)
                ->tap(fn ($query) => $this->search_tag ? $query->where('title','LIKE', '%' . $this->search_tag . '%') : $query)
                ->tap(fn ($query) => $this->tag_name ? $query->where('title','LIKE', '%' . $this->tag_name . '%') : $query)
                ->get();
        }
        return collect();
    }

    #[Computed]
    public function Users(){
        if(!empty($this->task)){
            return Project::find($this->boardId)->users()
                ->tap(fn ($query) => $this->name ? $query->where('name','LIKE', '%' . $this->name . '%') : $query)
                ->get();
        }
        return collect();
    }

    #[Computed]
    public function notInReviewers(){
        return $this->Users->whereNotIn('id', $this->task->reviewers->pluck('id'));
    }
    #[Computed]
    public function notInFollowers(){
        return $this->Users->whereNotIn('id', $this->task->followers->pluck('id'));
    }
    #[Computed]
    public function notInBadges(){
        return $this->Badges->whereNotIn('id', $this->task->badges->pluck('id'));
    }

    public function openTagModal(){
        $this->search_tag=null;
        $this->reset('tags');
        $this->tag_color=null;
        Flux::modal('edit-tags')->show();
    }
    public function openReviewerModal(){
        $this->name=null;
        $this->reset('review_user');
        Flux::modal('edit-reviewer')->show();
    }
    public function openFollowerModal(){
        $this->name=null;
        $this->reset('follow_user');
        Flux::modal('edit-follower')->show();
    }


    #[On('show-modal')]
    public function show($id){
        $this->new=false;
        if($id){
            $this->task=Task::with(['badges','subtasks','checklistItems','comments'])->find($id);
            $this->project=$this->task->column->board;
            $this->title = $this->task->title;
            $this->description = $this->task->description;
            $this->start_date = $this->task->start_date?->format('Y-m-d');
            $this->due_date = $this->task->due_date?->format('Y-m-d');
            $this->reminder_at = $this->task->reminder_at?->format('Y-m-d');
            $this->label_color = $this->task->label_color;
            $this->assigned_user = $this->task->assignedUser;
            $this->completed = $this->task->is_completed;
        }
        Flux::modal('edit-task')->show();
    }

    #[On('show-new-modal')]
    public function new($columnId){
        $this->columnId=$columnId;
        $this->new=true;
        $this->reset('title','description','label_color');
        Flux::modal('edit-task')->show();
    }

    public function updated($property){
        if(!$this->new){
            match ($property) {
                'description' => $this->task->update(['description' => $this->description]),
                'start_date' => $this->task->update(['start_date' => $this->start_date]),
                'due_date' => $this->task->update(['due_date' => $this->due_date]),
                'reminder_at' => $this->task->update(['reminder_at' => $this->reminder_at]),
                'assigned_user' => $this->task->update(['assigned_user_id' => $this->assigned_user]),
                'completed' => $this->TaskCompleted(),
                default => null,
            };

            $this->dispatch('refresh-columns')->to(Board::class);
        }
    }

    public function createTask(){
        $this->validate([
            'title' => 'required',
            'description' => 'nullable|max:1000',
        ]);

        $maxPosition = Task::where('column_id', $this->columnId)->max('position') ?? 0;

        $task = Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'column_id' => $this->columnId,
            'position' => $maxPosition + 1,
            'label_color' => $this->label_color,
            'created_user_id'=>auth()->id(),
            'assigned_user_id'=>auth()->id(),
        ]);

        $this->dispatch('refresh-columns')->to(Board::class);
        Flux::modals()->close();
    }


    public function TaskCompleted(){
        $oldPosition = $this->task->position;

        $this->task->update(['is_completed' => $this->completed]);

        if ($this->completed) {
            // タスクが完了した場合：完了済みタスクの一番下に移動
            $maxCompletedPosition = Task::where('column_id', $this->task->column_id)
                ->where('is_completed', true)
                ->max('position') ?? 0;

            $newPosition = $maxCompletedPosition + 1;

            // 完了したタスクより上にある未完了タスクを上に詰める
            Task::where('column_id', $this->task->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', false)
                ->decrement('position');

        } else {
            // タスクが未完了に戻された場合：未完了タスクの一番下に移動
            $maxUncompletedPosition = Task::where('column_id', $this->task->column_id)
                ->where('is_completed', false)
                ->max('position') ?? 0;

            $newPosition = $maxUncompletedPosition + 1;

            // 未完了に戻されたタスクより下にある完了済みタスクを下にずらす
            Task::where('column_id', $this->task->column_id)
                ->where('position', '>', $oldPosition)
                ->where('is_completed', true)
                ->increment('position');
        }

        // タスクの位置を更新
        $this->task->update(['position' => $newPosition]);
    }

    public function startEditing()
    {
        $this->editing = true;
    }

    public function titleSave()
    {
        $this->editing = false;

        $this->task->update(['title'=>$this->title]);

        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function moveCheckListItemToPosition($ItemId,$newPosition){
        $CheckListItem = CheckListItem::find($ItemId);

        if ($CheckListItem->position < $newPosition) {
            // 下に移動：間のColumnを上に詰める
            CheckListItem::where('task_id', $CheckListItem->task_id)
                ->where('position', '>', $CheckListItem->position)
                ->where('position', '<=', $newPosition)
                ->where('id', '!=', $ItemId)
                ->decrement('position');
        } else {
            // 上に移動：間のColumnを下にずらす
            CheckListItem::where('task_id', $CheckListItem->task_id)
                ->where('position', '>=', $newPosition)
                ->where('position', '<', $CheckListItem->position)
                ->where('id', '!=', $ItemId)
                ->increment('position');
        }

        $CheckListItem->update(['position' => $newPosition]);
    }

    public function moveSubTaskItemToPosition($ItemId,$newPosition){
        $SubTaskItem = Task::find($ItemId);

        if ($SubTaskItem->sub_position < $newPosition) {
            // 下に移動：間のColumnを上に詰める
            Task::where('parent_id', $SubTaskItem->parent_id)
                ->where('sub_position', '>', $SubTaskItem->sub_position)
                ->where('sub_position', '<=', $newPosition)
                ->where('id', '!=', $ItemId)
                ->decrement('sub_position');
        } else {
            // 上に移動：間のColumnを下にずらす
            Task::where('parent_id', $SubTaskItem->parent_id)
                ->where('sub_position', '>=', $newPosition)
                ->where('sub_position', '<', $SubTaskItem->sub_position)
                ->where('id', '!=', $ItemId)
                ->increment('sub_position');
        }
        $SubTaskItem->update(['sub_position' => $newPosition]);
    }

    public function AddSubTaskItem(){
        Task::create([
            'title'=>$this->subTitle,
            'description'=>'',
            'column_id'=>$this->task->column_id,
            'position'=>Task::where('column_id', $this->task->column_id)->max('position') ?? 0,
            'sub_position'=>$this->task->subtasks->count(),
            'parent_id'=>$this->task->id,
            'created_user_id'=>auth()->id(),
            'assigned_user_id'=>auth()->id(),
        ]);
        $this->subTitle = '';
        $this->refreshTask();
    }

    public function AddCheckItem(){
        $this->task->checklistItems()->create([
            'content' => $this->checkItem,
            'is_completed' => false,
            'position' => $this->task->checklistItems->count(),
        ]);
        $this->checkItem = '';
        $this->refreshTask();
    }

    public function DeleteCheckItem($id){
        $db=CheckListItem::find($id);

        $taskId = $db->task_id;
        $position = $db->position;
        $db->delete();
        CheckListItem::where('task_id', $taskId)
            ->where('position', '>', $position)
            ->decrement('position');

        $this->refreshTask();
    }

    public function DeleteSubTask($id){
        $db=Task::find($id);
        $columnId = $db->column_id;
        $sub_position = $db->sub_position;
        $position = $db->position;
        $parentId=$db->parent_id;

        $db->delete();

        Task::where('parent_id', $parentId)
            ->where('sub_position', '>', $sub_position)
            ->where('id', '!=', $id)
            ->decrement('sub_position');
        Task::where('column_id', $columnId)
            ->where('position', '>', $position)
            ->where('id', '!=', $id)
            ->decrement('position');

        $this->refreshTask();
    }

    public function unlinkMainTask($id){
        $db=Task::find($id);
        $parentId=$db->parent_id;
        $sub_position = $db->sub_position;

        Task::where('parent_id', $parentId)
            ->where('sub_position', '>', $sub_position)
            ->where('id', '!=', $id)
            ->decrement('sub_position');

        $db->update([
            'parent_id' => null,
            'sub_position' => null
        ]);

        $this->refreshTask();
    }

    public function changeMainTask($parentId,$taskId=null)
    {
        if($taskId == null){
            $taskId=$this->task->id;
        }
        $db=Task::find($taskId);
        $old_parentId=$db->parent_id;
        $sub_position = $db->sub_position;

        if($old_parentId != null){
            Task::where('parent_id', $old_parentId)
                ->where('sub_position', '>', $sub_position)
                ->where('id', '!=', $taskId)
                ->decrement('sub_position');
        }

        $parent=Task::find($parentId);

        $db->update([
            'sub_position'=>$parent->subtasks->count(),
            'parent_id' =>$parentId
        ]);

        $this->refreshTask();
    }


    public function setLabelColor($color)
    {
        if($this->label_color == $color){
            $this->label_color = null;
        }else{
            $this->label_color = $color;
        }
        if(!$this->new){
            $this->task->update(['label_color'=>$this->label_color]);
            $this->dispatch('refresh-columns')->to(Board::class);
        }
    }

    public function removeBadge($badgeId){
        $this->task->badges()->detach($badgeId);
        $this->refreshTask();
    }
    public function addBadge(){
        $this->task->badges()->attach($this->tags);
        $this->reset('tags');
        $this->refreshTask();
        Flux::modal('edit-tags')->close();
    }


    public function setTagColor($color)
    {
        if($this->tag_color == $color){
            $this->tag_color = null;
        }else{
            $this->tag_color = $color;
        }
    }

    public function addNewBadge(){
        $badge=Badge::create([
            'title'=>$this->tag_name,
            'board_id'=>$this->boardId,
            'color' => $this->tag_color,
        ]);
        $this->task->badges()->attach($badge->id);
        $this->refreshTask();
        $this->tags=$this->task->badges->pluck('id');
        $this->reset('tag_name');
        Flux::modal('add-tag')->close();
    }

    public function removeReviewer($userId){
        $this->task->reviewers()->detach($userId);
        $this->refreshTask();
    }
    public function addReviewer(){
        $this->task->reviewers()->attach($this->review_user,['is_reviewer' => true]);
        $this->reset('review_user');
        $this->refreshTask();
        Flux::modal('edit-reviewer')->close();
    }

    public function removeFollower($userId){
        $this->task->followers()->detach($userId);
        $this->refreshTask();
    }
    public function addFollower(){
        $this->task->followers()->attach($this->follow_user);
        $this->reset('follow_user');
        $this->refreshTask();
        Flux::modal('edit-follower')->close();
    }

    public function refreshTask(){
        $this->task->refresh();
        $this->dispatch('refresh-columns')->to(Board::class);
    }

    public function render()
    {
        return view('kanban-board::livewire.kanban-board.component.task-modal');
    }
}
