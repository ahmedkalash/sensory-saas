<?php

namespace Tests\Feature;

use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Patient;
use App\Models\Question;
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
            'evaluation_date' => now()->startOfDay()
        ]);

        // Dimension 1: 9 Questions, all Score 0 (OK)
        $measurement1 = Measurement::factory()->create(['name' => 'Visual Scale']);
        $dim1 = Dimension::factory()->create(['measurement_id' => $measurement1->id, 'name' => 'Visual Dim 1']);

        for ($i = 0; $i < 9; $i++) {
            $q = Question::factory()->create(['dimension_id' => $dim1->id]);
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $this->evaluation->id,
                'question_id' => $q->id,
                'score' => \App\Enums\Score::Never, // 0
            ]);
        }

        // Dimension 2: 9 Questions, all Score 3 (Severe)
        $measurement2 = Measurement::factory()->create(['name' => 'Auditory Scale']);
        $dim2 = Dimension::factory()->create(['measurement_id' => $measurement2->id, 'name' => 'Auditory Dim 1']);

        $weaknessQ = Question::factory()->create([
            'dimension_id' => $dim2->id,
            'q_text' => 'This is a terrible weakness',
            'recommendations' => ['Urgent Rec 1'],
            'goals' => ['Urgent Goal 1'],
            'activities' => ['Urgent Activity 1'],
        ]);
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $this->evaluation->id,
            'question_id' => $weaknessQ->id,
            'score' => \App\Enums\Score::Always, // 3
        ]);

        for ($i = 0; $i < 8; $i++) {
            $q = Question::factory()->create(['dimension_id' => $dim2->id]);
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $this->evaluation->id,
                'question_id' => $q->id,
                'score' => \App\Enums\Score::Always, // 3
            ]);
        }
    }

    public function test_it_can_download_evaluation_report_as_pdf()
    {
        $response = $this->get(route('evaluations.report', $this->evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $expectedFilename = 'تقرير_' . $this->evaluation->patient->name . '_' . $this->evaluation->evaluation_date->format('Y-m-d') . '.pdf';

        $response->assertHeader(
            'Content-Disposition',
            'attachment; filename="' . $expectedFilename . '"'
        );
    }

    public function test_it_can_view_evaluation_report_as_html_and_renders_correct_data()
    {
        $response = $this->get(route('evaluations.report.html', $this->evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');

        // Assert header is present
        $response->assertSee('تقرير التقييم الشامل للمعالجة الحسية');
        $response->assertSee($this->evaluation->patient->name);

        // Assert Normal Dimension strings (Score 0)
        $response->assertSee('Visual Scale');
        $response->assertSee('لا توجد نقاط ضعف.');
        $response->assertSee('لا توجد نقاط ضعف في هذا المقياس.');

        // Assert Severe Dimension strings (Score 3)
        $response->assertSee('Auditory Scale');
        $response->assertSee('This is a terrible weakness');
        $response->assertSee('Urgent Rec 1');
        $response->assertSee('Urgent Goal 1');
        $response->assertSee('Urgent Activity 1');
    }

    public function test_it_handles_zero_answers_elegantly()
    {
        // Create an evaluation with 0 answers
        $emptyEvaluation = Evaluation::factory()->create([
            'patient_id' => $this->evaluation->patient_id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $responseHtml = $this->get(route('evaluations.report.html', $emptyEvaluation->id));
        $responseHtml->assertStatus(200);

        // All dimensions should show "No Weaknesses" because the score is effectively 0
        $responseHtml->assertSee('لا توجد نقاط ضعف.');

        $responsePdf = $this->get(route('evaluations.report', $emptyEvaluation->id));
        $responsePdf->assertStatus(200);
    }

    public function test_report_returns_404_for_non_existent_evaluation()
    {
        $response = $this->get(route('evaluations.report', 99999));

        $response->assertStatus(404);
    }

    public function test_html_report_returns_404_for_non_existent_evaluation()
    {
        $response = $this->get(route('evaluations.report.html', 99999));

        $response->assertStatus(404);
    }

    public function test_pdf_report_with_special_characters_in_patient_name()
    {
        $patient = Patient::factory()->create(['name' => 'John & Jane Doe']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_report_with_arabic_patient_name()
    {
        $patient = Patient::factory()->create(['name' => 'أحمد محمد علي']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $expectedFilename = 'تقرير_' . $patient->name . '_' . $evaluation->evaluation_date->format('Y-m-d') . '.pdf';
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $expectedFilename . '"');
    }

    public function test_pdf_report_with_very_long_patient_name()
    {
        $longName = str_repeat('أحمد محمد علي ', 20);
        $patient = Patient::factory()->create(['name' => $longName]);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $response = $this->get(route('evaluations.report', $evaluation->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_html_report_with_no_weaknesses_at_all()
    {
        $patient = Patient::factory()->create(['name' => 'No Weaknesses Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);

        for ($i = 0; $i < 10; $i++) {
            $q = Question::factory()->create(['dimension_id' => $dimension->id]);
            EvaluationAnswer::factory()->create([
                'evaluation_id' => $evaluation->id,
                'question_id' => $q->id,
                'score' => \App\Enums\Score::Never,
            ]);
        }

        $response = $this->get(route('evaluations.report.html', $evaluation->id));

        $response->assertStatus(200);
        $response->assertSee('لا توجد نقاط ضعف');
    }

    public function test_html_report_with_all_dimensions_having_weaknesses()
    {
        $patient = Patient::factory()->create(['name' => 'All Weaknesses Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);

        for ($d = 0; $d < 3; $d++) {
            $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => "Dimension $d"]);

            for ($i = 0; $i < 5; $i++) {
                $q = Question::factory()->create([
                    'dimension_id' => $dimension->id,
                    'q_text' => "Weakness Question $i for Dimension $d",
                ]);
                EvaluationAnswer::factory()->create([
                    'evaluation_id' => $evaluation->id,
                    'question_id' => $q->id,
                    'score' => \App\Enums\Score::Always,
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

    public function test_html_report_contains_proper_rtl_direction()
    {
        $patient = Patient::factory()->create(['name' => 'RTL Test Patient']);
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => now()->startOfDay()
        ]);

        $response = $this->get(route('evaluations.report.html', $evaluation->id));

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', escape: false);
    }

    public function test_html_report_contains_patient_information()
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
