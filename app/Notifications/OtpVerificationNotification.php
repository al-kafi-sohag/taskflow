<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your TaskFlow verification code')
            ->view('emails.otp-verification', [
                'code' => $this->code,
                'name' => $notifiable->name ?? null,
                'expiresInMinutes' => 10,
            ]);
    }
}
