<?php

namespace App\Filament\Resources\FeedbackCategories\Pages;

use App\Filament\Resources\FeedbackCategories\FeedbackCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedbackCategory extends CreateRecord
{
    protected static string $resource = FeedbackCategoryResource::class;
}
