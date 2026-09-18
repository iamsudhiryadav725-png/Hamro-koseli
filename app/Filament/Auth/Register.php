<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        $user->forceFill([
            'role' => 'admin',
            'is_active' => true,
        ])->save();

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }

        return $user;
    }
}
