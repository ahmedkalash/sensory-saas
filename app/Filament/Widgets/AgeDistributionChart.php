<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AgeDistributionChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): ?string
    {
        return 'توزيع أعمار الأطفال';
    }

    public function getDescription(): ?string
    {
        return 'يوضح المحور الأفقي الفئة العمرية للطفل، والمحور الرأسي عدد الأطفال في تلك الفئة.';
    }

    protected function getData(): array
    {
        $patients = \App\Models\Patient::all();

        $groups = [
            '0-2' => 0,
            '3-5' => 0,
            '6-8' => 0,
            '9-11' => 0,
            '12+' => 0,
        ];

        foreach ($patients as $patient) {
            $age = \Carbon\Carbon::parse($patient->dob)->age;

            if ($age <= 2) {
                $groups['0-2']++;
            } elseif ($age <= 5) {
                $groups['3-5']++;
            } elseif ($age <= 8) {
                $groups['6-8']++;
            } elseif ($age <= 11) {
                $groups['9-11']++;
            } else {
                $groups['12+']++;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الأطفال',
                    'data' => array_values($groups),
                    'backgroundColor' => '#6366f1',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($groups),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'الفئة العمرية (بالسنوات)',
                    ],
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'عدد الأطفال',
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
