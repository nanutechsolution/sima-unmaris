<?php

namespace App\Enums;

enum NavigationGroupEnum: string
{
    case MASTER_DATA = 'Master Data Kampus';
    case ASSET_MANAGEMENT = 'Manajemen Aset';
    case INVENTORY_ATK = 'Inventori & ATK';
    case SYSTEM_AUDIT = 'Sistem & Audit Trail';


    /**
     * Mendapatkan label string untuk Filament.
     * Penggunaan di Resource: 
     * protected static ?string $navigationGroup = NavigationGroupEnum::MASTER_DATA->value;
     */
    public function getLabel(): string
    {
        return $this->value;
    }
}
