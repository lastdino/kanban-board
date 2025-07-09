<?php
namespace Lastdino\KanbanBoard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanBoardProject extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(KanbanBoardColumn::class,'board_id')->orderBy('position');
    }
}
