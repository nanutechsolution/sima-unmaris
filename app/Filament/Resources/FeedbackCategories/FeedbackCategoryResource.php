<?php

namespace App\Filament\Resources\FeedbackCategories;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\FeedbackCategories\Pages\CreateFeedbackCategory;
use App\Filament\Resources\FeedbackCategories\Pages\EditFeedbackCategory;
use App\Filament\Resources\FeedbackCategories\Pages\ListFeedbackCategories;
use App\Filament\Resources\FeedbackCategories\Schemas\FeedbackCategoryForm;
use App\Filament\Resources\FeedbackCategories\Tables\FeedbackCategoriesTable;
use App\Models\FeedbackCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FeedbackCategoryResource extends Resource
{
    protected static ?string $model = FeedbackCategory::class;


    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Kategori Survei / Layanan';
    protected static ?string $pluralModelLabel = 'Kategori Survei';
    protected static ?string $navigationLabel = 'Kategori Survei / Layanan';

    // Gabungkan dengan grup Suara Kampus agar rapi
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroupEnum::SERVICE_SATISFACTION->value;
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FeedbackCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeedbackCategoriesTable::configure($table);
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
            'index' => ListFeedbackCategories::route('/'),
            'create' => CreateFeedbackCategory::route('/create'),
            'edit' => EditFeedbackCategory::route('/{record}/edit'),
        ];
    }
}
