<?php

namespace App\Filament\Resources\StockOpnames;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\StockOpnameResource\RelationManagers\DetailsRelationManager;
use App\Filament\Resources\StockOpnames\Pages\CreateStockOpname;
use App\Filament\Resources\StockOpnames\Pages\EditStockOpname;
use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Filament\Resources\StockOpnames\Schemas\StockOpnameForm;
use App\Filament\Resources\StockOpnames\Tables\StockOpnamesTable;
use App\Models\StockOpname;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;

    protected static ?string $modelLabel = 'Sesi Stock Opname';
    protected static ?string $pluralModelLabel = 'Stock Opname (Audit)';

    // Gabungkan di grup Sistem & Audit
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::SYSTEM_AUDIT->value;
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockOpnameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockOpnamesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'edit' => EditStockOpname::route('/{record}/edit'),
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
