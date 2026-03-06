<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends BaseWidget
{
    // Mengatur urutan tampilan widget di atas
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Query performa tinggi untuk mengambil data agregat
        $totalAssets = Asset::count();
        $totalValue = Asset::sum('acquisition_value');
        $damagedAssets = Asset::where('condition', 'damaged')->count();

        return [
            Stat::make('Total Aset Kampus', $totalAssets)
                ->description('Jumlah seluruh fisik aset yang terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Sparkline pemanis UI

            Stat::make('Total Valuasi Aset', 'Rp ' . number_format($totalValue, 0, ',', '.'))
                ->description('Total akumulasi nilai perolehan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Aset Kondisi Rusak', $damagedAssets)
                ->description('Membutuhkan perbaikan atau dipensiunkan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([17, 16, 14, 15, 14, 13, 12]), // Sparkline trend perbaikan
        ];
    }
}