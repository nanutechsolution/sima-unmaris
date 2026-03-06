<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Group as ComponentsGroup;
use Filament\Schemas\Components\Section as ComponentsSection;

class HandoversRelationManager extends RelationManager
{
    protected static string $relationship = 'handovers';

    protected static ?string $title = 'Riwayat Serah Terima & Bukti Digital';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('handover_time')
            ->columns([
                TextColumn::make('handover_time')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y - H:i')
                    ->sortable(),

                TextColumn::make('giver.name')
                    ->label('Pihak Pemberi')
                    ->description(fn($record) => 'Oleh: ' . ($record->giver?->name ?? 'Sistem')),

                TextColumn::make('receiver.name')
                    ->label('Pihak Penerima')
                    ->weight('bold')
                    ->color('primary'),

                ImageColumn::make('asset_photo_path')
                    ->label('Foto Aset')
                    ->disk('public')
                    ->circular()
                    ->size(50),

                ImageColumn::make('document_photo_path')
                    ->label('Bukti Dokumen')
                    ->disk('public')
                    ->square()
                    ->size(50),

                TextColumn::make('location.name')
                    ->label('Lokasi Penyerahan')
                    ->placeholder('Tidak dicatat'),
            ])
            ->defaultSort('handover_time', 'desc')
            ->actions([
                // Menerapkan standar Filament 5: Menggunakan schema() untuk konten modal
                ViewAction::make()
                    ->label('Lihat Detail & Foto')
                    ->modalHeading('Detail Bukti Serah Terima Digital')
                    ->modalWidth('3xl')
                    ->schema([
                        ComponentsGrid::make(2)
                            ->schema([
                                ComponentsGroup::make([
                                    ComponentsSection::make('Data Transaksi')
                                        ->schema([
                                            TextEntry::make('handover_time')
                                                ->label('Waktu Penyerahan')
                                                ->dateTime('d F Y, H:i')
                                                ->icon('heroicon-m-calendar'),
                                            TextEntry::make('location.name')
                                                ->label('Lokasi Fisik')
                                                ->icon('heroicon-m-map-pin'),
                                            TextEntry::make('giver.name')
                                                ->label('Diserahkan Oleh')
                                                ->color('gray'),
                                            TextEntry::make('receiver.name')
                                                ->label('Diterima Oleh')
                                                ->weight('bold')
                                                ->color('primary'),
                                        ])->columns(1),
                                ]),

                                ComponentsGroup::make([
                                    ComponentsSection::make('Bukti Visual')
                                        ->schema([
                                            ImageEntry::make('asset_photo_path')
                                                ->label('Foto Kondisi Fisik')
                                                ->disk('public')
                                                ->width('100%')
                                                ->height(200)
                                                ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-sm']),

                                            ImageEntry::make('document_photo_path')
                                                ->label('Foto Dokumen / BAST')
                                                ->disk('public')
                                                ->width('100%')
                                                ->height(200)
                                                ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-sm']),
                                        ])->columns(1),
                                ]),
                            ]),

                        ComponentsSection::make('Catatan')
                            ->schema([
                                TextEntry::make('notes')
                                    ->label('Keterangan Tambahan')
                                    ->placeholder('Tidak ada catatan.'),
                            ]),
                    ]),
            ]);
    }

    /**
     * Karena kita sudah mendefinisikan schema di dalam Action, 
     * kita tidak memerlukan method infolist() terpisah di class ini.
     */
    public function isReadOnly(): bool
    {
        return true;
    }
}
