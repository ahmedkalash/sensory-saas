<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'specialist_name' => fake()->name(),
            'title' => fake()->sentence(3),
            'evaluation_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'child_age' => fake()->numberBetween(3, 10).' سنوات',
            'draft_answers' => [],
        ];
    }
}
