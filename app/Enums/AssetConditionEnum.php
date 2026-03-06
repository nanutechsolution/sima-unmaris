<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetConditionEnum: string implements HasLabel, HasColor
{
    case GOOD = 'good';
    case FAIR = 'fair';
    case DAMAGED = 'damaged';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::FAIR => 'Kurang Baik / Wajar',
            self::DAMAGED => 'Rusak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GOOD => 'success',
            self::FAIR => 'warning',
            self::DAMAGED => 'danger',
        };
    }
}