<?php

namespace App\Filament\Resources\AssetLoans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AssetLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Peminjaman')
                    ->description('Catat pengeluaran aset sementara ke personil/mahasiswa.')
                    ->schema([
                        Select::make('asset_id')
                            // GUARDRAILS: Hanya aset yang berstatus "Available" yang muncul di pilihan!
                            ->relationship('asset', 'name', fn(Builder $query) => $query->where('status', 'available'))
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->asset_code} - {$record->name}")
                            ->label('Pilih Aset (Hanya yang Tersedia)')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('borrower_user_id')
                            ->relationship('borrower', 'name')
                            ->label('Peminjam (Dosen / Staf / Mahasiswa)')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DateTimePicker::make('loan_date')
                            ->label('Waktu Pinjam')
                            ->native(false)
                            ->default(now())
                            ->required(),

                        DateTimePicker::make('expected_return_date')
                            ->label('Tenggat Waktu Kembali')
                            ->native(false)
                            ->default(now()->addDays(1)) // Default pinjam 1 hari
                            ->required(),

                        Textarea::make('notes')
                            ->label('Tujuan / Catatan Pinjaman')
                            ->columnSpanFull()
                            ->placeholder('Contoh: Untuk keperluan Seminar Nasional di Aula.'),
                    ])->columns(2),
            ]);
    }
}
