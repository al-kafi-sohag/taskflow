<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetOtpController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPasswordOtp', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function store(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! $otp->verify($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'That code is invalid or has expired.',
            ]);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();
        $otp->clear($user);

        return redirect()->route('login')->with('status', 'Your password has been reset — sign in with your new password.');
    }
}