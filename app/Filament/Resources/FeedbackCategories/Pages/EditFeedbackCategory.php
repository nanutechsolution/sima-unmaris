<?php

namespace App\Filament\Resources\FeedbackCategories\Pages;

use App\Filament\Resources\FeedbackCategories\FeedbackCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeedbackCategory extends EditRecord
{
    protected static string $resource = FeedbackCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
