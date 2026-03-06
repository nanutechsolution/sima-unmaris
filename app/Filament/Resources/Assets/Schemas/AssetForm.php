<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetConditionEnum;
use App\Enums\AssetStatusEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Utama')
                    ->description('Identitas dasar aset kampus')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->schema([

                        Grid::make(12)->schema([
                            TextInput::make('asset_code')
                                ->label('Kode Aset')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->placeholder('UNMARIS/IT/2026/001')
                                ->maxLength(50)
                                ->columnSpan(4),

                            TextInput::make('name')
                                ->label('Nama Aset')
                                ->required()
                                ->placeholder('Contoh: Laptop Asus ROG')
                                ->maxLength(200)
                                ->columnSpan(8),

                            Select::make('category_id')
                                ->label('Kategori Aset')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(6),

                        ]),

                    ]),

                Section::make('Lokasi & Kepemilikan')
                    ->description('Lokasi penempatan serta penanggung jawab aset')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->schema([
                        Grid::make(12)->schema([
                            Select::make('room_id')
                                ->label('Ruangan')
                                ->relationship('room', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Pilih ruangan')
                                ->columnSpan(6),
                            Select::make('department_id')
                                ->label('Departemen / Unit')
                                ->relationship('department', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('Unit atau fakultas pemilik aset')
                                ->columnSpan(6),
                            Select::make('pic_user_id')
                                ->label('Penanggung Jawab (PIC)')
                                ->relationship('pic', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(6),
                            Select::make('supplier_id')
                                ->label('Vendor / Supplier')
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Opsional')
                                ->columnSpan(6),

                        ]),

                    ]),

                Section::make('Finansial & Status')
                    ->description('Informasi pembelian serta kondisi aset')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([

                        Grid::make(12)->schema([

                            DatePicker::make('acquisition_date')
                                ->label('Tanggal Perolehan')
                                ->native(false)
                                ->minDate(now()->subYears(30))
                                ->displayFormat('d M Y')
                                ->required()
                                ->maxDate(now())
                                ->columnSpan(6),

                            TextInput::make('acquisition_value')
                                ->label('Nilai Perolehan')
                                ->numeric()
                                ->required()
                                ->prefix('Rp')
                                ->placeholder('15000000')
                                ->minValue(0)
                                ->step(1)
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->columnSpan(6),

                            Select::make('status')
                                ->label('Status Lifecycle')
                                ->options(AssetStatusEnum::class)
                                ->default(AssetStatusEnum::AVAILABLE)
                                ->required()
                                ->columnSpan(6),

                            Select::make('condition')
                                ->label('Kondisi Fisik')
                                ->options(AssetConditionEnum::class)
                                ->default(AssetConditionEnum::GOOD)
                                ->required()
                                ->columnSpan(6),

                        ]),

                    ]),

            ]);
    }
}
