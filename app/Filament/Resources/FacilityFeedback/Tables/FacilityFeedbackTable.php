<?php

namespace App\Filament\Resources\FacilityFeedback\Tables;

use App\Models\FacilityFeedback;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ForceDeleteBulkAction as ActionsForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction as ActionsRestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section as ComponentsSection;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FacilityFeedbackTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Survei')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'draft' => 'Draft',
                        default => $state,
                    }),

                TextColumn::make('responses_count')
                    ->counts('responses')
                    ->label('Respon')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'active' => 'Aktif',
                        'draft' => 'Draft',
                    ]),
            ])
            ->recordActions([
                // --- FITUR BAGIKAN LINK (FILAMENT 5.x BEST PRACTICE) ---
                ActionsAction::make('share_link')
                    ->label('Bagikan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn(FacilityFeedback $record) => $record->status === 'active')
                    // Konfigurasi Modal sesuai Dokumentasi 5.x
                    ->modalHeading('Bagikan Formulir Survei')
                    ->modalDescription('Salin tautan atau gunakan QR Code di bawah untuk akses responden.')
                    ->modalIcon('heroicon-o-share')
                    ->modalIconColor('info')
                    ->modalWidth('md')
                    ->modalAlignment('center')
                    // Modal ini bersifat Read-only (Informasi)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Selesai')
                    ->form(fn(FacilityFeedback $record) => [
                        ComponentsSection::make()
                            ->schema([
                                // Bagian QR Code
                                Placeholder::make('qr_preview')
                                    ->label('')
                                    ->content(function () use ($record) {
                                        $url = route('survey.show', $record->id);
                                        $qr = QrCode::format('svg')
                                            ->size(200)
                                            ->margin(1)
                                            ->color(27, 42, 102) // Warna Biru UNMARIS #1B2A66
                                            ->generate($url);

                                        return new HtmlString('
                                            <div class="flex flex-col items-center justify-center space-y-4 justify-items-center">
                                                <div class="p-4 bg-white ring-1 ring-gray-200 shadow-sm rounded-2xl">
                                                    ' . $qr . '
                                                </div>
                                                <div class="text-center">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pindai Untuk Mengisi</span>
                                                </div>
                                            </div>
                                        ');
                                    }),

                                // Input Link dengan Action Copy bawaan Filament (Suffix Action)
                                TextInput::make('public_url')
                                    ->label('Tautan Publik')
                                    ->default(route('survey.show', $record->id))
                                    ->readOnly()
                                    ->copyable(copyMessage: 'Tautan berhasil disalin ke clipboard!', copyMessageDuration: 1500)

                            ])
                    ]),
                ActionsEditAction::make(),
                ActionsDeleteAction::make(),
            ])
            ->toolbarActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                    ActionsForceDeleteBulkAction::make(),
                    ActionsRestoreBulkAction::make(),
                ]),
            ]);
    }
}
