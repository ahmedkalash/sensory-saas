<?php

namespace App\Filament\Resources\Patients\Widgets;

use App\Models\Patient;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PatientProgressChart extends ChartWidget
{
    protected ?string $heading = 'مؤشر التقدم العام';

    public ?Model $record = null;

    protected function getData(): array
    {
        if (! $this->record instanceof Patient) {
            return [];
        }

        // Fetch evaluations ordered by date
        $evaluations = $this->record->evaluations()
            ->with(['answers'])
            ->orderBy('evaluation_date')
            ->get();

        $labels = [];
        $data = [];

        foreach ($evaluations as $evaluation) {
            $labels[] = $evaluation->evaluation_date->format('Y-m-d');

            // Calculate "Severity" sum as originally requested
            // 0 = No symptoms (Better), 3 = Major symptoms (Worse)
            $totalSeverity = $evaluation->answers->sum(fn ($a) => $a->score->value ?? 0);
            $data[] = $totalSeverity;
        }

        return [
            'datasets' => [
                [
                    'label' => 'صعود الخط يعني تحسن الطفل',
                    'data' => $data,
                    'borderColor' => '#10b981', // Emerald/Green for progress
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'تاريخ التقييم',
                        'font' => ['weight' => 'bold'],
                    ],
                ],
                'y' => [
                    'reverse' => true, // Flipping the axis: 0 (Good/Improvement) is at the TOP
                    'title' => [
                        'display' => true,
                        'text' => ' مجموع درجات الشدة لكل المقاييس',
                        'font' => ['weight' => 'bold'],
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { 
                            return 'مجموع النقاط: ' + context.parsed.y + ' (انخفاض الرقم يعني تحسن)'; 
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
