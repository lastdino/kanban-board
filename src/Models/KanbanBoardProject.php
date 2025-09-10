<?php

namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanBoardProject extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(KanbanBoardColumn::class, 'board_id')->orderBy('position');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(KanbanBoardBadge::class, 'board_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(config('kanban-board.users_model'), 'user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('kanban-board.users_model'), 'kanban_board_project_user', 'project_id', 'user_id');
    }

    public function NotInvitedUsers()
    {
        return config('kanban-board.users_model')::whereNotIn('id', $this->users->pluck('id'))->get();
    }
}
