<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Import Enum Status & Kondisi Aset
use App\Enums\AssetStatusEnum;
use App\Enums\AssetConditionEnum;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use League\Uri\Components\Scheme;

class MaintenanceLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceLogs';

    protected static ?string $title = 'Riwayat Servis & Perbaikan';

    /**
     * Ekstrak skema form ke method terpisah (statis) agar bisa di-reuse 
     * oleh form utama dan dipanggil langsung oleh Actions.
     */
    public static function getFormSchema(): array
    {
        return [
         Section::make('Detail Servis')
                ->schema([
                    Forms\Components\DatePicker::make('maintenance_date')
                        ->label('Tanggal Servis')
                        ->native(false)
                        ->required()
                        ->default(now()),
                        
                    Forms\Components\Select::make('status')
                        ->label('Status Pengerjaan')
                        ->options([
                            'scheduled' => 'Terjadwal (Belum Mulai)',
                            'in_progress' => 'Sedang Dikerjakan',
                            'completed' => 'Selesai / Tuntas',
                        ])
                        ->required()
                        ->default('completed'),
                        
                    Forms\Components\TextInput::make('problem_description')
                        ->label('Keluhan / Kerusakan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                        
                    Forms\Components\Textarea::make('action_taken')
                        ->label('Tindakan yang Dilakukan (Opsional)')
                        ->columnSpanFull(),
                        
                    Forms\Components\TextInput::make('performed_by')
                        ->label('Dikerjakan Oleh (Teknisi/Bengkel)')
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('cost')
                        ->label('Biaya Perbaikan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                        
                    Forms\Components\FileUpload::make('receipt_photo_path')
                        ->label('Foto Nota / Kuitansi')
                        ->image()
                        ->disk('public')
                        ->directory('maintenance-receipts')
                        ->columnSpanFull(),
                        
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan Tambahan (Internal)')
                        ->columnSpanFull(),
                ])->columns(2),

            // --- FITUR BARU: SINKRONISASI KE MASTER ASET ---
           Section::make('Update Master Aset')
                ->description('Sesuaikan status dan kondisi fisik aset di database utama setelah pencatatan ini.')
                ->schema([
                    Forms\Components\Select::make('update_asset_status')
                        ->label('Ubah Status Aset')
                        ->options(AssetStatusEnum::class)
                        // Mengambil status aset saat ini sebagai nilai default
                        ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->status),
                        
                    Forms\Components\Select::make('update_asset_condition')
                        ->label('Ubah Kondisi Fisik')
                        ->options(AssetConditionEnum::class)
                        // Mengambil kondisi aset saat ini sebagai nilai default
                        ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->condition),
                ])->columns(2),
        ];
    }

    /**
     * Menggunakan standar asli Filament: form(Form $form)
     */
    public function form(Schema $form): Schema
    {
        return $form->schema(self::getFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('problem_description')
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('problem_description')
                    ->label('Kendala / Kerusakan')
                    ->limit(40)
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'in_progress' => 'Proses',
                        'completed' => 'Selesai',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Teknisi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('maintenance_date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                ActionsCreateAction::make()
                    ->label('Catat Servis Baru')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Buat Maintenance Log')
                    ->form(self::getFormSchema())
                    // Menggunakan mutateFormDataUsing untuk Action pada tabel (CreateAction)
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $asset = $livewire->getOwnerRecord();
                        $asset->update([
                            'status' => $data['update_asset_status'] ?? $asset->status,
                            'condition' => $data['update_asset_condition'] ?? $asset->condition,
                        ]);
                        
                        // Buang field sementara agar tidak error saat insert ke tabel maintenance_logs
                        unset($data['update_asset_status'], $data['update_asset_condition']);
                        
                        return $data;
                    }),
            ])
            // Menerapkan standar baru Filament 5: recordActions menggantikan actions
            ->recordActions([
                ActionsEditAction::make()
                    ->form(self::getFormSchema())
                    // Menggunakan mutateFormDataUsing untuk Action pada tabel (EditAction)
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $asset = $livewire->getOwnerRecord();
                        $asset->update([
                            'status' => $data['update_asset_status'] ?? $asset->status,
                            'condition' => $data['update_asset_condition'] ?? $asset->condition,
                        ]);
                        
                        unset($data['update_asset_status'], $data['update_asset_condition']);
                        
                        return $data;
                    }),
                ActionsDeleteAction::make(),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                ]),
            ]);
    }
}