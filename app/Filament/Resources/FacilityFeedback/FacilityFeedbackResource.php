<?php

namespace App\Filament\Resources\FacilityFeedback;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\FacilityFeedback\Pages\CreateFacilityFeedback;
use App\Filament\Resources\FacilityFeedback\Pages\EditFacilityFeedback;
use App\Filament\Resources\FacilityFeedback\Pages\ListFacilityFeedback;
use App\Filament\Resources\FacilityFeedback\Schemas\FacilityFeedbackForm;
use App\Filament\Resources\FacilityFeedback\Tables\FacilityFeedbackTable;
use App\Models\FacilityFeedback;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityFeedbackResource extends Resource
{
    protected static ?string $model = FacilityFeedback::class;

    protected static ?string $modelLabel = 'Template Survei (Form Builder)';
    protected static ?string $pluralModelLabel = 'Form Builder Survei';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::SERVICE_SATISFACTION->value;
    protected static ?int $navigationSort = 1;


    public static function form(Schema $schema): Schema
    {
        return FacilityFeedbackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacilityFeedbackTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFacilityFeedback::route('/'),
            'create' => CreateFacilityFeedback::route('/create'),
            'edit' => EditFacilityFeedback::route('/{record}/edit'),
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
