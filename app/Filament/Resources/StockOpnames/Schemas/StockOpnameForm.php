<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockOpnameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sesi Audit')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Sesi Audit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Audit Fisik Akhir Tahun 2026'),

                        Select::make('pic_id')
                            ->relationship('pic', 'name')
                            ->label('Ketua Tim Auditor')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label('Target Selesai')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status Audit')
                            ->options([
                                'in_progress' => 'Sedang Berjalan (Proses Audit)',
                                'completed' => 'Selesai & Ditutup',
                            ])
                            ->required()
                            ->default('in_progress'),

                        Textarea::make('notes')
                            ->label('Catatan Instruksi')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
