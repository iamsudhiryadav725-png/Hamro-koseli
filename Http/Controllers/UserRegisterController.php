<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserRegisterController extends Controller
{
    /**
     * Handle registration for a regular (buyer) account.
     */
    public function register(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:150', 'unique:users,email'],
                'phone' => ['nullable', 'digits:10', 'unique:users,phone'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[\\^$*.\\[\\]{}()?\\-"!@#%&\/\\\\,><\'\':;|_~`+\\=]/',
                ],
            ], [
                'phone.digits' => 'Phone number must be exactly 10 digits.',
                'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'is_active' => true,
            ]);

            // Ensure the 'user' role exists even if seeders haven't been run.
            // This prevents a RoleDoesNotExist exception (500 error) on fresh installs.
            Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
            $user->assignRole('user');

            return redirect()->route('userlogin')
                ->with('success', 'Account created successfully! Please sign in to continue.');

        } catch (ValidationException $e) {
            // Redirect back with errors and flag to show register view
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_register', true);
        }
    }
}
