<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorPasswordResetController extends Controller
{
    // ─── 1. Show Forgot Password form ────────────────────────────────────────
    public function showForgotForm()
    {
        return view('seller.forgot-password');
    }

    // ─── 2. Send reset link -only if the email belongs to a vendor ──────────
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Extra check: email must belong to an active vendor account
        $user = User::where('email', $request->email)->first();

        if (! $user || ! $user->vendor) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'No vendor account found with that email address.']);
        }

        if ($user->vendor->status !== 'active') {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Your vendor account is not active. Please contact support.']);
        }

        // Laravel's built-in broker handles token + email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'We\'ve sent a password reset link to your email. Check your inbox!');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput();
    }

    // ─── 3. Show Reset Password form (from email link) ────────────────────────
    public function showResetForm(Request $request, string $token)
    {
        return view('seller.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // ─── 4. Save new password ─────────────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[\^$*.\[\]{}()?\-"!@#%&\/\\,><\':;|_~`+=]/',
            ],
        ], [
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('seller.login')
                ->with('success', 'Password reset successfully. Please sign in with your new password.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
}
