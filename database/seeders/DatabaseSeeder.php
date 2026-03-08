<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Measurement;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Measurement::truncate(); // todo handel it properly
        Question::truncate(); // todo handel it properly
        Dimension::truncate(); // todo handel it properly
        $this->call([
            AdminSeeder::class,
            AuditoryMeasurementSeeder::class,
            TactileMeasurementSeeder::class,
            VestibularMeasurementSeeder::class,
            ProprioceptiveMeasurementSeeder::class,
            OlfactoryMeasurementSeeder::class,
            GustatoryMeasurementSeeder::class,
            VisualMeasurementSeeder::class,
            DemoEvaluationSeeder::class,
        ]);
    }
}
