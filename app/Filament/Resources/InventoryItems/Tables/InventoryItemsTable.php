<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                TextColumn::make('name')->label('Nama Barang')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->searchable()->sortable(),
                TextColumn::make('unit')->label('Satuan'),
                TextColumn::make('current_stock')
                    ->label('Stok Saat Ini')
                    ->alignCenter()
                    ->weight('bold')
                    // Logic Low Stock Alert
                    ->color(fn($record) => $record->current_stock <= $record->min_stock ? 'danger' : 'success')
                    ->formatStateUsing(fn($state, $record) => "{$state} {$record->unit}"),
                TextColumn::make('min_stock')
                    ->label('Batas Aman')
                    ->size('xs')
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Alat Tulis Kantor' => 'Alat Tulis Kantor (ATK)',
                        'Peralatan Kebersihan' => 'Peralatan Kebersihan',
                        'Kebutuhan Dapur/Konsumsi' => 'Kebutuhan Dapur/Konsumsi',
                        'Tinta & Toner' => 'Tinta & Toner Printer',
                    ]),
            ])
            ->recordActions([
                Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading('Tambah Stok Barang (Restock)')
                    ->modalDescription(fn(InventoryItem $record) => "Masukkan jumlah penambahan stok untuk {$record->name}.")
                    ->form([
                        TextInput::make('quantity')
                            ->label('Jumlah Barang Masuk')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->suffix(fn(InventoryItem $record) => $record->unit),
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Restock')
                            ->default(now())
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Keterangan / Nomor Nota Pembelian')
                            ->placeholder('Cth: Pembelian rutin awal bulan dari Supplier A.'),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        InventoryTransaction::create([
                            'inventory_item_id' => $record->id,
                            'type' => 'in',
                            'quantity' => $data['quantity'],
                            'user_id' => auth()->id(),
                            'transaction_date' => $data['transaction_date'],
                            'notes' => $data['notes'],
                        ]);
                        Notification::make()
                            ->title('Stok berhasil ditambahkan!')
                            ->success()
                            ->send();
                    }),

                // PINTASAN 2: Barang Keluar (Distribusi ke Unit)
                Action::make('distribute')
                    ->label('Keluarkan')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->modalHeading('Distribusi Barang ke Unit')
                    ->modalDescription(fn(InventoryItem $record) => "Stok tersedia saat ini: {$record->current_stock} {$record->unit}.")
                    ->form([
                        TextInput::make('quantity')
                            ->label('Jumlah Barang Keluar')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            // Validasi agar tidak bisa mengeluarkan barang melebihi stok yang ada
                            ->maxValue(fn(InventoryItem $record) => max($record->current_stock, 1))
                            ->suffix(fn(InventoryItem $record) => $record->unit),
                        Select::make('department_id')
                            ->label('Unit Kerja Peminta')
                            ->relationship('transactions.department', 'name') // Mengambil relasi department
                            ->options(\App\Models\Department::pluck('name', 'id')) // Query langsung untuk dropdown
                            ->searchable()
                            ->required(),
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Penyerahan')
                            ->default(now())
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Keterangan Keperluan')
                            ->placeholder('Cth: Untuk keperluan akreditasi Fakultas Teknik.'),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        if ($record->current_stock < $data['quantity']) {
                            Notification::make()->title('Gagal: Stok tidak mencukupi!')->danger()->send();
                            return;
                        }

                        InventoryTransaction::create([
                            'inventory_item_id' => $record->id,
                            'type' => 'out',
                            'quantity' => $data['quantity'],
                            'department_id' => $data['department_id'],
                            'user_id' => auth()->id(),
                            'transaction_date' => $data['transaction_date'],
                            'notes' => $data['notes'],
                        ]);
                        Notification::make()
                            ->title('Barang berhasil didistribusikan!')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
