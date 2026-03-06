<?php

namespace Database\Factories;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Question;
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
            'question_id' => Question::factory(),
            'score' => fake()->randomElement([Score::Never, Score::Sometimes, Score::Often, Score::Always]),
        ];
    }
}
