<?php

namespace App\Filament\Resources\AssetLoans\Tables;

use App\Models\AssetLoan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssetLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->description(fn($record) => $record->asset?->asset_code)
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('borrower.name')
                    ->label('Peminjam')
                    ->searchable(),

                TextColumn::make('expected_return_date')
                    ->label('Tenggat Kembali')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // Status Dinamis (Dihitung dari selisih waktu secara live)
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (AssetLoan $record) {
                        if ($record->actual_return_date) return 'returned';
                        if ($record->expected_return_date < now()) return 'overdue';
                        return 'borrowed';
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'returned' => 'Selesai (Dikembalikan)',
                        'overdue' => 'Terlambat (Overdue)',
                        'borrowed' => 'Sedang Dipinjam',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'returned' => 'success',
                        'overdue' => 'danger',
                        'borrowed' => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('returnAsset')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah aset ini sudah diterima kembali dalam kondisi baik?')
                    ->modalSubmitActionLabel('Ya, Sudah Dikembalikan')
                    // Tombol ini akan otomatis Sembunyi jika barang sudah dikembalikan
                    ->visible(fn(AssetLoan $record) => is_null($record->actual_return_date))
                    ->action(function (AssetLoan $record) {
                        // 1. Catat waktu kembali aktual
                        $record->update(['actual_return_date' => now()]);

                        // 2. Kembalikan status aset di master menjadi Available
                        if ($record->asset) {
                            $record->asset->update(['status' => \App\Enums\AssetStatusEnum::AVAILABLE]);
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
