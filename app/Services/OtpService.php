<?php

namespace App\Services;

use App\Jobs\SendOTP;
use App\Models\User;

class OtpService
{

    public function generate(int $length = 6): string
    {
        return generateOtp($length);
    }

    public function issue(User $user, int $ttlMinutes = 10): string
    {
        $code = $this->generate();

        $user->forceFill([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes($ttlMinutes),
        ])->save();

        SendOTP::dispatch($user, $code);

        return $code;
    }

    public function verify(User $user, string $code): bool
    {
        return $user->otp_code === $code
            && $user->otp_expires_at
            && now()->lessThanOrEqualTo($user->otp_expires_at);
    }

    public function clear(User $user): void
    {
        $user->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();
    }
}