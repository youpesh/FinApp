<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $daysUntilExpiry)
    {
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->daysUntilExpiry === 0
            ? 'Your password expires today!'
            : "Your password will expire in {$this->daysUntilExpiry} day(s).";

        return (new MailMessage)
            ->subject('Password Expiry Warning')
            ->line($message)
            ->line('Please change your password to maintain access to your account.')
            ->action('Change Password', url('/password/change'))
            ->line('Thank you for using FinApp!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'days_until_expiry' => $this->daysUntilExpiry,
            'message' => $this->daysUntilExpiry === 0
                ? 'Your password expires today!'
                : "Your password will expire in {$this->daysUntilExpiry} day(s).",
        ];
    }
}
