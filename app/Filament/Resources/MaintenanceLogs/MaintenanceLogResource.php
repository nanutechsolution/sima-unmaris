<?php

namespace App\Filament\Resources\MaintenanceLogs;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\MaintenanceLogs\Pages\CreateMaintenanceLog;
use App\Filament\Resources\MaintenanceLogs\Pages\EditMaintenanceLog;
use App\Filament\Resources\MaintenanceLogs\Pages\ListMaintenanceLogs;
use App\Filament\Resources\MaintenanceLogs\Schemas\MaintenanceLogForm;
use App\Filament\Resources\MaintenanceLogs\Tables\MaintenanceLogsTable;
use App\Models\MaintenanceLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceLogResource extends Resource
{
    protected static ?string $model = MaintenanceLog::class;


    protected static ?string $modelLabel = 'Log Perbaikan & Servis';
    protected static ?string $pluralModelLabel = 'Log Perbaikan & Servis';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::ASSET_MANAGEMENT->value;
    protected static ?int $navigationSort = 2; // Tampil di bawah menu Aset

    public static function form(Schema $schema): Schema
    {
        return MaintenanceLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceLogs::route('/'),
            'create' => CreateMaintenanceLog::route('/create'),
            'edit' => EditMaintenanceLog::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
