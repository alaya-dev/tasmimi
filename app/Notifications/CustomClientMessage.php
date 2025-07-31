<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomClientMessage extends Notification
{
    use Queueable;

    protected $subject;
    protected $messageContent;
    protected $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $messageContent, $senderName = 'فريق سامقة للتصميم')
    {
        $this->subject = $subject;
        $this->messageContent = $messageContent;
        $this->senderName = $senderName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('مرحباً!')
            ->line($this->messageContent)
            ->line('شكراً لك على استخدام منصة سامقة للتصميم.')
            ->salutation('مع أطيب التحيات، ' . $this->senderName);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->messageContent,
            'sender' => $this->senderName,
        ];
    }
}
