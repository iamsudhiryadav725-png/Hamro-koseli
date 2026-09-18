<?php

namespace App\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('vendor_name')
                    ->searchable(),
                TextColumn::make('owner_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('province')
                    ->searchable(),
                TextColumn::make('pan_number')
                    ->searchable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('total_earnings')
                    ->label('Total Sales')
                    ->money('NPR')
                    ->getStateUsing(fn ($record) => $record->getTotalEarnings()),
                TextColumn::make('commission_owed')
                    ->label('Commission Owed (3%)')
                    ->money('NPR')
                    ->getStateUsing(fn ($record) => $record->getCommissionOwed()),
                TextColumn::make('commission_paid')
                    ->label('Commission Paid')
                    ->money('NPR')
                    ->getStateUsing(fn ($record) => $record->getCommissionPaid()),
                TextColumn::make('commission_balance')
                    ->label('Commission Due')
                    ->money('NPR')
                    ->getStateUsing(fn ($record) => $record->getCommissionBalance())
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
