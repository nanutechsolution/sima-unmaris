<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Barang')
                    ->schema([
                        TextInput::make('sku')
                            ->label('Kode SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Cth: ATK-001'),

                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),

                        Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'Alat Tulis' => 'Alat Tulis',
                                'Kebersihan' => 'Kebersihan',
                                'Konsumsi' => 'Konsumsi',
                                'Lainnya' => 'Lainnya',
                            ])->required(),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('Cth: Rim, Box, Pcs')
                            ->required(),

                        TextInput::make('min_stock')
                            ->label('Stok Minimum (Alert)')
                            ->numeric()
                            ->default(5)
                            ->helperText('Sistem akan memberi tanda merah jika stok di bawah angka ini.'),
                    ])->columns(2),
            ]);
    }
}
