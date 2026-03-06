<?php

namespace Database\Factories;

use App\Models\Dimension;
use App\Models\Measurement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dimension>
 */
class DimensionFactory extends Factory
{
    protected $model = Dimension::class;

    public function definition(): array
    {
        return [
            'measurement_id' => Measurement::factory(),
            'name' => fake()->word() . ' Dimension',
        ];
    }
}
