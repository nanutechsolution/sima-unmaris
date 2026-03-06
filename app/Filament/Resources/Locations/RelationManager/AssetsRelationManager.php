<?php

namespace App\Filament\Resources\Locations\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\AssetStatusEnum;
use App\Enums\AssetConditionEnum;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class AssetsRelationManager extends RelationManager
{
    // Relasi ini mengambil semua aset melalui ruangan yang ada di lokasi tersebut
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Inventaris Aset di Lokasi Ini';
    

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->label('Kode Aset')
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('room.name')
                    ->label('Ruangan')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('condition')
                    ->badge(),

                Tables\Columns\TextColumn::make('pic.name')
                    ->label('Penanggung Jawab')
                    ->placeholder('Belum ada PIC'),
            ])
            // Fitur Grouping: Admin bisa melihat daftar yang dikelompokkan berdasarkan Ruangan
            ->groups([
                Tables\Grouping\Group::make('room.name')
                    ->label('Berdasarkan Ruangan')
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AssetStatusEnum::class),
                Tables\Filters\SelectFilter::make('condition')
                    ->options(AssetConditionEnum::class),
            ])
            ->headerActions([
                // Memungkinkan tambah aset langsung dari sini jika diperlukan
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}