<?php
namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KanbanBoardBadge extends Model
{
    protected $fillable = [
        'title',
        'color',
    ];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(KanbanBoardTask::class,'badge_task','badge_id','task_id');
    }
}
