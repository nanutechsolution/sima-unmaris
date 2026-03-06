<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('contact_person')
                    ->label('Kontak Person')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable() // Admin bisa langsung copy nomor telepon
                    ->copyMessage('Nomor telepon disalin!'),
                TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label('Jumlah Aset Disuplai')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Export Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('success'),
                    BulkAction::make('export_pdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-text')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            // Membuat template HTML sederhana langsung di dalam fungsi (tanpa view terpisah)
                            $html = '<h2 style="text-align:center; font-family: sans-serif;">Laporan Data Mitra/Supplier</h2>';
                            $html .= '<table border="1" width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">';
                            $html .= '<tr style="background-color: #f3f4f6; text-align: left;">
                                        <th>Nama Perusahaan</th>
                                        <th>Kontak Person</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                      </tr>';

                            foreach ($records as $record) {
                                $html .= "<tr>
                                    <td>{$record->name}</td>
                                    <td>{$record->contact_person}</td>
                                    <td>{$record->phone}</td>
                                    <td>{$record->address}</td>
                                </tr>";
                            }
                            $html .= '</table>';

                            // Render HTML menjadi PDF
                            $pdf = Pdf::loadHTML($html);

                            // Langsung download file PDF-nya
                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'Laporan_Supplier_' . date('Ymd_His') . '.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
