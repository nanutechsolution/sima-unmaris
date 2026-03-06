<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetStatusEnum;
use App\Filament\Imports\AssetImporter;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use App\Services\AssetHandoverService;
use App\Services\QrIdentityService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\ImportAction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(AssetImporter::class)
                    ->label('Import Data (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->options([
                        // Memaksa sistem mengenali pemisah koma atau titik koma di file Excel/CSV
                        'delimiter' => ',',
                    ]),
            ])
            ->columns([
                TextColumn::make('asset_code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Admin bisa klik untuk copy kode
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room.name')
                    ->label('Ruangan / Penempatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pic.name')
                    ->label('PIC / Pemegang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge() // Mengambil warna & label dari AssetStatusEnum
                    ->searchable(),

                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge() // Mengambil warna & label dari AssetConditionEnum
                    ->searchable(),

                TextColumn::make('acquisition_value')
                    ->label('Harga Beli Asli')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan secara default agar tabel tidak penuh
                TextColumn::make('current_value')
                    ->label('Nilai Buku (Saat Ini)')
                    ->money('IDR', locale: 'id') // Format Rupiah
                    // Mengambil nilai dari rumus cerdas (Accessor) yang kita buat di Model
                    ->getStateUsing(fn($record) => $record->current_value)
                    // Beri warna peringatan jika nilai barang sudah Rp 0 (sudah waktunya diganti/pensiun)
                    ->color(fn($record) => $record->current_value == 0 ? 'danger' : 'success')
                    ->weight('bold')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('acquisition_date')
                    ->label('Tgl Perolehan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(AssetStatusEnum::class),
                SelectFilter::make('category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ActionsEditAction::make(),
                    Action::make('generate_qr')
                        ->label('Identitas QR')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->modalHeading('Identitas Digital Aset')
                        ->modalWidth('sm')
                        ->modalContent(function (Asset $record, QrIdentityService $qrService) {
                            $url = $qrService->generateAssetQrCode($record);
                            return new HtmlString('
                                <div class="flex flex-col items-center justify-center p-4">
                                    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                                        <img src="' . $url . '" alt="QR Code" class="w-48 h-48">
                                    </div>
                                    <p class="mt-4 text-base text-gray-800 font-mono font-bold tracking-wider">' . $record->asset_code . '</p>
                                    <p class="text-xs text-center text-gray-500 mt-2">Cetak dan tempelkan QR ini pada fisik aset.<br>Scan untuk verifikasi dan audit.</p>
                                </div>
                            ');
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),
                    Action::make('handover')
                        ->label('Serah Terima')
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('success')
                        ->disabled(fn(\App\Models\Asset $record) => in_array(
                            is_object($record->status) ? $record->status->value : $record->status,
                            ['maintenance', 'lost', 'retired']
                        ))
                        // 2. Berikan pesan Tooltip (muncul saat mouse diarahkan ke tombol)
                        ->tooltip(function (\App\Models\Asset $record) {
                            $statusValue = is_object($record->status) ? $record->status->value : $record->status;

                            return match ($statusValue) {
                                'maintenance' => 'Aset sedang di bengkel/diperbaiki. Tidak bisa diserahterimakan.',
                                'lost' => 'Aset sedang hilang. Tidak bisa diserahterimakan.',
                                'retired' => 'Aset sudah dipensiunkan / dihapus.',
                                default => 'Lakukan proses serah terima aset ke personil lain',
                            };
                        })
                        ->modalHeading('Proses Serah Terima Aset')
                        ->modalDescription('Proses ini akan memindahkan tanggung jawab aset dan menyimpan bukti digital.')
                        ->form([
                            Select::make('receiver_user_id')
                                ->label('Penerima Aset Baru')
                                ->options(fn() => User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('location_id')
                                ->label('Lokasi Penyerahan Fisik')
                                ->options(fn() => Location::pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            // Biarkan Filament menangani upload dan atur direktorinya di sini
                            FileUpload::make('asset_photo')
                                ->label('Foto Fisik Aset Saat Penyerahan')
                                ->image()
                                ->directory('handovers/' . date('Y/m') . '/assets')
                                ->required(),
                            FileUpload::make('document_photo')
                                ->label('Foto Dokumen Berita Acara')
                                ->image()
                                ->directory('handovers/' . date('Y/m') . '/documents')
                                ->required(),

                            Textarea::make('notes')
                                ->label('Catatan Tambahan')
                                ->maxLength(500),
                        ])
                        ->action(function (array $data, Asset $record, AssetHandoverService $handoverService) {
                            try {
                                $handoverService->processHandover(
                                    $record,
                                    $data['receiver_user_id'],
                                    $data['location_id'],
                                    $data['asset_photo'],   // Langsung pass string path dari Filament
                                    $data['document_photo'], // Langsung pass string path dari Filament
                                    $data['notes'] ?? null
                                );

                                Notification::make()
                                    ->title('Serah Terima Berhasil')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Memproses Serah Terima')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                ]),
                Action::make('returnToWarehouse')
                    ->label('Tarik ke Gudang')
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->color('warning')
                    // Guardrails: Hanya muncul untuk aset yang sedang "Digunakan" (In Use)
                    ->visible(fn(\App\Models\Asset $record) => in_array(
                        is_object($record->status) ? $record->status->value : $record->status,
                        ['in_use']
                    ))
                    ->modalHeading('Tarik Aset Kembali ke Gudang')
                    ->modalDescription('Aset akan ditarik dari Penanggung Jawab saat ini, dikembalikan menjadi "Tersedia", dan ditransfer ke akun Anda (Gudang/Admin).')
                    ->form([
                        Select::make('return_condition')
                            ->label('Kondisi Fisik Saat Dikembalikan')
                            ->options(\App\Enums\AssetConditionEnum::class)
                            // Default ke kondisi terakhir yang tercatat di database
                            ->default(fn(\App\Models\Asset $record) => is_object($record->condition) ? $record->condition->value : $record->condition)
                            ->required()
                            ->helperText('Pastikan mengecek fisik barang. Jika dikembalikan dalam keadaan rusak, ubah menjadi "Rusak".'),

                        Textarea::make('return_notes')
                            ->label('Catatan Penarikan (Opsional)')
                            ->placeholder('Contoh: Dikembalikan karena dosen bersangkutan pensiun.'),
                    ])
                    ->action(function (\App\Models\Asset $record, array $data): void {
                        // 1. Update Master Aset secara instan
                        $record->update([
                            'pic_user_id' => auth()->id(), // Pindahkan kembali ke Admin yang login
                            'status' => \App\Enums\AssetStatusEnum::AVAILABLE, // Status siap digunakan lagi
                            'condition' => $data['return_condition'],
                        ]);

                        // Note: Histori perubahan PIC & Status ini otomatis dicatat oleh Spatie Activitylog!

                        \Filament\Notifications\Notification::make()
                            ->title('Aset Ditarik ke Gudang')
                            ->body('Aset kini berstatus Tersedia dan menjadi tanggung jawab Anda.')
                            ->success()
                            ->send();
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('print_qr_labels')
                        ->label('Cetak Stiker QR')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // Load relasi untuk mencegah N+1 Query problem saat render PDF
                            $records->load(['category', 'room']);

                            // Membuat template HTML khusus berformat grid untuk label stiker profesional
                            $html = '<html><head><style>
                                @page { margin: 15px; }
                                body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
                                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
                                .header h2 { margin: 0; color: #1e3a8a; font-size: 22px; text-transform: uppercase; letter-spacing: 2px; }
                                .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
                                .grid { width: 100%; text-align: center; }
                                .sticker { 
                                    width: 31%; 
                                    display: inline-block; 
                                    margin: 1%; 
                                    padding: 10px; 
                                    border: 1.5px solid #1e3a8a; 
                                    box-sizing: border-box; 
                                    border-radius: 6px; 
                                    page-break-inside: avoid;
                                    background-color: #ffffff;
                                    vertical-align: top;
                                }
                                .sticker-header {
                                    background-color: #1e3a8a;
                                    color: #ffffff;
                                    font-size: 10px;
                                    font-weight: bold;
                                    padding: 5px;
                                    margin: -10px -10px 10px -10px;
                                    border-top-left-radius: 4px;
                                    border-top-right-radius: 4px;
                                    text-transform: uppercase;
                                    letter-spacing: 1px;
                                }
                                .qr-container { margin: 5px 0; }
                                .qr-img { width: 110px; height: 110px; }
                                .code { font-size: 13px; font-weight: bold; margin: 5px 0 2px 0; color: #000; }
                                .name { font-size: 11px; margin: 0 0 5px 0; color: #333; height: 28px; overflow: hidden; display: block; }
                                .details { font-size: 9px; color: #555; text-align: left; border-top: 1px dotted #ccc; padding-top: 5px; margin-top: 5px; line-height: 1.4; }
                                .details span { font-weight: bold; color: #000; }
                            </style></head><body>';

                            $html .= '<div class="header">
                                        <h2>SIMA UNMARIS</h2>
                                        <p>Dokumen Label Identitas Aset Digital (Scan QR untuk Verifikasi)</p>
                                      </div>';
                            $html .= '<div class="grid">';

                            foreach ($records as $record) {
                                // 1. Pastikan Digital Signature (Hash) ada di database
                                if (!$record->qr_signature_hash) {
                                    $signature = hash_hmac('sha256', $record->id . $record->asset_code, config('app.key'));
                                    $record->update(['qr_signature_hash' => $signature]);
                                }
                                $verifyUrl = route('asset.verify', ['signature' => $record->qr_signature_hash]);

                                // 2. Generate Base64 SVG QR Code
                                $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(1)->generate($verifyUrl);
                                $imgSrc = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

                                // 3. Masukkan ke dalam grid HTML Stiker Profesional
                                $html .= '<div class="sticker">';
                                $html .= '<div class="sticker-header">Aset Kampus Unmaris</div>';
                                $html .= '<div class="qr-container"><img src="' . $imgSrc . '" class="qr-img" /></div>';
                                $html .= '<p class="code">' . $record->asset_code . '</p>';
                                $html .= '<p class="name">' . substr($record->name, 0, 45) . '</p>';
                                $html .= '<div class="details">';
                                $html .= 'Kategori: <span>' . ($record->category->name ?? '-') . '</span><br>';
                                $html .= 'Lokasi: <span>' . ($record->room->name ?? 'Belum Ditentukan') . '</span>';
                                $html .= '</div>';
                                $html .= '</div>';
                            }

                            $html .= '</div></body></html>';

                            // 4. Proses render PDF
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

                            // 5. Download Otomatis PDF-nya
                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'Stiker_QR_Aset_' . date('Ymd_His') . '.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
