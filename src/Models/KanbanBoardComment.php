<?php

namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanBoardComment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'reply_id',
        'content',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardTask::class, 'task_id');
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardComment::class, 'reply_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'user_id');
    }
}
