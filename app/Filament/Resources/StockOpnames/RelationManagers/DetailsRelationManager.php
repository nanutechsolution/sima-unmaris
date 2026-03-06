<?php

namespace App\Filament\Resources\StockOpnameResource\RelationManagers;

use App\Enums\AssetConditionEnum;
use App\Models\Asset;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

// Import action yang benar
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = 'Hasil Scan / Audit Aset';

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('asset_id')
                ->relationship('asset', 'name')
                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->asset_code} - {$record->name}")
                ->label('Scan / Pilih Aset')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpanFull()
                ->helperText('Ketik kode aset atau nama barang yang sedang di-audit.'),

            Forms\Components\Toggle::make('is_found')
                ->label('Fisik Barang Ditemukan?')
                ->default(true)
                ->onColor('success')
                ->offColor('danger')
                ->live(),

            Forms\Components\Select::make('actual_condition')
                ->label('Kondisi Real Lapangan')
                ->options(AssetConditionEnum::class)
                ->required(fn(Get $get) => $get('is_found') === true)
                ->visible(fn(Get $get) => $get('is_found') === true)
                ->helperText('Perhatian: Mengubah ini akan langsung mengubah status di Master Aset.'),

            Forms\Components\Select::make('actual_room_id')
                ->relationship('actualRoom', 'name')
                ->label('Lokasi Ditemukan (Ruangan)')
                ->searchable()
                ->preload()
                ->visible(fn(Get $get) => $get('is_found') === true)
                ->helperText('Pilih jika ruangan tidak sesuai dengan database. Ini akan memindahkan aset tersebut.'),

            Forms\Components\Textarea::make('notes')
                ->label('Catatan Temuan')
                ->columnSpanFull()
                ->placeholder('Contoh: Layar retak / Aset dipindah tanpa lapor'),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('asset_id')
            ->columns([
                Tables\Columns\TextColumn::make('scanned_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y - H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('asset.asset_code')
                    ->label('Kode Aset')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\IconColumn::make('is_found')
                    ->label('Ditemukan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('actual_condition')
                    ->label('Kondisi Real')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => $state ? AssetConditionEnum::tryFrom($state)?->getLabel() : '-')
                    ->color(fn(?string $state) => $state ? AssetConditionEnum::tryFrom($state)?->getColor() : 'gray'),

                Tables\Columns\TextColumn::make('actualRoom.name')
                    ->label('Ruangan Real')
                    ->placeholder('Sesuai DB'),
            ])
            ->defaultSort('scanned_at', 'desc')
            ->headerActions([
                ActionsCreateAction::make()
                    ->label('Mulai Audit Aset (Scan)')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('Audit Fisik Aset Baru')
                    ->form(self::getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        // 1. Tambahkan waktu scan & nama auditor
                        $data['scanned_at'] = now();
                        $data['scanned_by'] = auth()->id();

                        // 2. SINKRONISASI KE MASTER ASET
                        $asset = Asset::find($data['asset_id']);
                        if ($asset) {
                            if (!$data['is_found']) {
                                // Jika tidak ditemukan, ubah status aset menjadi "Hilang"
                                $asset->update([
                                    'status' => 'lost',
                                    'condition' => 'bad', // Anggap buruk karena tidak bisa divalidasi
                                ]);
                            } else {
                                // Jika ditemukan, update kondisi dan pindahkan ruangan jika berbeda
                                $updatePayload = [
                                    'condition' => $data['actual_condition'] ?? $asset->condition,
                                ];

                                if (!empty($data['actual_room_id']) && $data['actual_room_id'] !== $asset->room_id) {
                                    $updatePayload['room_id'] = $data['actual_room_id'];
                                }

                                $asset->update($updatePayload);
                            }
                        }

                        return $data;
                    })
                    ->createAnother(true),
            ])
            ->recordActions([
                ActionsEditAction::make()
                    ->form(self::getFormSchema())
                    ->mutateFormDataUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        // SINKRONISASI SAAT EDIT AUDIT
                        $asset = Asset::find($record->asset_id ?? $data['asset_id']);
                        
                        if ($asset) {
                            if (!$data['is_found']) {
                                $asset->update(['status' => 'lost', 'condition' => 'bad']);
                            } else {
                                $updatePayload = [
                                    'condition' => $data['actual_condition'] ?? $asset->condition,
                                ];
                                
                                // Kembalikan status ke normal jika sebelumnya hilang
                                if ($asset->status === 'lost') {
                                    $updatePayload['status'] = 'available';
                                }
                                
                                if (!empty($data['actual_room_id']) && $data['actual_room_id'] !== $asset->room_id) {
                                    $updatePayload['room_id'] = $data['actual_room_id'];
                                }
                                
                                $asset->update($updatePayload);
                            }
                        }
                        return $data;
                    }),
                ActionsDeleteAction::make(),
            ]);
    }
}
