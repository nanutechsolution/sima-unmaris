<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Infolist;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    // Ini merujuk ke relasi polymorphic 'activities' bawaan Spatie Activitylog
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Log Aktivitas & Audit Trail';
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Perubahan')
                    ->dateTime('d M Y - H:i:s')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Aktor / User')
                    ->description(fn ($record) => $record->causer?->email) // Menampilkan email di bawah nama
                    ->searchable()
                    ->placeholder('Sistem / Anonymous'),
                    
                Tables\Columns\TextColumn::make('event')
                    ->label('Jenis Aksi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Dibuat',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                        'restored' => 'Dipulihkan',
                        default => strtoupper($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc') // Selalu tampilkan log terbaru di atas
            ->filters([
                //
            ])
            ->headerActions([
                // Kosongkan agar tidak ada tombol "Create" di log
            ])
            ->actions([
                // ACTION: Lihat detail perubahan (Before - After)
              ViewAction::make()
                    ->label('Lihat Detail')
                    ->color('primary')
                    ->modalHeading('Detail Perubahan Data (Audit Trail)')
                    ->infolist([
                       Section::make('Komparasi Data')
                            ->description('Menampilkan perbedaan data sebelum dan sesudah aksi dilakukan.')
                            ->schema([
                               KeyValueEntry::make('properties.old')
                                    ->label('Data Lama (Sebelum)')
                                    ->keyLabel('Atribut')
                                    ->valueLabel('Nilai'),
                                    
                               KeyValueEntry::make('properties.attributes')
                                    ->label('Data Baru (Sesudah)')
                                    ->keyLabel('Atribut')
                                    ->valueLabel('Nilai'),
                            ])->columns(2),
                    ]),
            ])
            ->bulkActions([
                // Kosongkan agar tidak ada fitur Delete Bulk (Immutable constraint)
            ]);
    }

    /**
     * KUNCI KEAMANAN ENTERPRISE:
     * Memaksa seluruh tabel dan action di Relation Manager ini bersifat Read-Only.
     * Tidak ada satupun user yang bisa mengedit atau menghapus log histori.
     */
    public function isReadOnly(): bool
    {
        return true;
    }
}