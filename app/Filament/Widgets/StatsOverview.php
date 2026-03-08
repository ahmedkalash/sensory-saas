<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return null;
    }

    protected function getStats(): array
    {
        $activePatients = \App\Models\Patient::where('status', \App\Enums\PatientStatus::ACTIVE)->count();

        $now = now();
        $evaluationsThisMonth = \App\Models\Evaluation::whereMonth('evaluation_date', $now->month)
            ->whereYear('evaluation_date', $now->year)
            ->count();

        $evaluationsLastMonth = \App\Models\Evaluation::whereMonth('evaluation_date', $now->copy()->subMonth()->month)
            ->whereYear('evaluation_date', $now->copy()->subMonth()->year)
            ->count();

        $diff = $evaluationsThisMonth - $evaluationsLastMonth;
        $trendIcon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $trendColor = $diff >= 0 ? 'success' : 'danger';
        $description = $diff >= 0 ? "+{$diff} زيادة عن الشهر الماضي" : "{$diff} نقص عن الشهر الماضي";

        $completedPrograms = \App\Models\Patient::where('status', \App\Enums\PatientStatus::COMPLETED)->count();

        return [
            Stat::make('الأطفال النشطين', $activePatients)
                ->description('حالات المتابعة الحالية')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('تقييمات هذا الشهر', $evaluationsThisMonth)
                ->description($description)
                ->descriptionIcon($trendIcon)
                ->color($trendColor),
            Stat::make('البرامج مكتملة', $completedPrograms)
                ->description('إجمالي قصص النجاح')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
