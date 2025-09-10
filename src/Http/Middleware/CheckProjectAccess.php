<?php

namespace Lastdino\KanbanBoard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lastdino\KanbanBoard\Models\KanbanBoardProject;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 認証チェック
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // boardIdパラメータを取得（URLパラメータまたはクエリパラメータ）
        $boardId = $request->route('boardId') ?? $request->get('boardId');

        if (! $boardId) {
            abort(400, 'プロジェクトIDが指定されていません。');
        }

        // プロジェクトの存在確認
        $project = KanbanBoardProject::find($boardId);
        if (! $project) {
            abort(404, 'プロジェクトが見つかりません。');
        }

        if ($project->is_private) {
            // 現在のユーザーがプロジェクトのメンバーかチェック
            $currentUserId = auth()->id();
            $isProjectMember = $project->users()->where('user_id', $currentUserId)->exists();

            if (! $isProjectMember) {
                abort(403, 'このプロジェクトにアクセスする権限がありません。');
            }
        }

        return $next($request);
    }
}
