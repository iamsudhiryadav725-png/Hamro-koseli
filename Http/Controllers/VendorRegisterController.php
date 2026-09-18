<?php

namespace App\Http\Controllers;

use App\Mail\NewVendorRegistered;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class VendorRegisterController extends Controller
{
    public function show()
    {
        return view('auth.vendor-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[\\^$*.\\[\\]{}()?\\-"!@#%&\/\\\\,><\'\':;|_~`+\\=]/',
            ],
            'phone' => 'required|digits:10|unique:users,phone',
            'vendor_name' => 'required|string|max:150',
            'owner_name' => 'required|string|max:100',
            'vendor_email' => 'required|email|unique:vendors,email',
            'vendor_phone' => 'required|digits:10|unique:vendors,phone',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:80',
            'province' => 'nullable|string|max:80',
            'pan_number' => 'nullable|string|max:30|unique:vendors,pan_number',
        ], [
            'phone.digits' => 'Personal phone must be exactly 10 digits.',
            'vendor_phone.digits' => 'Shop phone must be exactly 10 digits.',
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Create the user and vendor records atomically. If vendor creation
        // fails for any reason (race-condition duplicate, DB error, etc.),
        // the whole thing rolls back instead of leaving an orphaned user
        // with a login but no vendor profile.
        $vendor = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'role' => 'vendor',
                'is_active' => true,
            ]);

            // Assign Spatie role
            $user->assignRole('vendor');

            // Auto-create vendor record. New vendors always start out
            // "pending" -they cannot log in to the seller dashboard
            // until an admin flips their status to "active" from the
            // admin panel (see Filament\Resources\Vendors\Pages\EditVendor).
            return $user->vendor()->create([
                'vendor_name' => $data['vendor_name'],
                'owner_name' => $data['owner_name'],
                'email' => $data['vendor_email'],
                'phone' => $data['vendor_phone'],
                'vendor_address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'pan_number' => $data['pan_number'] ?? null,
                'status' => 'pending',
            ]);
        });

        // Only notify admins once the registration has actually committed.
        $this->notifyAdmins($vendor);

        return redirect()->route('seller.login')
            ->with('success', 'Registration successful! Wait for admin approval. You will receive an email after approval.');
    }

    /**
     * Send a notification email to every admin about the new vendor.
     * Uses synchronous send (no queue worker required).
     */
    private function notifyAdmins(Vendor $vendor): void
    {
        $adminEmails = User::where('role', 'admin')->pluck('email');

        foreach ($adminEmails as $adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewVendorRegistered($vendor));
            } catch (\Throwable $e) {
                // Log the error but don't crash the registration flow
                \Log::error("Failed to send vendor notification to {$adminEmail}: ".$e->getMessage());
            }
        }
    }
}
