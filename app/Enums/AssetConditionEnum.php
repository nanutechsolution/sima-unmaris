<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;


enum AssetConditionEnum: string implements HasLabel, HasColor
{
    case GOOD = 'good';
    case FAIR = 'fair';
    case DAMAGED = 'damaged';
    case BAD = 'bad';
    case UNKNOWN = 'unknown';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::FAIR => 'Kurang Baik / Wajar',
            self::DAMAGED => 'Rusak',
            self::BAD => 'Hilang / Tidak Ditemukan',
            self::UNKNOWN => 'Tidak Diketahui',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GOOD => 'success',
            self::FAIR => 'warning',
            self::DAMAGED => 'danger',
            self::BAD => 'gray',
            self::UNKNOWN => 'secondary',
        };
    }
}
