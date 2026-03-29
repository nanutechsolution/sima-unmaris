<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Spatie\Activitylog\Models\Activity;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;

use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\View as TableView;

class GlobalAuditTrail extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;

    protected static ?string $title = 'Log Aktivitas Sistem';
    protected static ?string $navigationLabel = 'Log Aktivitas Global';
    protected static ?string $slug = 'system-audit-logs';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::SYSTEM_AUDIT->value;
    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.global-audit-trail';

    /**
     * Memastikan hanya user yang punya permission yang bisa mengakses halaman ini.
     * Biasanya diizinkan untuk 'view_audit_trail' (Super Admin / Auditor)
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_system_audit_logs');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->with('causer'))
            ->columns([
                // 1. MENGGUNAKAN SPLIT UNTUK MEMBAGI KOLOM KIRI, TENGAH, KANAN
                Split::make([
                    // Bagian Kiri: Info Aktor/User
                    Stack::make([
                        TextColumn::make('causer.name')
                            ->weight(FontWeight::Bold)
                            ->placeholder('Sistem Otomatis / Bot')
                            ->searchable(),
                        TextColumn::make('causer.email')
                            ->color('gray')
                            ->size('sm')
                            ->placeholder('No Email Account'),
                    ]),

                    // Bagian Tengah: Tindakan & Modul
                    Stack::make([
                        TextColumn::make('event')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'created' => 'Data Dibuat',
                                'updated' => 'Data Diubah',
                                'deleted' => 'Data Dihapus',
                                'restored' => 'Dipulihkan',
                                default => strtoupper($state),
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                'restored' => 'primary',
                                default => 'gray',
                            }),
                        TextColumn::make('log_name')
                            ->formatStateUsing(fn($state) => 'Modul ' . ucfirst($state))
                            ->color('info')
                            ->size('xs'),
                    ])->alignCenter(),

                    // Bagian Kanan: Waktu Kejadian
                    Stack::make([
                        TextColumn::make('created_at')
                            ->dateTime('d M Y - H:i')
                            ->weight(FontWeight::SemiBold)
                            ->alignEnd(),
                        TextColumn::make('created_at_human')
                            ->getStateUsing(fn($record) => $record->created_at?->diffForHumans())
                            ->color('primary')
                            ->size('xs')
                            ->alignEnd(),
                    ]),
                ]),

                // 2. MENGGUNAKAN VIEW CUSTOM HTML YANG BISA DI-COLLAPSE
                TableView::make('filament.tables.columns.audit-activity-detail')
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Filter Tindakan')
                    ->options([
                        'created' => 'Data Dibuat (Created)',
                        'updated' => 'Data Diubah (Updated)',
                        'deleted' => 'Data Dihapus (Deleted)',
                    ]),
                SelectFilter::make('log_name')
                    ->label('Filter Modul')
                    ->options(fn() => Activity::select('log_name')->distinct()->pluck('log_name', 'log_name')->toArray()),
            ])
            ->actions([
                ActionsViewAction::make()
                    ->label('Detail Perubahan')
                    ->modalHeading('Rekam Jejak Data')
                    ->infolist([
                        Section::make('Perbandingan Data')
                            ->description('Melihat persis apa yang diubah oleh user.')
                            ->schema([
                                KeyValueEntry::make('properties.old')
                                    ->label('Data Lama')
                                    ->keyLabel('Kolom')
                                    ->valueLabel('Isi Lama'),

                                KeyValueEntry::make('properties.attributes')
                                    ->label('Data Baru')
                                    ->keyLabel('Kolom')
                                    ->valueLabel('Isi Baru'),
                            ])->columns(2),
                    ]),
            ])
            ->bulkActions([]); // Tidak ada bulk action delete karena ini audit trail (Immutable)
    }
}
