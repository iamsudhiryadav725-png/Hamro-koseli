<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique('vendors', 'user_id', ignoreRecord: true),
                TextInput::make('vendor_name')
                    ->required(),
                TextInput::make('owner_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique('vendors', 'email', ignoreRecord: true),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->regex('/^[0-9]{10}$/')
                    ->validationMessages([
                        'regex' => 'Phone number must contain exactly 10 digits.',
                    ])
                    ->unique('vendors', 'phone', ignoreRecord: true),
                Textarea::make('vendor_address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('city')
                    ->default(null),
                Select::make('province')
                    ->options([
                        'Bagmati Province' => 'Bagmati Province',
                        'Koshi Province' => 'Koshi Province',
                        'Gandaki Province' => 'Gandaki Province',
                        'Lumbini Province' => 'Lumbini Province',
                        'Madhesh Province' => 'Madhesh Province',
                        'Karnali Province' => 'Karnali Province',
                        'Sudurpashchim Province' => 'Sudurpashchim Province',
                    ])
                    ->searchable()
                    ->placeholder('Select a province')
                    ->default(null),
                TextInput::make('pan_number')
                    ->unique('vendors', 'pan_number', ignoreRecord: true)
                    ->default(null),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->default(5.0),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'suspended' => 'Suspended'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
