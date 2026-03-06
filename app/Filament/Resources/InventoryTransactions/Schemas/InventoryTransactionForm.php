<?php

namespace App\Filament\Resources\InventoryTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class InventoryTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Section::make('Input Mutasi Barang')
                    ->schema([
                       Select::make('inventory_item_id')
                            ->label('Pilih Barang')
                            ->relationship('item', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->sku}] {$record->name} - Sisa: {$record->current_stock} {$record->unit}")
                            ->searchable()
                            ->required(),
                            
                       Select::make('type')
                            ->label('Jenis Transaksi')
                            ->options([
                                'in' => 'Barang Masuk (Pembelian/Restock)',
                                'out' => 'Barang Keluar (Permintaan Unit)',
                            ])
                            ->required()
                            ->live(),
                            
                       TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                            
                       Select::make('department_id')
                            ->label('Unit Kerja Peminta')
                            ->relationship('department', 'name')
                            ->visible(fn (Get $get) => $get('type') === 'out')
                            ->required(fn (Get $get) => $get('type') === 'out'),
                            
                       DatePicker::make('transaction_date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                            
                       Hidden::make('user_id')
                            ->default(auth()->id()),
                            
                       Textarea::make('notes')
                            ->label('Keterangan')
                            ->placeholder('Cth: Persiapan Ujian Semester')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
