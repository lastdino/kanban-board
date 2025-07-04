<?php
namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KanbanBoardTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'column_id',
        'position',
        'assigned_user_id',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(KanbanBoardColumn::class, 'column_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(KanbanBoardBadge::class,'badge_task','task_id','badge_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(config('kanban-bord.users_model'), 'assigned_user_id');
    }
}
