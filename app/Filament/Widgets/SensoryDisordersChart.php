<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SensoryDisordersChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return 'توزيع الاضطرابات الحسية (الأكثر شيوعاً)';
    }

    public function getDescription(): ?string
    {
        return 'تُحسب النسب بناءً على تكرار نقاط الضعف (غالباً/دائماً) عبر جميع التقييمات.';
    }

    protected function getData(): array
    {
        $measurements = \App\Models\Measurement::all();

        $data = [];
        $rawLabels = [];
        $totalWeaknesses = 0;

        foreach ($measurements as $measurement) {
            // Count total questions scoring 2 (Often) or 3 (Always) for this measurement across all evaluations
            $weaknessCount = \App\Models\EvaluationAnswer::query()
                ->where('measurement_name', $measurement->name)
                ->whereIn('score', [2, 3])
                ->count();

            if ($weaknessCount > 0) {
                $rawLabels[] = $measurement->name;
                $data[] = $weaknessCount;
                $totalWeaknesses += $weaknessCount;
            }
        }

        $formattedLabels = [];
        foreach ($rawLabels as $index => $label) {
            // Use 1 decimal place for better precision if needed, or stick to round for clean UI
            $percentage = $totalWeaknesses > 0 ? round(($data[$index] / $totalWeaknesses) * 100, 1) : 0;
            $formattedLabels[] = "{$label} ({$percentage}%)";
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي نقاط الضعف',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                        '#6366f1',
                    ],
                ],
            ],
            'labels' => $formattedLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
