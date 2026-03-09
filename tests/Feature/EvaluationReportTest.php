<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationReportTest extends TestCase
{
    use RefreshDatabase;

    private Evaluation $evaluation;

    protected function setUp(): void
    {
        parent::setUp();

        $patient = Patient::factory()->create(['name' => 'John Doe']);
        $this->evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        // Dimension 1: 9 answers, all Score 0 (OK)
        for ($i = 0; $i < 9; $i++) {
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $this->evaluation->id,
                'measurement_name' => 'Visual Scale',
                'dimension_name' => 'Visual Dim 1',
                'question_text' => "Visual Question $i",
                'score' => Score::Never,
            ]);
        }

        // Dimension 2: 9 answers, all Score 3 (Severe) — first one is a named weakness
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $this->evaluation->id,
            'measurement_name' => 'Auditory Scale',
            'dimension_name' => 'Auditory Dim 1',
            'question_text' => 'This is a terrible weakness',
            'recommendations' => ['Urgent Rec 1'],
            'goals' => ['Urgent Goal 1'],
            'activities' => ['Urgent Activity 1'],
            'score' => Score::Always,
        ]);

        for ($i = 0; $i < 8; $i++) {
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $this->evaluation->id,
                'measurement_name' => 'Auditory Scale',
                'dimension_name' => 'Auditory Dim 1',
                'question_text' => "Auditory Question $i",
                'score' => Score::Always,
            ]);
        }
    }

    public function test_it_can_download_evaluation_report_as_pdf(): void
    {
        $response = $this->get(route('evaluations.report', $this->evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $expectedFilename = 'تقرير_'.$this->evaluation->patient->name.'_'.$this->evaluation->evaluation_date->format('Y-m-d').'.pdf';
        $response->assertHeader('Content-Disposition', 'attachment; filename="'.$expectedFilename.'"');
    }

    public function test_it_can_view_evaluation_report_as_html_and_renders_correct_data(): void
    {
        $response = $this->get(route('evaluations.report.html', $this->evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');

        $response->assertSee('تقرير التقييم الشامل للمعالجة الحسية');
        $response->assertSee($this->evaluation->patient->name);

        // Normal dimension (Score 0)
        $response->assertSee('Visual Scale');
        $response->assertSee('لا توجد نقاط ضعف.');
        $response->assertSee('لا توجد نقاط ضعف في هذا المقياس.');

        // Severe dimension (Score 3)
        $response->assertSee('Auditory Scale');
        $response->assertSee('This is a terrible weakness');
        $response->assertSee('Urgent Rec 1');
        $response->assertSee('Urgent Goal 1');
        $response->assertSee('Urgent Activity 1');
    }

    public function test_it_handles_zero_answers_elegantly(): void
    {
        $emptyEvaluation = Evaluation::factory()->create([
            'patient_id' => $this->evaluation->patient_id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        $responseHtml = $this->get(route('evaluations.report.html', $emptyEvaluation->id));
        $responseHtml->assertStatus(200);
        $responseHtml->assertSee('لا توجد نقاط ضعف.');

        $responsePdf = $this->get(route('evaluations.report', $emptyEvaluation->id));
        $responsePdf->assertStatus(200);
    }

    public function test_report_returns_404_for_non_existent_evaluation(): void
    {
        $response = $this->get(route('evaluations.report', 99999));
        $response->assertStatus(404);
    }

    public function test_html_report_returns_404_for_non_existent_evaluation(): void
    {
        $response = $this->get(route('evaluations.report.html', 99999));
        $response->assertStatus(404);
    }

    public function test_pdf_report_with_special_characters_in_patient_name(): void
    {
        $patient = Patient::factory()->create(['name' => 'John & Jane Doe']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_report_with_arabic_patient_name(): void
    {
        $patient = Patient::factory()->create(['name' => 'أحمد محمد علي']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $expectedFilename = 'تقرير_'.$patient->name.'_'.$evaluation->evaluation_date->format('Y-m-d').'.pdf';
        $response->assertHeader('Content-Disposition', 'attachment; filename="'.$expectedFilename.'"');
    }

    public function test_pdf_report_with_very_long_patient_name(): void
    {
        $longName = str_repeat('أحمد محمد علي ', 20);
        $patient = Patient::factory()->create(['name' => $longName]);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_html_report_with_no_weaknesses_at_all(): void
    {
        $patient = Patient::factory()->create(['name' => 'No Weaknesses Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        for ($i = 0; $i < 10; $i++) {
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $evaluation->id,
                'measurement_name' => 'Test Measurement',
                'dimension_name' => 'Test Dimension',
                'score' => Score::Never,
            ]);
        }

        $response = $this->get(route('evaluations.report.html', $evaluation->id));
        $response->assertStatus(200);
        $response->assertSee('لا توجد نقاط ضعف');
    }

    public function test_html_report_with_all_dimensions_having_weaknesses(): void
    {
        $patient = Patient::factory()->create(['name' => 'All Weaknesses Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        for ($d = 0; $d < 3; $d++) {
            for ($i = 0; $i < 5; $i++) {
                EvaluationAnswer::factory()->create([
                    'evaluation_id' => $evaluation->id,
                    'measurement_name' => 'Test Measurement',
                    'dimension_name' => "Dimension $d",
                    'question_text' => "Weakness Question $i for Dimension $d",
                    'score' => Score::Always,
                ]);
            }
        }

        $response = $this->get(route('evaluations.report.html', $evaluation->id));
        $response->assertStatus(200);
        $response->assertSee('Weakness Question');
        $response->assertSee('Dimension 0');
        $response->assertSee('Dimension 1');
        $response->assertSee('Dimension 2');
    }

    public function test_html_report_contains_proper_rtl_direction(): void
    {
        $patient = Patient::factory()->create(['name' => 'RTL Test Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
        ]);

        $response = $this->get(route('evaluations.report.html', $evaluation->id));
        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', escape: false);
    }

    public function test_html_report_contains_patient_information(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'Complete Info Patient',
            'gender' => 'ذكر',
            'school' => 'مدرسة الاختبار',
            'grade' => 'الأول',
        ]);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay(),
            'specialist_name' => 'أخصائي الاختبار',
            'child_age' => '5 سنوات',
        ]);

        $response = $this->get(route('evaluations.report.html', $evaluation->id));
        $response->assertStatus(200);
        $response->assertSee('Complete Info Patient');
        $response->assertSee('ذكر');
        $response->assertSee('5 سنوات');
        $response->assertSee('أخصائي الاختبار');
    }
}
