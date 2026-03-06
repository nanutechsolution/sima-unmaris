<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetConditionEnum;
use App\Enums\AssetStatusEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->description('Identitas dasar aset kampus')
                    ->schema([
                        TextInput::make('asset_code')
                            ->label('Kode Aset (Identity)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: UNMARIS/IT/2026/001'),

                        TextInput::make('name')
                            ->label('Nama Aset')
                            ->required()
                            ->maxLength(200),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3),

                Section::make('Lokasi & Kepemilikan')
                    ->schema([
                        Select::make('room_id')
                            ->relationship('room', 'name')
                            ->label('Ruangan / Penempatan')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('pic_user_id')
                            ->relationship('pic', 'name') // Menggunakan nama relasi di Model Asset
                            ->label('Penanggung Jawab (PIC)')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->label('Vendor/Supplier (Opsional)')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Section::make('Finansial & Status')
                    ->schema([
                        DatePicker::make('acquisition_date')
                            ->label('Tanggal Perolehan')
                            ->native(false) // UI Kalender pop-up modern
                            ->required(),

                        TextInput::make('acquisition_value')
                            ->label('Nilai Perolehan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'), // Format Rupiah

                        Select::make('status')
                            ->label('Status Lifecycle')
                            ->options(AssetStatusEnum::class)
                            ->default(AssetStatusEnum::AVAILABLE)
                            ->required(),

                        Select::make('condition')
                            ->label('Kondisi Fisik')
                            ->options(AssetConditionEnum::class)
                            ->default(AssetConditionEnum::GOOD)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
