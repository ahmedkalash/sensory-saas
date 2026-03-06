<?php

namespace Database\Factories;

use App\Models\Dimension;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'dimension_id' => Dimension::factory(),
            'q_text' => fake()->sentence(),
            'recommendations' => [fake()->sentence(), fake()->sentence()],
            'goals' => [fake()->sentence()],
            'activities' => [fake()->sentence(), fake()->sentence()],
        ];
    }
}
