<?php

namespace App\Filament\Resources\MaintenanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaintenanceLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Aset & Kendala')
                    ->schema([
                        Select::make('asset_id')
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->asset_code} - {$record->name}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pilih Aset yang Diservis'),

                        DatePicker::make('maintenance_date')
                            ->label('Tanggal Perbaikan / Servis')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        TextInput::make('problem_description')
                            ->label('Keluhan / Kerusakan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Layar laptop bergaris / Ganti oli rutin'),

                        Select::make('status')
                            ->label('Status Pengerjaan')
                            ->options([
                                'scheduled' => 'Terjadwal (Belum Mulai)',
                                'in_progress' => 'Sedang Dikerjakan',
                                'completed' => 'Selesai / Tuntas',
                            ])
                            ->required()
                            ->default('completed'),
                    ])->columns(2),

                Section::make('Tindakan & Biaya')
                    ->schema([
                        Textarea::make('action_taken')
                            ->label('Tindakan yang Dilakukan (Opsional)')
                            ->placeholder('Contoh: Penggantian panel LCD baru oleh teknisi')
                            ->columnSpanFull(),

                        TextInput::make('performed_by')
                            ->label('Dikerjakan Oleh (Teknisi/Bengkel)')
                            ->maxLength(255)
                            ->placeholder('Contoh: IT Support Internal / Bengkel Ahass'),

                        TextInput::make('cost')
                            ->label('Total Biaya Perbaikan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(2),

                Section::make('Bukti & Catatan Tambahan')
                    ->schema([
                        FileUpload::make('receipt_photo_path')
                            ->label('Foto Nota / Kuitansi Servis')
                            ->image()
                            ->disk('public') // Pastikan menggunakan disk public
                            ->directory('maintenance-receipts')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan Tambahan (Internal)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
