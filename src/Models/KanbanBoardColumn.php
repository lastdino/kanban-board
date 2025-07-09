<?php
namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanBoardColumn extends Model
{
    protected $fillable = [
        'title',
        'board_id',
        'position',
        'color',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardProject::class,'board_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(KanbanBoardTask::class, 'column_id')->orderBy('position');
    }
}
