<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'aashutosbaral@gmail.com'],
            [
                'name' => 'Aashutos',
                'password' => Hash::make('1234567890'),
                'role' => 'admin',
                'is_active' => 1,
            ]
        );
        $admin->assignRole('admin');

        // Create a vendor user account
        $vendor = User::firstOrCreate(
            ['email' => 'example@gmail.com'],
            [
                'name' => 'Aashutosh',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'is_active' => 1,
            ]
        );
        $vendor->assignRole('vendor');

        // Create or update the vendor profile for this user
        Vendor::updateOrCreate(
            ['user_id' => $vendor->id],
            [
                'vendor_name' => 'Aashutosh',
                'owner_name' => 'Aashutosh',
                'email' => 'example@gmail.com',
                'phone' => '9876543210',
                'vendor_address' => 'Default vendor address',
                'city' => 'Kathmandu',
                'province' => 'Bagmati',
                'status' => 'active',
            ]
        );

        // Seed default deal countdown time
        Setting::updateOrCreate(
            ['key' => 'todays_deal_ends_at'],
            ['value' => now()->addHours(24)->toDateTimeString()]
        );

        // Seed default deal background image setting
        Setting::updateOrCreate(
            ['key' => 'deal_countdown_bg_image'],
            ['value' => null]
        );
    }
}
