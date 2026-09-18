<?php

namespace App\Filament\Resources\Payouts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.vendor_name')
                    ->label('Vendor Store')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Transaction Type')
                    ->getStateUsing(function ($record) {
                        return $record->platform_fee > 0
                            ? 'Disbursement (Admin to Vendor)'
                            : 'Commission Payment (Vendor to Admin)';
                    })
                    ->badge()
                    ->color(fn ($state) => str_contains($state, 'Commission') ? 'success' : 'info'),
                TextColumn::make('gross_amount')
                    ->label('Gross Amount')
                    ->numeric()
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('platform_fee')
                    ->label('Platform Fee')
                    ->numeric()
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount Paid')
                    ->numeric()
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('method')
                    ->searchable(),
                TextColumn::make('transaction_id')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(30)
                    ->searchable(),
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
