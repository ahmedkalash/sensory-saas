<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_progress_report_pdf_for_two_evaluations(): void
    {
        // 1. Setup Patient and Evaluations
        $patient = Patient::factory()->create();

        $eval1 = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->subMonths(6),
        ]);

        $eval2 = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now(),
        ]);

        // 2. We hit the route
        $response = $this->get(route('reports.progress', [
            'patient' => $patient->id,
            'eval_1' => $eval1->id,
            'eval_2' => $eval2->id,
        ]));

        // 3. Assertions
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $expectedFilename = 'مقارنة_تقدم_' . $patient->name . '_' . now()->format('Y-m-d') . '.pdf';
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $expectedFilename . '"');
    }
}
