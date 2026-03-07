<?php

namespace App\Filament\Resources\FacilityFeedback\Pages;

use App\Filament\Resources\FacilityFeedback\FacilityFeedbackResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditFacilityFeedback extends EditRecord
{
    protected static string $resource = FacilityFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
