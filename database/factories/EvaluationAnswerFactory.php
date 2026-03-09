<?php

namespace Database\Factories;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationAnswer>
 */
class EvaluationAnswerFactory extends Factory
{
    protected $model = EvaluationAnswer::class;

    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'question_text' => $this->faker->sentence(8),
            'dimension_name' => $this->faker->randomElement(['ضعف الاستجابة', 'فرط الاستجابة', 'تجنب الحسي', 'البحث الحسي']),
            'measurement_name' => $this->faker->randomElement(['مقياس البصري', 'مقياس السمعي', 'مقياس اللمسي']),
            'recommendations' => [fake()->sentence(), fake()->sentence()],
            'activities' => [fake()->sentence(), fake()->sentence()],
            'goals' => [fake()->sentence()],
            'score' => fake()->randomElement([Score::Never, Score::Sometimes, Score::Often, Score::Always]),
        ];
    }
}
