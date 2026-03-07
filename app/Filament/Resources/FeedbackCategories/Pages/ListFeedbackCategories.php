<?php

namespace App\Filament\Resources\FeedbackCategories\Pages;

use App\Filament\Resources\FeedbackCategories\FeedbackCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackCategories extends ListRecords
{
    protected static string $resource = FeedbackCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
