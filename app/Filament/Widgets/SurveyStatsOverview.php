<?php

namespace App\Filament\Resources\FacilityFeedback\Widgets;

use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SurveyStatsOverview extends BaseWidget
{
    /**
     * Mengambil data statistik dari database untuk ditampilkan di dashboard survei.
     */
    protected function getStats(): array
    {
        // 1. Hitung Total Template Form yang tersedia
        $totalForms = FacilityFeedback::count();

        // 2. Hitung Total Respon/Jawaban yang sudah masuk dari semua survei
        $totalResponses = SurveyResponse::count();

        // 3. Hitung Survei yang sedang aktif (bisa diisi publik)
        $activeSurveys = FacilityFeedback::where('status', 'active')->count();

        return [
            Stat::make('Total Template Survei', $totalForms)
                ->description('Jumlah formulir yang telah dirancang')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Total Respon Masuk', $totalResponses)
                ->description('Total suara mahasiswa & dosen')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->chart([7, 10, 5, 12, 18, 14, 25]) // Sparkline simulasi tren
                ->color('success'),

            Stat::make('Survei Aktif', $activeSurveys)
                ->description('Formulir yang sedang dibuka publik')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($activeSurveys > 0 ? 'warning' : 'gray'),
        ];
    }
}