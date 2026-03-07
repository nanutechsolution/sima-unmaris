<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetConditionEnum;
use App\Enums\AssetStatusEnum;
use App\Models\Asset;
use App\Models\Category;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Ramsey\Collection\Set;

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
                            Select::make('category_id')
                                ->label('Kategori Aset')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set) {

                                    $category = Category::find($state);

                                    if (!$category) {
                                        return;
                                    }

                                    $prefix = $category->prefix_code;

                                    $lastAsset = Asset::where('category_id', $state)
                                        ->orderBy('id', 'desc')
                                        ->first();

                                    $number = 1;

                                    if ($lastAsset) {
                                        preg_match('/(\d+)$/', $lastAsset->asset_code, $match);
                                        $number = isset($match[1]) ? ((int)$match[1] + 1) : 1;
                                    }

                                    $code = $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);

                                    $set('asset_code', $code);
                                })
                                ->columnSpan(6),
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
                                ->helperText('Lokasi penempatan fisik aset, untuk keperluan inventarisasi dan koordinasi perawatan.')
                                ->relationship('room', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Pilih ruangan')
                                ->columnSpan(6),
                            Select::make('department_id')
                                ->label('Departemen / Unit')
                                ->helperText('Unit atau fakultas pemilik aset')
                                ->relationship('department', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('Unit atau fakultas pemilik aset')
                                ->columnSpan(6),
                            Select::make('pic_user_id')
                                ->label('Penanggung Jawab (PIC)')
                                ->helperText('Orang yang bertanggung jawab atas aset ini, untuk keperluan koordinasi perawatan dan inventarisasi.')
                                ->relationship('pic', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(6),
                            Select::make('supplier_id')
                                ->label('Vendor / Supplier')
                                ->helperText('Penyedia barang, untuk keperluan histori pembelian dan kontak jika perlu klaim garansi')
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
                                ->helperText('Tanggal pembelian atau perolehan aset, untuk keperluan akuntansi dan penyusutan.')
                                ->native(false)
                                ->minDate(now()->subYears(30))
                                ->displayFormat('d M Y')
                                ->required()
                                ->maxDate(now())
                                ->columnSpan(6),

                            TextInput::make('acquisition_value')
                                ->label('Harga Perolehan')
                                ->helperText('Harga asli saat pembelian, untuk keperluan akuntansi dan penyusutan.')
                                ->required()
                                ->prefix('Rp')
                                ->placeholder('15000000')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrateStateUsing(function ($state) {
                                    return (int) preg_replace('/[^0-9]/', '', $state);
                                })
                                ->columnSpan(6),
                            TextInput::make('useful_life_years')
                                ->label('Umur Ekonomis (Tahun)')
                                ->numeric()
                                ->default(5)
                                ->helperText('Perkiraan masa pakai barang untuk hitungan penyusutan keuangan.')
                                ->columnSpan(6)
                                ->required(),

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
