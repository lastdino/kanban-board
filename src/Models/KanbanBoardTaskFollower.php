<?php

namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanBoardTaskFollower extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'is_reviewer',
    ];

    protected $casts = [
        'is_reviewer' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'user_id');
    }
}
