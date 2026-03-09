<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Models\Location;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportGenerator extends Page
{
    protected static ?string $modelLabel = 'Generator Laporan';
    protected static ?string $title = 'Laporan Akreditasi & Keuangan';
    protected static ?string $slug = 'report-generator';
    // Masukkan ke grup Sistem & Audit
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::SYSTEM_AUDIT->value;
    protected static ?int $navigationSort = 5; // Taruh paling bawah

    protected string $view = 'filament.pages.report-generator';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_report')
                ->label('Buat Laporan Baru')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalHeading('Parameter Laporan Akreditasi')
                ->modalDescription('Pilih filter data yang ingin ditarik. Kosongkan filter jika ingin mengekspor seluruh aset kampus.')
                ->modalSubmitActionLabel('Generate PDF Resmi')
                ->modalIcon('heroicon-o-printer')
                ->form([
                    Select::make('location_id')
                        ->label('Filter Lokasi / Gedung')
                        ->options(Location::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('Semua Lokasi Kampus'),
                        
                    Select::make('condition')
                        ->label('Filter Kondisi Fisik')
                        ->options(\App\Enums\AssetConditionEnum::class)
                        ->placeholder('Semua Kondisi'),
                        
                    DatePicker::make('start_date')
                        ->label('Tahun Pembelian (Dari)')
                        ->native(false)
                        ->displayFormat('Y')
                        ->format('Y-01-01'),
                        
                    DatePicker::make('end_date')
                        ->label('Tahun Pembelian (Sampai)')
                        ->native(false)
                        ->displayFormat('Y')
                        ->format('Y-12-31'),
                ])
                ->action(function (array $data) {
                    // 1. QUERY KOMPLEKS BERDASARKAN FILTER
                    $query = Asset::query()->with(['category', 'room.location', 'pic']);
                    
                    if (!empty($data['location_id'])) {
                        $query->whereHas('room', function($q) use ($data) {
                            $q->where('location_id', $data['location_id']);
                        });
                    }
                    if (!empty($data['condition'])) {
                        $query->where('condition', $data['condition']);
                    }
                    if (!empty($data['start_date'])) {
                        $query->whereDate('acquisition_date', '>=', $data['start_date']);
                    }
                    if (!empty($data['end_date'])) {
                        $query->whereDate('acquisition_date', '<=', $data['end_date']);
                    }
                    
                    $assets = $query->orderBy('acquisition_date', 'desc')->get();
                    
                    // 2. TEMPLATE HTML UNTUK STANDAR BAN-PT (Kop Surat & Tabel)
                    $html = '
                    <html>
                    <head>
                        <style>
                            body { font-family: "Times New Roman", Times, serif; font-size: 11pt; margin: 15px; }
                            .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                            .kop-surat h1 { margin: 0; font-size: 16pt; text-transform: uppercase; letter-spacing: 1px; }
                            .kop-surat p { margin: 2px 0; font-size: 10pt; }
                            .judul { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 20px; text-transform: uppercase; }
                            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
                            th, td { border: 1px solid #000; padding: 8px; vertical-align: top; }
                            th { background-color: #e5e7eb; text-align: center; font-weight: bold; }
                            .text-right { text-align: right; }
                            .text-center { text-align: center; }
                            .footer { margin-top: 40px; width: 100%; }
                            .ttd-box { float: right; width: 280px; text-align: center; }
                            .clear { clear: both; }
                        </style>
                    </head>
                    <body>
                        <div class="kop-surat">
                            <h1>UNIVERSITAS STELLA MARIS (UNMARIS)</h1>
                            <p>Jl. Pendidikan No. 1, Kota Akademik, Provinsi Cerdas 12345</p>
                            <p>Email: rektorat@unmaris.ac.id | Telp: (021) 1234567</p>
                        </div>
                        
                        <div class="judul">
                            REKAPITULASI ASET TETAP (INVENTARIS KAMPUS)<br>
                            <span style="font-size: 10pt; font-weight: normal;">Dicetak pada: ' . now()->translatedFormat('d F Y H:i') . '</span>
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Aset</th>
                                    <th width="25%">Nama Barang & Kategori</th>
                                    <th width="15%">Lokasi / Ruang</th>
                                    <th width="10%">Kondisi</th>
                                    <th width="15%">Harga Beli (Rp)</th>
                                    <th width="15%">Nilai Buku (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                    $totalHargaBeli = 0;
                    $totalNilaiBuku = 0;
                    
                    foreach($assets as $index => $asset) {
                        // Memanggil rumus penyusutan (current_value) yang kita buat di Model
                        $hargaBeli = $asset->acquisition_value ?? 0;
                        $nilaiBuku = $asset->current_value ?? $hargaBeli;
                        $kondisiLabel = is_object($asset->condition) ? $asset->condition->getLabel() : ucfirst($asset->condition);
                        
                        $totalHargaBeli += $hargaBeli;
                        $totalNilaiBuku += $nilaiBuku;
                        
                        $html .= '<tr>
                            <td class="text-center">' . ($index + 1) . '</td>
                            <td>' . $asset->asset_code . '</td>
                            <td><b>' . $asset->name . '</b><br><span style="font-size:8pt; color:#666;">Kategori: ' . ($asset->category->name ?? '-') . '</span></td>
                            <td>' . ($asset->room->name ?? '-') . '</td>
                            <td class="text-center">' . $kondisiLabel . '</td>
                            <td class="text-right">' . number_format($hargaBeli, 0, ',', '.') . '</td>
                            <td class="text-right"><b>' . number_format($nilaiBuku, 0, ',', '.') . '</b></td>
                        </tr>';
                    }
                    
                    $html .= '
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">TOTAL KESELURUHAN:</th>
                                    <th class="text-right">' . number_format($totalHargaBeli, 0, ',', '.') . '</th>
                                    <th class="text-right">' . number_format($totalNilaiBuku, 0, ',', '.') . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <div class="footer">
                            <div class="ttd-box">
                                <p>Disahkan di Tempat,<br>' . now()->translatedFormat('d F Y') . '</p>
                                <p>Kepala Biro Administrasi Umum,</p>
                                <br><br><br><br>
                                <p><b>_________________________</b><br>NIP. 19800101 200501 1 001</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </body>
                    </html>';
                    
                    // 3. RENDER HTML MENJADI PDF LANDSCAPE
                    $pdf = Pdf::loadHTML($html)->setPaper('A4', 'landscape');
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()), 
                        'Laporan_Akreditasi_Aset_'.date('Ymd_His').'.pdf'
                    );
                }),
        ];
    }
}