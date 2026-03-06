<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'dob' => fake()->dateTimeBetween('-10 years', '-3 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['ذكر', 'أنثى']),
            'school' => fake()->company(),
            'grade' => fake()->randomElement(['الروضة', 'الصف الأول', 'الصف الثاني']),
        ];
    }
}
