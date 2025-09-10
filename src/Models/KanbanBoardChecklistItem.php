<?php

namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanBoardChecklistItem extends Model
{
    protected $fillable = [
        'task_id',
        'content',
        'is_completed',
        'position',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'position' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardTask::class, 'task_id');
    }
}
