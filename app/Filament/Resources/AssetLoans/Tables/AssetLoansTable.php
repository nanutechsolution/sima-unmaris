<?php

namespace App\Filament\Resources\AssetLoans\Tables;

use App\Models\AssetLoan;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssetLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->description(fn($record) => $record->asset?->asset_code)
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('borrower.name')
                    ->label('Peminjam')
                    ->searchable(),

                TextColumn::make('expected_return_date')
                    ->label('Tenggat Kembali')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // Status Dinamis (Dihitung dari selisih waktu secara live)
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (AssetLoan $record) {
                        if ($record->actual_return_date) return 'returned';
                        if ($record->expected_return_date < now()) return 'overdue';
                        return 'borrowed';
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'returned' => 'Selesai (Dikembalikan)',
                        'overdue' => 'Terlambat (Overdue)',
                        'borrowed' => 'Sedang Dipinjam',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'returned' => 'success',
                        'overdue' => 'danger',
                        'borrowed' => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('print_bast')
                    ->label('Cetak BAST')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (AssetLoan $record) {
                        // Load relasi agar data lengkap saat dicetak
                        $record->load(['asset', 'borrower']);

                        // Membuat template HTML untuk BAST Profesional
                        $html = '
                        <html>
                        <head>
                            <style>
                                body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; margin: 30px; }
                                .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                                .kop-surat h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
                                .kop-surat p { margin: 2px 0; font-size: 10pt; }
                                .judul { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20px; text-decoration: underline; }
                                .content { margin-bottom: 30px; text-align: justify; }
                                table.detail { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                                table.detail th, table.detail td { border: 1px solid #000; padding: 8px; text-align: left; }
                                table.detail th { background-color: #f2f2f2; }
                                .ttd-area { width: 100%; margin-top: 50px; text-align: center; }
                                .ttd-box { width: 45%; display: inline-block; vertical-align: top; }
                                .ttd-space { height: 80px; }
                            </style>
                        </head>
                        <body>
                            <div class="kop-surat">
                                <h1>UNIVERSITAS MARITIM (UNMARIS)</h1>
                                <p>Jl. Pendidikan No. 1, Kota Akademik, Provinsi Cerdas 12345</p>
                                <p>Email: sarpras@unmaris.ac.id | Telp: (021) 1234567</p>
                            </div>
                            
                            <div class="judul">BERITA ACARA SERAH TERIMA (BAST) PEMINJAMAN BARANG</div>
                            
                            <div class="content">
                                <p>Pada hari ini, tanggal <b>' . \Carbon\Carbon::parse($record->loan_date)->translatedFormat('d F Y') . '</b>, telah dilakukan serah terima peminjaman barang inventaris kampus dengan rincian sebagai berikut:</p>
                                
                                <table class="detail">
                                    <tr>
                                        <th width="30%">Kode Aset</th>
                                        <td>' . $record->asset->asset_code . '</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <td>' . $record->asset->name . '</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Peminjaman</th>
                                        <td>' . \Carbon\Carbon::parse($record->loan_date)->translatedFormat('d F Y H:i') . '</td>
                                    </tr>
                                    <tr>
                                        <th>Batas Waktu Pengembalian</th>
                                        <td>' . \Carbon\Carbon::parse($record->expected_return_date)->translatedFormat('d F Y H:i') . '</td>
                                    </tr>
                                    <tr>
                                        <th>Tujuan Peminjaman</th>
                                        <td>' . ($record->notes ?? 'Keperluan Akademik / Operasional') . '</td>
                                    </tr>
                                </table>
                                
                                <p>Barang tersebut diserahkan dalam keadaan <b>BAIK</b> dan peminjam bertanggung jawab penuh atas keamanan, keutuhan, dan perawatan barang selama masa peminjaman. Segala bentuk kerusakan atau kehilangan akan menjadi tanggung jawab peminjam.</p>
                            </div>
                            
                            <div class="ttd-area">
                                <div class="ttd-box">
                                    <p>Pihak Pertama (Yang Menyerahkan),</p>
                                    <p>Admin / Petugas Sarpras</p>
                                    <div class="ttd-space"></div>
                                    <p><b>_________________________</b></p>
                                </div>
                                <div class="ttd-box">
                                    <p>Pihak Kedua (Yang Menerima),</p>
                                    <p>Peminjam / Penanggung Jawab</p>
                                    <div class="ttd-space"></div>
                                    <p><b>' . strtoupper($record->borrower->name) . '</b></p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                        // Generate PDF
                        $pdf = Pdf::loadHTML($html);

                        // Download File
                        $filename = 'BAST_Peminjaman_' . $record->asset->asset_code . '.pdf';
                        // Replace slashes and backslashes with underscores
                        $filename = str_replace(['/', '\\'], '_', $filename);

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            $filename
                        );
                    }),
                Action::make('returnAsset')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah aset ini sudah diterima kembali dalam kondisi baik?')
                    ->modalSubmitActionLabel('Ya, Sudah Dikembalikan')
                    // Tombol ini akan otomatis Sembunyi jika barang sudah dikembalikan
                    ->visible(fn(AssetLoan $record) => is_null($record->actual_return_date))
                    ->action(function (AssetLoan $record) {
                        // 1. Catat waktu kembali aktual
                        $record->update(['actual_return_date' => now()]);

                        // 2. Kembalikan status aset di master menjadi Available
                        if ($record->asset) {
                            $record->asset->update(['status' => \App\Enums\AssetStatusEnum::AVAILABLE]);
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
