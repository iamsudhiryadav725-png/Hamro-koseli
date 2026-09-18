<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'vendor_name')
                    ->label('Vendor')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('ticket_number')
                    ->required(),
                Select::make('category')
                    ->label('Category')
                    ->options([
                        'General Support' => 'General Support',
                        'Technical Support' => 'Technical Support',
                        'Other' => 'Other',
                    ])
                    ->required(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Open' => 'Open',
                        'Resolved' => 'Resolved',
                        'Closed' => 'Closed',
                    ])
                    ->required()
                    ->default('Pending'),
            ]);
    }
}
