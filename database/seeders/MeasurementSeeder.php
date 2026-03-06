<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Measurement;
use App\Models\Question;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate in FK-safe order
        Question::query()->delete();
        Dimension::query()->delete();
        Measurement::query()->delete();
    }
}
