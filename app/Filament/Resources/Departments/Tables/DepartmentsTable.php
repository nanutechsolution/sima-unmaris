<?php

namespace App\Filament\Resources\Departments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Unit / Departemen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('manager.name')
                    ->label('Kepala Unit')
                    ->searchable()
                    ->placeholder('Belum ditentukan'),

                TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label('Jumlah Aset')
                    ->badge()
                    ->color('success'),
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
