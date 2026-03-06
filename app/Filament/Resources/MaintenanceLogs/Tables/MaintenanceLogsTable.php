<?php

namespace App\Filament\Resources\MaintenanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MaintenanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('maintenance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->description(fn($record) => $record->asset?->asset_code)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('problem_description')
                    ->label('Kendala / Servis')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'in_progress' => 'Proses',
                        'completed' => 'Selesai',
                        default => $state,
                    }),
            ])
            ->defaultSort('maintenance_date', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Terjadwal',
                        'in_progress' => 'Sedang Dikerjakan',
                        'completed' => 'Selesai',
                    ]),
            ])
            ->recordActions([
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
