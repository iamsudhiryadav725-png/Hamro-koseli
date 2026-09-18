<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use App\Mail\VendorApproved;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected string $previousStatus = '';

    protected function beforeSave(): void
    {
        $this->previousStatus = $this->record->getOriginal('status') ?? $this->record->status;
    }

    protected function afterSave(): void
    {
        $vendor = $this->record;

        if ($this->previousStatus !== 'active' && $vendor->status === 'active') {
            try {
                Mail::to($vendor->email)->send(new VendorApproved($vendor));
            } catch (\Throwable $e) {
                \Log::error("Failed to send approval email to vendor {$vendor->email}: ".$e->getMessage());
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    if ($this->record->orderItems()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Vendor')
                            ->body('This vendor has existing order items and cannot be deleted. Suspend them instead.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
