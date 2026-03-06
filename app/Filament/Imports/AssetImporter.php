<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('asset_code')
                ->label('Kode Aset')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
                
            ImportColumn::make('name')
                ->label('Nama Aset')
                ->requiredMapping()
                ->rules(['required', 'max:200']),
                
            // MAGIC: Sistem akan otomatis mencari ID kategori berdasarkan nama yang diketik di Excel
            ImportColumn::make('category')
                ->relationship(resolveUsing: 'name')
                ->label('Kategori (Ketik Nama Kategori)'),
                
            // MAGIC: Mencari Ruangan berdasarkan nama
            ImportColumn::make('room')
                ->relationship(resolveUsing: 'name')
                ->label('Ruangan (Ketik Nama Ruangan)'),
                
            // MAGIC: Mencari user (PIC) berdasarkan email
            ImportColumn::make('pic')
                ->relationship(resolveUsing: 'email')
                ->label('PIC (Ketik Email Dosen/Staf)'),
                
            ImportColumn::make('acquisition_value')
                ->label('Nilai Harga (Angka Saja)')
                ->numeric()
                ->rules(['numeric', 'min:0']),
                
            ImportColumn::make('acquisition_date')
                ->label('Tgl Beli (YYYY-MM-DD)')
                ->rules(['date']),
                
            ImportColumn::make('status')
                ->label('Status (available / in_use / maintenance)')
                ->rules(['in:available,in_use,maintenance,lost,retired']),
                
            ImportColumn::make('condition')
                ->label('Kondisi (good / fair / damaged)')
                ->rules(['in:good,fair,damaged']),
        ];
    }

    public function resolveRecord(): ?Asset
    {
        // Jika kode aset sudah ada, sistem akan melakukan UPDATE (bukan duplikat)
        // Jika belum ada, sistem akan membuat data BARU (Insert)
        return Asset::firstOrNew([
            'asset_code' => $this->data['asset_code'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data aset berhasil diselesaikan. ' . number_format($import->successful_rows) . ' baris berhasil masuk ke database.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Namun, ada ' . number_format($failedRowsCount) . ' baris yang gagal diimpor (Silakan download file log error).';
        }

        return $body;
    }
}