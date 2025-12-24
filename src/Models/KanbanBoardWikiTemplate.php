<?php

namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanBoardWikiTemplate extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'content',
        'user_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardProject::class, 'project_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'user_id');
    }
}
