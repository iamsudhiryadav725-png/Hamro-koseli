<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view orders', 'manage orders',
            'view products', 'manage products',
            'view users', 'manage users',
            'view vendors', 'manage vendors',
            'view payments', 'manage payments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $vendor = Role::firstOrCreate(['name' => 'vendor']);
        $vendor->syncPermissions([
            'view products', 'manage products',
            'view orders',
        ]);
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions(['view products', 'view orders']);
    }
}
