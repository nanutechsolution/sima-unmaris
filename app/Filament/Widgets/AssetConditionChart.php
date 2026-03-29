<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class AssetConditionChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Komposisi Kondisi Aset';
    protected static ?int $sort = 2;
    protected string $color = 'info';
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 'full',
    ];
    protected function getData(): array
    {
        // Menghitung jumlah aset berdasarkan enum kondisinya
        $good = Asset::where('condition', 'good')->count();
        $fair = Asset::where('condition', 'fair')->count();
        $damaged = Asset::where('condition', 'damaged')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Aset',
                    'data' => [$good, $fair, $damaged],
                    // Warna disesuaikan dengan standar UI (Hijau, Kuning, Merah)
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['Kondisi Baik', 'Kurang Baik / Wajar', 'Kondisi Rusak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
