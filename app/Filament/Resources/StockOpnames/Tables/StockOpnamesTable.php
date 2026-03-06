<?php

namespace App\Filament\Resources\StockOpnames\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StockOpnamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Sesi Audit')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('pic.name')
                    ->label('Ketua Tim'),

                TextColumn::make('start_date')
                    ->label('Periode Mulai')
                    ->date('d M Y'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in_progress' => 'Berjalan',
                        'completed' => 'Selesai',
                        default => $state,
                    }),

                TextColumn::make('details_count')
                    ->counts('details')
                    ->label('Aset Ter-audit')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
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
