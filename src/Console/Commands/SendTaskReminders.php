<?php

namespace Lastdino\KanbanBoard\Console\Commands;

use Illuminate\Console\Command;
use Lastdino\KanbanBoard\Models\KanbanBoardTask;
use Lastdino\KanbanBoard\Notifications\TaskReminderNotification;

class SendTaskReminders extends Command
{
    protected $signature = 'kanban:send-reminders';

    protected $description = 'タスクのリマインダー通知を送信します';

    public function handle()
    {
        $tasks = KanbanBoardTask::needsReminder();

        if ($tasks->isEmpty()) {
            // $this->info('送信するリマインダーはありません。');
            return;
        }

        foreach ($tasks as $task) {
            // 担当者に通知を送信
            if ($task->assignedUser) {
                $task->assignedUser->notify(new TaskReminderNotification($task));
            }

            // フォロワーにも通知を送信（必要に応じて）
            foreach ($task->followers as $follower) {
                $follower->notify(new TaskReminderNotification($task));
            }

            // リマインダー送信済みとしてマーク
            $task->markReminderSent();

            // $this->info("タスク「{$task->title}」のリマインダーを送信しました。");
        }

        // $this->info("合計 {$tasks->count()} 件のリマインダーを送信しました。");
    }
}
