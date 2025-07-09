<?php
namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanBoardTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'column_id',
        'position',
        'sub_position',
        'assigned_user_id',
        'parent_id',
        'start_date',
        'due_date',
        'reminder_at',
        'label_color',
        'is_completed',
        'created_user_id',

    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'reminder_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    protected $withCount = [
        'checklistItems',
        'completedChecklistItems',
    ];

    protected function initializeKanbanBoardTask()
    {
        // コメントテーブルが存在する場合のみwithCountに追加
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kanban_board_comments')) {
                $this->withCount[] = 'comments';
            }
        } catch (\Exception $e) {
            // テーブル確認中にエラーが発生した場合は無視
        }
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardColumn::class, 'column_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(KanbanBoardBadge::class,'kanban_board_badge_task','task_id','badge_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'assigned_user_id');
    }
    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'created_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardTask::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(KanbanBoardTask::class, 'parent_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(KanbanBoardChecklistItem::class, 'task_id');
    }

    public function completedChecklistItems(): HasMany
    {
        return $this->hasMany(KanbanBoardChecklistItem::class, 'task_id')->where('is_completed', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(KanbanBoardComment::class, 'task_id')->orderBy('created_at', 'desc');
    }

    // コメント数を取得するためのアクセサ
    public function getCommentsCountAttribute()
    {
        // テーブルが存在するかチェック
        try {
            return $this->comments()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(config('kanban-board.users_model'), 'kanban_board_task_followers', 'task_id', 'user_id')
            ->withPivot('is_reviewer')
            ->withTimestamps();
    }

    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(config('kanban-board.users_model'), 'kanban_board_task_followers', 'task_id', 'user_id')
            ->withPivot('is_reviewer')
            ->wherePivot('is_reviewer', true)
            ->withTimestamps();
    }

    public function isSubtask(): bool
    {
        return $this->parent_id !== null;
    }

    public function hasSubtasks(): bool
    {
        return $this->subtasks()->count() > 0;
    }

    public function hasChecklist(): bool
    {
        return $this->checklist_items_count > 0;
    }

    public function getChecklistCompletedAttribute(): bool
    {
        if ($this->checklist_items_count === 0) {
            return false;
        }

        return $this->completed_checklist_items_count === $this->checklist_items_count;
    }

    public function isDueSoon(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return now()->diffInDays($this->due_date, false) <= 3 && now()->diffInDays($this->due_date, false) >= 0;
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return now()->gt($this->due_date);
    }
}
