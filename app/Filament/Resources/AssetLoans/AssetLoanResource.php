<?php

namespace App\Filament\Resources\AssetLoans;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\AssetLoans\Pages\CreateAssetLoan;
use App\Filament\Resources\AssetLoans\Pages\EditAssetLoan;
use App\Filament\Resources\AssetLoans\Pages\ListAssetLoans;
use App\Filament\Resources\AssetLoans\Schemas\AssetLoanForm;
use App\Filament\Resources\AssetLoans\Tables\AssetLoansTable;
use App\Models\AssetLoan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetLoanResource extends Resource
{
    protected static ?string $model = AssetLoan::class;
    protected static ?string $modelLabel = 'Peminjaman Aset';
    protected static ?string $pluralModelLabel = 'Peminjaman Aset';

    // Gabungkan di dalam grup Manajemen Aset
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::ASSET_MANAGEMENT->value;
    protected static ?int $navigationSort = 4;


    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AssetLoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetLoansTable::configure($table);
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
            'index' => ListAssetLoans::route('/'),
            'create' => CreateAssetLoan::route('/create'),
            'edit' => EditAssetLoan::route('/{record}/edit'),
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
