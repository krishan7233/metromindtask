<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskStatusUpdated extends Notification
{
    use Queueable;

 

    protected $task;
    protected $user;

    public function __construct(Task $task, $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // Structure the data to be stored in the database notification
    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'completed_by' => $this->user->name,
            'completed_by_id' => $this->user->id,
            'status' => $this->task->completed == 1 ? 'completed' : 'pending',
            'message' => "Task '{$this->task->title}' was marked as completed by {$this->user->name}"

        ];
    }
}
