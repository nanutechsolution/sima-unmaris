<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informasi Lokasi')
                    ->description('Kelola data gedung atau area kampus')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lokasi / Gedung')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Contoh: Gedung Rektorat Utama'),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(1),

                // --- FITUR BARU: Manajemen Ruangan ---
                ComponentsSection::make('Daftar Ruangan')
                    ->description('Daftarkan ruangan yang ada di lokasi/gedung ini')
                    ->schema([
                        Repeater::make('rooms')
                            ->relationship('rooms') // Otomatis mengisi foreign_key location_id di tabel rooms
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Ruangan')
                                    ->required()
                                    ->placeholder('Contoh: Lab Komputer A'),
                                TextInput::make('code')
                                    ->label('Kode Ruangan')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: LAB-01'),
                            ])
                            ->columns(2) // Input nama dan kode dibuat bersebelahan
                            ->defaultItems(0) // Default kosong agar rapi
                            ->addActionLabel('Tambah Ruangan Baru')
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }
}