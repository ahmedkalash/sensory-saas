<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The ordered list of seeders to run.
     * Add new seeders here for future updates — they will run once and never again.
     */
    private array $seeders = [
        AdminSeeder::class,

        AuditoryMeasurementSeeder::class,
        TactileMeasurementSeeder::class,
        VestibularMeasurementSeeder::class,
        ProprioceptiveMeasurementSeeder::class,
        OlfactoryMeasurementSeeder::class,
        GustatoryMeasurementSeeder::class,
        VisualMeasurementSeeder::class,

        DemoEvaluationSeeder::class,

        PlanSeeder::class,
    ];

    /**
     * Seed the application's database.
     * Each seeder runs exactly once, tracked in the seeder_history table.
     */
    public function run(): void
    {
        $ran_seeders = DB::table('seeder_history')->get();
        foreach ($this->seeders as $seederClass) {
            if ($ran_seeders->where('seeder', $seederClass)->isNotEmpty()) {
                continue;
            }

            $this->call($seederClass);

            DB::table('seeder_history')->insert([
                'seeder' => $seederClass,
                'ran_at' => now(),
            ]);
        }
    }
}
