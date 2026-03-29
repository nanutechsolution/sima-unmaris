<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AssetLoans\AssetLoanResource;
use App\Models\AssetLoan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueLoansWidget extends BaseWidget
{
     use HasWidgetShield;
    // Judul mencolok dengan icon peringatan
    protected static ?string $heading = '🚨 Peminjaman Terlambat (Overdue)';
    
    // Urutan ke-3 di Dashboard (Di bawah grafik)
    protected static ?int $sort = 3;
    
    // Lebar tabel mengambil full screen
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query Cerdas: Ambil aset yang belum dikembalikan DAN tanggal harap kembalinya sudah lewat dari hari ini
                AssetLoan::query()
                    ->whereNull('actual_return_date')
                    ->where('expected_return_date', '<', now())
            )
            ->columns([
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->weight('bold')
                    ->description(fn ($record) => $record->asset?->asset_code),
                    
                Tables\Columns\TextColumn::make('borrower.name')
                    ->label('Peminjam / Penanggung Jawab')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('expected_return_date')
                    ->label('Tenggat Waktu Kembali')
                    ->dateTime('d M Y - H:i')
                    ->color('danger')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn () => 'Terlambat')
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                // Tombol pintasan langsung menuju ke menu pengembalian
                Action::make('view')
                    ->label('Tindak Lanjuti')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->button()
                    ->color('danger')
                    ->url(fn (AssetLoan $record): string => AssetLoanResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}