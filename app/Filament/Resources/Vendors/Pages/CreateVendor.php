<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function afterCreate(): void
    {
        $vendor = $this->record;
        if ($vendor->user && method_exists($vendor->user, 'assignRole')) {
            $vendor->user->assignRole('vendor');
        }
    }
}
