<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
    ) {}

    public function handle(): void
    {
        $notification = new OtpVerificationNotification($this->code);

        $this->user->notify($notification);
    }
}