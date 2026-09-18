<?php

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Ensure the Spatie roles that the app expects actually exist in the test DB.
 * RefreshDatabase wipes them on every test, so we re-create them here.
 */
function ensureRolesExist(): void
{
    foreach (['user', 'vendor', 'admin'] as $name) {
        Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => $name, 'guard_name' => 'vendor']);
    }
}

/**
 * Create a regular buyer user with the given attribute overrides.
 */
function makeUser(array $attrs = []): User
{
    ensureRolesExist();

    $user = User::factory()->create(array_merge([
        'role' => 'user',
        'is_active' => true,
        'password' => Hash::make('password'),
    ], $attrs));

    $user->assignRole('user');

    return $user;
}

/**
 * Create a vendor user + matching Vendor profile.
 */
function makeVendor(array $userAttrs = [], array $vendorAttrs = []): User
{
    ensureRolesExist();

    $user = User::factory()->create(array_merge([
        'role' => 'vendor',
        'is_active' => true,
        'password' => Hash::make('password'),
    ], $userAttrs));

    $user->assignRole('vendor');

    Vendor::create(array_merge([
        'user_id' => $user->id,
        'vendor_name' => 'Test Shop',
        'owner_name' => $user->name,
        'email' => $user->email,
        // Unique per test — vendors table has a unique index on phone.
        'phone' => '98'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
        'status' => 'active',
    ], $vendorAttrs));

    return $user;
}

// ===========================================================================
// BUYER LOGIN  (POST /userlogin)
// ===========================================================================

describe('Buyer login', function () {

    it('redirects to home on valid credentials', function () {
        $user = makeUser();

        $this->post('/userlogin', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    });

    it('stays on login with an error for wrong password', function () {
        $user = makeUser();

        $this->post('/userlogin', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('stays on login with an error for unknown email', function () {
        $this->post('/userlogin', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('rejects an inactive user and logs them out', function () {
        $user = makeUser(['is_active' => false]);

        $this->post('/userlogin', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('requires email field', function () {
        $this->post('/userlogin', ['password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('requires password field', function () {
        $user = makeUser();

        $this->post('/userlogin', ['email' => $user->email])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    });

    it('redirects a vendor user to home (vendors can shop as buyers)', function () {
        $user = makeVendor();

        $this->post('/userlogin', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    });

    it('rejects an admin user attempting buyer login', function () {
        $admin = makeUser(['role' => 'admin']);

        $this->post('/userlogin', [
            'email' => $admin->email,
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

});

// ===========================================================================
// SELLER LOGIN  (POST /seller-login)
// ===========================================================================

describe('Seller login', function () {

    it('redirects to dashboard on valid vendor credentials', function () {
        $user = makeVendor();

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard'));

        // Vendor login uses the 'vendor' guard, not the default 'web' guard.
        $this->assertAuthenticatedAs($user, 'vendor');
    });

    it('rejects a non-vendor user attempting seller login', function () {
        $user = makeUser();   // role = 'user', not 'vendor'

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest('vendor');
    });

    it('rejects a vendor whose account is pending approval', function () {
        $user = makeVendor(vendorAttrs: ['status' => 'pending']);

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('seller.login'))
            ->assertSessionHas('error');

        $this->assertGuest('vendor');
    });

    it('rejects a suspended vendor', function () {
        $user = makeVendor(vendorAttrs: ['status' => 'suspended']);

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('seller.login'))
            ->assertSessionHas('error');

        $this->assertGuest('vendor');
    });

    it('rejects an inactive user account even with correct password', function () {
        $user = makeVendor(userAttrs: ['is_active' => false]);

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest('vendor');
    });

    it('stays on seller login for wrong password', function () {
        $user = makeVendor();

        $this->post('/seller-login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest('vendor');
    });

});

// ===========================================================================
// LOGOUT
// ===========================================================================

describe('Logout', function () {

    it('logs the buyer out and invalidates the session', function () {
        $user = makeUser();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    });

    it('logs the seller out and redirects to seller login', function () {
        $user = makeVendor();

        // Authenticate via the vendor guard (matches how seller login works).
        $this->actingAs($user, 'vendor')
            ->post('/seller-logout')
            ->assertRedirect(route('seller.login'));

        // Only the vendor guard session should be cleared.
        $this->assertGuest('vendor');
    });

    it('a guest hitting logout is redirected without error', function () {
        // Laravel redirects unauthenticated POST /logout to /login by default,
        // but our app defines a named route — just assert it does not 500.
        $this->post('/logout')
            ->assertRedirect();
    });

});
