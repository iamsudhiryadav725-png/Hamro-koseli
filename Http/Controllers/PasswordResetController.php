<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // ─── 1. Show Forgot Password form (standalone page) ──────────────────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ─── 2. Send the reset link ───────────────────────────────────────────────
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'We could not find an account with that email address.',
        ]);

        if ($validator->fails()) {
            // JSON response for the AJAX call inside the modal
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Laravel's built-in password broker handles token creation + email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => __($status),
                ]);
            }

            return back()->with('status', __($status));
        }

        // Too many requests or other broker error
        if ($request->expectsJson()) {
            return response()->json([
                'errors' => ['email' => [__($status)]],
                'message' => __($status),
            ], 422);
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput();
    }

    // ─── 3. Show Reset Password form (link from email) ────────────────────────
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // ─── 4. Handle new password submission ───────────────────────────────────
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
            // Redirect to login with a success flash that auto-opens the modal
            return redirect()
                ->route('userlogin')
                ->with('success', 'Your password has been reset successfully. Please sign in.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
}
