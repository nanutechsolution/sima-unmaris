<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MaintenanceLogs\MaintenanceLogResource;
use App\Models\MaintenanceLog;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ScheduledMaintenanceWidget extends BaseWidget
{
    // Judul tabel untuk menampung jadwal servis & laporan publik dari QR Code
    protected static ?string $heading = '🔧 Jadwal Perbaikan & Laporan Masuk';
    
    // Urutan ke-4 di Dashboard
    protected static ?int $sort = 4;
    
    // Lebar tabel mengambil full screen
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil semua laporan yang berstatus 'scheduled' (menunggu diproses)
                MaintenanceLog::query()
                    ->where('status', 'scheduled')
                    ->orderBy('maintenance_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Jadwal / Tanggal Lapor')
                    ->date('d M Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Aset')
                    ->weight('bold')
                    ->description(fn ($record) => $record->asset?->asset_code),
                    
                Tables\Columns\TextColumn::make('problem_description')
                    ->label('Detail Laporan / Kendala')
                    ->limit(60) // Batasi teks panjang agar UI tidak rusak
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn () => 'Menunggu Tindakan')
                    ->badge()
                    ->color('warning'),
            ])
            ->actions([
                // Pintasan untuk memproses laporan perbaikan tersebut
              Action::make('process')
                    ->label('Proses')
                    ->icon('heroicon-o-wrench')
                    ->button()
                    ->color('warning')
                    ->url(fn (MaintenanceLog $record): string =>MaintenanceLogResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}