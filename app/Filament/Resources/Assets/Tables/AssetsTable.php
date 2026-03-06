<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetStatusEnum;
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
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->label('Nilai Perolehan')
                    ->money('IDR') // Otomatis format Rupiah
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan secara default agar tabel tidak penuh

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
                ActionGroup::make([ // Grouping tombol action agar rapi (dropdown)
                    ActionsEditAction::make(),

                    // --- 1. ACTION: VIEW QR CODE ---
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

                    // --- 2. ACTION: SERAH TERIMA ---
                  // --- 2. ACTION: SERAH TERIMA ---
                    Action::make('handover')
                        ->label('Serah Terima')
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('success')
                        ->modalHeading('Proses Serah Terima Aset')
                        ->modalDescription('Proses ini akan memindahkan tanggung jawab aset dan menyimpan bukti digital.')
                        ->form([
                            Select::make('receiver_user_id')
                                ->label('Penerima Aset Baru')
                                ->options(fn () => User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('location_id')
                                ->label('Lokasi Penyerahan Fisik')
                                ->options(fn () => Location::pluck('name', 'id'))
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
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([ // Pastikan memanggil dari class Tables\Actions
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
