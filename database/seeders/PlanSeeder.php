<?php

namespace Database\Seeders;

use App\Enums\PlanType;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'اشتراك سنوي'],
            [
                'type' => PlanType::Yearly,
                'duration_days' => 365,
                'quota_count' => null,
                'description' => 'وصول كامل لمدة سنة كاملة',
                'is_active' => true,
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'باقة 100 تقييم'],
            [
                'type' => PlanType::Quota,
                'duration_days' => null,
                'quota_count' => 100,
                'description' => 'حصة 100 تقييم بدون تاريخ انتهاء',
                'is_active' => true,
            ]
        );
    }
}
