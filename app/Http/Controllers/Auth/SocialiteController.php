<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;

class SocialiteController extends Controller
{
    public function __construct(
        private OtpService $otpService
    ) {
    }

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('google_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->forceFill([
                    'google_id' => $socialUser->getId(),
                ])->save();
            }
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'google_id' => $socialUser->getId(),
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        Auth::login($user, remember: true);

        if (! $user->hasVerifiedEmail()) {
            $this->otpService->issue($user);

            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('d.index', absolute: false));
    }
}
