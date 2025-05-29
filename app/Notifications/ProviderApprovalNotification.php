<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProviderApprovalNotification extends Notification
{
    use Queueable;

    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Send as both mail and in-app notification
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = ($this->status === 'approved')
            ? 'Congratulations! Your provider account has been approved.'
            : 'Sorry, your provider registration has been rejected.';

        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($message)
            ->action('Login to your account', url('/login'))
            ->line('Thank you for using our application.');
    }

    /**
     * Get the array representation of the notification (for database storage).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => ($this->status === 'approved')
                ? 'Your provider account has been approved.'
                : 'Your provider account has been rejected.',
        ];
    }
}
