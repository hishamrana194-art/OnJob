<?php

namespace App\Notifications;

use App\Models\EmailVerificationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailVerificationToken $verificationToken;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmailVerificationToken $verificationToken)
    {
        $this->verificationToken = $verificationToken;
    }

    /**
     * Get the notification's delivery channels.
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
        $verificationUrl = config('app.frontend_url', config('app.url')) 
            . '/verify-email?token=' . $this->verificationToken->token;

        return (new MailMessage)
            ->subject('Verify Your Email Address - OnJob')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering with OnJob.')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This verification link will expire in 24 hours.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best regards, The OnJob Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->verificationToken->token,
            'expires_at' => $this->verificationToken->expires_at,
        ];
    }
}

