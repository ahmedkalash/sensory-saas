<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Enums\Severity;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use App\Services\EvaluationService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReportService(new EvaluationService);
    }

    /** Helper: create an EvaluationAnswer with snapshot columns fully populated. */
    private function makeAnswer(Evaluation $evaluation, array $overrides = []): EvaluationAnswer
    {
        return EvaluationAnswer::factory()->create(array_merge([
            'evaluation_id' => $evaluation->id,
            'measurement_name' => 'Test Measurement',
            'dimension_name' => 'Test Dimension',
            'question_text' => 'Test Question',
            'recommendations' => ['Rec 1'],
            'goals' => ['Goal 1'],
            'activities' => ['Act 1'],
            'score' => Score::Sometimes,
        ], $overrides));
    }

    private function callBuildReportData(Evaluation $evaluation, ?int $measurementId = null): array
    {
        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        return $method->invoke($this->service, $evaluation, $measurementId);
    }

    public function test_build_report_data_structures_correctly_and_filters_weaknesses(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        // Score 1 (Not a weakness)
        $this->makeAnswer($evaluation, [
            'question_text' => 'Normal Question',
            'recommendations' => ['Rec 1'],
            'goals' => ['Goal 1'],
            'activities' => ['Act 1'],
            'score' => Score::Sometimes,
        ]);

        // Score 3 (Is a weakness)
        $this->makeAnswer($evaluation, [
            'question_text' => 'Weakness Question',
            'recommendations' => ['Urgent Rec'],
            'goals' => ['Urgent Goal'],
            'activities' => ['Urgent Act'],
            'score' => Score::Always,
        ]);

        $data = $this->callBuildReportData($evaluation);

        $this->assertArrayHasKey('evaluation', $data);
        $this->assertArrayHasKey('patient', $data);
        $this->assertArrayHasKey('measurements', $data);
        $this->assertEquals($patient->id, $data['patient']->id);

        $measurementsData = $data['measurements'];
        $this->assertCount(1, $measurementsData);
        $this->assertEquals('Test Measurement', $measurementsData[0]['name']);

        $dimensionsData = $measurementsData[0]['dimensions'];
        $this->assertCount(1, $dimensionsData);
        $this->assertEquals('Test Dimension', $dimensionsData[0]['name']);

        // Score 1 + 3 = 4
        $this->assertEquals(4, $dimensionsData[0]['total_score']);

        $weaknesses = $dimensionsData[0]['weaknesses'];
        $this->assertCount(1, $weaknesses);
        $this->assertEquals('Weakness Question', $weaknesses[0]['question_text']);
        $this->assertEquals(['Urgent Rec'], $weaknesses[0]['recommendations']);
        $this->assertEquals(['Urgent Goal'], $weaknesses[0]['goals']);
        $this->assertEquals(['Urgent Act'], $weaknesses[0]['activities']);
    }

    public function test_handles_empty_evaluation_with_no_answers(): void
    {
        $evaluation = Evaluation::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
        ]);

        $data = $this->callBuildReportData($evaluation);

        $this->assertArrayHasKey('measurements', $data);
        $this->assertCount(0, $data['measurements']);
    }

    public function test_build_report_data_with_multiple_measurements_and_dimensions(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->makeAnswer($evaluation, ['measurement_name' => 'Visual Scale', 'dimension_name' => 'Visual Dim 1', 'score' => Score::Always]);
        $this->makeAnswer($evaluation, ['measurement_name' => 'Visual Scale', 'dimension_name' => 'Visual Dim 2', 'score' => Score::Sometimes]);
        $this->makeAnswer($evaluation, ['measurement_name' => 'Auditory Scale', 'dimension_name' => 'Auditory Dim 1', 'score' => Score::Often]);

        $data = $this->callBuildReportData($evaluation);

        $this->assertCount(2, $data['measurements']);

        $names = array_column($data['measurements'], 'name');
        $this->assertContains('Visual Scale', $names);
        $this->assertContains('Auditory Scale', $names);

        $visual = collect($data['measurements'])->firstWhere('name', 'Visual Scale');
        $this->assertCount(2, $visual['dimensions']);

        $auditory = collect($data['measurements'])->firstWhere('name', 'Auditory Scale');
        $this->assertCount(1, $auditory['dimensions']);
    }

    public function test_build_report_data_with_score_2_boundary(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->makeAnswer($evaluation, [
            'question_text' => 'Score 2 Question',
            'recommendations' => ['Rec for 2'],
            'goals' => ['Goal for 2'],
            'activities' => ['Act for 2'],
            'score' => Score::Often,
        ]);

        $data = $this->callBuildReportData($evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount(1, $weaknesses);
        $this->assertEquals('Score 2 Question', $weaknesses[0]['question_text']);
        $this->assertEquals(['Rec for 2'], $weaknesses[0]['recommendations']);
    }

    public function test_build_report_data_with_null_empty_fields(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->makeAnswer($evaluation, [
            'question_text' => 'Empty Fields Question',
            'recommendations' => [],
            'goals' => [],
            'activities' => [],
            'score' => Score::Always,
        ]);

        $data = $this->callBuildReportData($evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount(1, $weaknesses);
        $this->assertIsArray($weaknesses[0]['recommendations']);
        $this->assertIsArray($weaknesses[0]['goals']);
        $this->assertIsArray($weaknesses[0]['activities']);
    }

    public function test_build_report_data_with_all_severity_levels(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        // 1 question per dimension → getSeverity(1, score)
        $this->makeAnswer($evaluation, ['dimension_name' => 'OK Dimension', 'score' => Score::Never]);
        $this->makeAnswer($evaluation, ['dimension_name' => 'LOW Dimension', 'score' => Score::Sometimes]);
        $this->makeAnswer($evaluation, ['dimension_name' => 'MID Dimension', 'score' => Score::Often]);
        $this->makeAnswer($evaluation, ['dimension_name' => 'HIGH Dimension', 'score' => Score::Always]);

        $data = $this->callBuildReportData($evaluation);

        $dimensions = collect($data['measurements'][0]['dimensions'])->keyBy('name');
        $this->assertEquals(Severity::OK, $dimensions['OK Dimension']['severity']);
        $this->assertEquals(Severity::LOW, $dimensions['LOW Dimension']['severity']);
        $this->assertEquals(Severity::MID, $dimensions['MID Dimension']['severity']);
        $this->assertEquals(Severity::HIGH, $dimensions['HIGH Dimension']['severity']);
    }

    public function test_build_report_data_with_multiple_weaknesses_same_dimension(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        for ($i = 0; $i < 5; $i++) {
            $this->makeAnswer($evaluation, [
                'question_text' => "Weakness Question $i",
                'recommendations' => ["Rec $i"],
                'goals' => ["Goal $i"],
                'activities' => ["Act $i"],
                'score' => Score::Always,
            ]);
        }

        $data = $this->callBuildReportData($evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount(5, $weaknesses);
    }

    public function test_build_report_data_with_mixed_severity_levels(): void
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->makeAnswer($evaluation, ['dimension_name' => 'Dimension 1', 'score' => Score::Never]);
        $this->makeAnswer($evaluation, ['dimension_name' => 'Dimension 2', 'score' => Score::Often]);
        $this->makeAnswer($evaluation, ['dimension_name' => 'Dimension 3', 'score' => Score::Always]);

        $data = $this->callBuildReportData($evaluation);

        $dimensions = collect($data['measurements'][0]['dimensions'])->keyBy('name');
        $this->assertEquals(Severity::OK, $dimensions['Dimension 1']['severity']);
        $this->assertEquals(Severity::MID, $dimensions['Dimension 2']['severity']);
        $this->assertEquals(Severity::HIGH, $dimensions['Dimension 3']['severity']);
        $this->assertEmpty($dimensions['Dimension 1']['weaknesses']);
        $this->assertCount(1, $dimensions['Dimension 2']['weaknesses']);
        $this->assertCount(1, $dimensions['Dimension 3']['weaknesses']);
    }

    public function test_render_html_returns_valid_html(): void
    {
        $patient = Patient::factory()->create(['name' => 'Test Patient']);
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->makeAnswer($evaluation, [
            'measurement_name' => 'Test Measurement',
            'score' => Score::Never,
        ]);

        $html = $this->service->renderGeneralReportHtml($evaluation);

        $this->assertStringContainsString('تقرير التقييم الشامل للمعالجة الحسية', $html);
        $this->assertStringContainsString('Test Patient', $html);
        $this->assertStringContainsString('Test Measurement', $html);
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('dir="rtl"', $html);
    }

    /**
     * Regression test: dimensions with no weaknesses (score 0 or 1)
     * should NOT render recommendation/goal/activity headers in the HTML.
     */
    public function test_dimension_without_weaknesses_omits_recommendation_headers_in_html(): void
    {
        $patient = Patient::factory()->create(['name' => 'No Weakness Patient']);
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        // All answers score 0 (Never) — no weaknesses at all
        $this->makeAnswer($evaluation, [
            'measurement_name' => 'Clean Scale',
            'dimension_name' => 'Healthy Dimension',
            'question_text' => 'Normal behavior question',
            'recommendations' => ['Should not appear'],
            'goals' => ['Should not appear'],
            'activities' => ['Should not appear'],
            'score' => Score::Never,
        ]);

        // Also add a dimension WITH a weakness to confirm it DOES appear
        $this->makeAnswer($evaluation, [
            'measurement_name' => 'Clean Scale',
            'dimension_name' => 'Problem Dimension',
            'question_text' => 'Problem behavior question',
            'recommendations' => ['Must appear'],
            'goals' => ['Must appear goal'],
            'activities' => ['Must appear activity'],
            'score' => Score::Always,
        ]);

        $html = $this->service->renderGeneralReportHtml($evaluation);

        // The "Problem Dimension" recommendations SHOULD appear
        $this->assertStringContainsString('Must appear', $html);

        // The "Healthy Dimension" recommendations should NOT appear
        $this->assertStringNotContainsString('Should not appear', $html);
    }
}
