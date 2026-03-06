<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetStatusEnum: string implements HasLabel, HasColor
{
    case AVAILABLE = 'available';
    case IN_USE = 'in_use';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';
    case RETIRED = 'retired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::IN_USE => 'Sedang Digunakan',
            self::MAINTENANCE => 'Dalam Perbaikan',
            self::LOST => 'Hilang',
            self::RETIRED => 'Dipensiunkan / Dihapus',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::IN_USE => 'info',
            self::MAINTENANCE => 'warning',
            self::LOST => 'danger',
            self::RETIRED => 'gray',
        };
    }
}
