<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Setting Key')
                    ->helperText('Type "_image", "bg", "logo", "icon", or "pic" in the key name to upload an image. Type "_at", "date", or "time" for a Date/Time picker. Otherwise, it will be a standard text input.')
                    ->required()
                    ->disabled(fn ($record) => $record !== null)
                    ->live(),

                DateTimePicker::make('value')
                    ->label('Setting Value (Date/Time)')
                    ->required()
                    ->placeholder('Select target date/time')
                    ->displayFormat('Y-m-d H:i:s')
                    ->timezone(config('app.timezone'))
                    ->native(false)
                    ->minDate(now())
                    ->visible(fn (Get $get) => Str::contains($get('key') ?? '', ['date', 'time', '_at']))
                    ->dehydrated(fn (Get $get) => Str::contains($get('key') ?? '', ['date', 'time', '_at'])),

                FileUpload::make('value')
                    ->label('Setting Value (Image Upload)')
                    ->required()
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->visible(fn (Get $get) => ! Str::contains($get('key') ?? '', ['date', 'time', '_at']) && Str::contains($get('key') ?? '', ['image', 'bg', 'logo', 'icon', 'pic']))
                    ->dehydrated(fn (Get $get) => ! Str::contains($get('key') ?? '', ['date', 'time', '_at']) && Str::contains($get('key') ?? '', ['image', 'bg', 'logo', 'icon', 'pic'])),

                TextInput::make('value')
                    ->label('Setting Value (Text)')
                    ->required()
                    ->visible(fn (Get $get) => $get('key') !== null && ! Str::contains($get('key') ?? '', ['date', 'time', '_at', 'image', 'bg', 'logo', 'icon', 'pic']))
                    ->dehydrated(fn (Get $get) => $get('key') !== null && ! Str::contains($get('key') ?? '', ['date', 'time', '_at', 'image', 'bg', 'logo', 'icon', 'pic'])),
            ]);
    }
}
