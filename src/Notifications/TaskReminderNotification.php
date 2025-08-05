<?php

namespace Lastdino\KanbanBoard\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Lastdino\KanbanBoard\Models\KanbanBoardTask;

class TaskReminderNotification extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(KanbanBoardTask $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('タスクリマインダー: ' . $this->task->title)
            ->line('以下のタスクのリマインダー日時になりました。')
            ->line('タスク名: ' . $this->task->title)
            ->line('説明: ' . $this->task->description)
            ->line('期限: ' . ($this->task->due_date ? $this->task->due_date->format('Y-m-d H:i') : '未設定'))
            ->action('タスクを確認', url('/kanban-board/task/' . $this->task->id))
            ->line('お忙しい中恐れ入りますが、ご確認をお願いいたします。');
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => 'タスク「' . $this->task->title . '」のリマインダー日時になりました。',
        ];
    }
}
