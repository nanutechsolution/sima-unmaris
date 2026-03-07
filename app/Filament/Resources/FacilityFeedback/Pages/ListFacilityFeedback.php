<?php

namespace App\Filament\Resources\FacilityFeedback\Pages;

use App\Filament\Resources\FacilityFeedback\FacilityFeedbackResource;
use App\Filament\Resources\FacilityFeedback\Widgets\SurveyStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFacilityFeedback extends ListRecords
{
    protected static string $resource = FacilityFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SurveyStatsOverview::class,
        ];
    }
}
