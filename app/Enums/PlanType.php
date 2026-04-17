<?php

namespace App\Enums;

enum PlanType: string
{
    case Yearly = 'yearly';
    case Quota = 'quota';

    public function label(): string
    {
        return match ($this) {
            PlanType::Yearly => 'سنوي',
            PlanType::Quota => 'حصة تقييمات',
        };
    }
}
