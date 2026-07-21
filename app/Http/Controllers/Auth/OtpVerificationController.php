<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpVerificationController extends Controller
{
    public function store(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if (! $otp->verify($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'That code is invalid or has expired.',
            ]);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $otp->clear($user);

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}