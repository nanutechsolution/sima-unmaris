<?php

namespace App\Filament\Resources\InventoryTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('item.name')
                    ->label('Barang')
                    ->description(fn($record) => "SKU: {$record->item->sku}"),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'in' ? 'MASUK' : 'KELUAR')
                    ->color(fn($state) => $state === 'in' ? 'success' : 'danger'),

                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state, $record) => "{$state} {$record->item->unit}"),

                TextColumn::make('department.name')
                    ->label('Unit Peminta')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
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
