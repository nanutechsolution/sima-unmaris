<?php

namespace App\Filament\Widgets;

use App\Enums\AssetConditionEnum;
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

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 'full',
    ];

    protected function getData(): array
    {
        // Ambil agregat seluruh kondisi sekaligus (lebih hemat query daripada count per kondisi).
        $counts = Asset::query()
            ->select('condition')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        $labels = [];
        $data = [];
        $colors = [];

        // Tampilkan semua kondisi yang dikenal aplikasi, termasuk yang jumlahnya nol,
        // agar legenda dan warna tetap konsisten dengan badge kondisi di tabel.
        foreach (AssetConditionEnum::cases() as $condition) {
            $labels[] = $condition->getLabel();
            $data[] = (int) ($counts[$condition->value] ?? 0);
            $colors[] = match ($condition->value) {
                'good' => '#10b981',
                'fair' => '#f59e0b',
                'damaged' => '#ef4444',
                'bad' => '#64748b',
                default => '#94a3b8',
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Aset',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
