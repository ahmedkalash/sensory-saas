<?php

namespace Database\Factories;

use App\Enums\PlanType;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'type' => PlanType::Yearly,
            'duration_days' => 365,
            'quota_count' => null,
            'description' => null,
            'is_active' => true,
        ];
    }

    public function quota(int $count = 100): static
    {
        return $this->state([
            'type' => PlanType::Quota,
            'duration_days' => null,
            'quota_count' => $count,
        ]);
    }

    public function yearly(int $days = 365): static
    {
        return $this->state([
            'type' => PlanType::Yearly,
            'duration_days' => $days,
            'quota_count' => null,
        ]);
    }
}
