<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Patient;
use App\Models\Question;
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

    public function test_build_report_data_structures_correctly_and_filters_weaknesses()
    {
        // 1. Setup DB
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create([
            'measurement_id' => $measurement->id,
            'name' => 'Test Dimension',
        ]);

        // Create 1 question with score = 1 (Not a weakness)
        $q1 = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'q_text' => 'Normal Question',
            'recommendations' => ['Rec 1'],
            'goals' => ['Goal 1'],
            'activities' => ['Act 1'],
        ]);
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q1->id,
            'score' => Score::Sometimes, // Score 1
        ]);

        // Create 1 question with score = 3 (Is a weakness)
        $q2 = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'q_text' => 'Weakness Question',
            'recommendations' => ['Urgent Rec'],
            'goals' => ['Urgent Goal'],
            'activities' => ['Urgent Act'],
        ]);
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q2->id,
            'score' => Score::Always, // Score 3
        ]);

        // 2. Reflect on ReportService to access private buildReportData
        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        // 3. Act
        $data = $method->invoke($this->service, $evaluation);

        // 4. Assert structural root items
        $this->assertArrayHasKey('evaluation', $data);
        $this->assertArrayHasKey('patient', $data);
        $this->assertArrayHasKey('measurements', $data);

        // Assert patient matches
        $this->assertEquals($patient->id, $data['patient']->id);

        $measurementsData = $data['measurements'];
        $this->assertCount(1, $measurementsData);
        $this->assertEquals('Test Measurement', $measurementsData[0]['name']);

        $dimensionsData = $measurementsData[0]['dimensions'];
        $this->assertCount(1, $dimensionsData);
        $this->assertEquals('Test Dimension', $dimensionsData[0]['name']);

        // Score 1 + 3 = 4
        $this->assertEquals(4, $dimensionsData[0]['total_score']);

        // Assert Weaknesses filtering (Only q2 should be here since score >= 2)
        $weaknesses = $dimensionsData[0]['weaknesses'];
        $this->assertCount(1, $weaknesses);

        $weakness = $weaknesses[0];
        $this->assertEquals('Weakness Question', $weakness['question_text']);
        $this->assertEquals(['Urgent Rec'], $weakness['recommendations']);
        $this->assertEquals(['Urgent Goal'], $weakness['goals']);
        $this->assertEquals(['Urgent Act'], $weakness['activities']);
    }

    public function test_handles_empty_database_measurements()
    {
        $evaluation = Evaluation::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
        ]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $this->assertArrayHasKey('measurements', $data);
        $this->assertCount(0, $data['measurements']);
    }

    public function test_build_report_data_with_empty_evaluation()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $this->assertCount(1, $data['measurements']);
        $this->assertCount(1, $data['measurements'][0]['dimensions']);
        $this->assertEquals(0, $data['measurements'][0]['dimensions'][0]['total_score']);
        $this->assertEquals(\App\Enums\Severity::OK, $data['measurements'][0]['dimensions'][0]['severity']);
        $this->assertEmpty($data['measurements'][0]['dimensions'][0]['weaknesses']);
    }

    public function test_build_report_data_with_multiple_measurements_and_dimensions()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement1 = Measurement::factory()->create(['name' => 'Visual Scale']);
        $measurement2 = Measurement::factory()->create(['name' => 'Auditory Scale']);

        $dim1 = Dimension::factory()->create(['measurement_id' => $measurement1->id, 'name' => 'Visual Dim 1']);
        $dim2 = Dimension::factory()->create(['measurement_id' => $measurement1->id, 'name' => 'Visual Dim 2']);
        $dim3 = Dimension::factory()->create(['measurement_id' => $measurement2->id, 'name' => 'Auditory Dim 1']);

        $q1 = Question::factory()->create(['dimension_id' => $dim1->id]);
        $q2 = Question::factory()->create(['dimension_id' => $dim2->id]);
        $q3 = Question::factory()->create(['dimension_id' => $dim3->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q1->id,
            'score' => Score::Always,
        ]);
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q2->id,
            'score' => Score::Sometimes,
        ]);
        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q3->id,
            'score' => Score::Often,
        ]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $this->assertCount(2, $data['measurements']);
        $this->assertEquals('Visual Scale', $data['measurements'][0]['name']);
        $this->assertEquals('Auditory Scale', $data['measurements'][1]['name']);
        $this->assertCount(2, $data['measurements'][0]['dimensions']);
        $this->assertCount(1, $data['measurements'][1]['dimensions']);
    }

    public function test_build_report_data_with_score_2_boundary()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);

        $q1 = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'q_text' => 'Score 2 Question',
            'recommendations' => ['Rec for 2'],
            'goals' => ['Goal for 2'],
            'activities' => ['Act for 2'],
        ]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q1->id,
            'score' => Score::Often,
        ]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount(1, $weaknesses);
        $this->assertEquals('Score 2 Question', $weaknesses[0]['question_text']);
        $this->assertEquals(['Rec for 2'], $weaknesses[0]['recommendations']);
    }

    public function test_build_report_data_with_null_empty_fields()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);

        $q1 = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'q_text' => 'Empty Fields Question',
            'recommendations' => [],
            'goals' => [],
            'activities' => [],
        ]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q1->id,
            'score' => Score::Always,
        ]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount(1, $weaknesses);
        $this->assertIsArray($weaknesses[0]['recommendations']);
        $this->assertIsArray($weaknesses[0]['goals']);
        $this->assertIsArray($weaknesses[0]['activities']);
    }

    public function test_build_report_data_with_all_severity_levels()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);

        $dimOk = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'OK Dimension']);
        $dimLow = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'LOW Dimension']);
        $dimMid = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'MID Dimension']);
        $dimHigh = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'HIGH Dimension']);

        $qOk = Question::factory()->create(['dimension_id' => $dimOk->id]);
        $qLow = Question::factory()->create(['dimension_id' => $dimLow->id]);
        $qMid = Question::factory()->create(['dimension_id' => $dimMid->id]);
        $qHigh = Question::factory()->create(['dimension_id' => $dimHigh->id]);

        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $qOk->id, 'score' => Score::Never]);
        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $qLow->id, 'score' => Score::Sometimes]);
        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $qMid->id, 'score' => Score::Often]);
        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $qHigh->id, 'score' => Score::Always]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $dimensions = $data['measurements'][0]['dimensions'];
        $severities = collect($dimensions)->pluck('severity')->toArray();

        $this->assertContains(\App\Enums\Severity::OK, $severities);
        $this->assertContains(\App\Enums\Severity::LOW, $severities);
        $this->assertContains(\App\Enums\Severity::MID, $severities);
        $this->assertContains(\App\Enums\Severity::HIGH, $severities);
    }

    public function test_build_report_data_with_multiple_weaknesses_same_dimension()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);

        $weaknessCount = 5;
        for ($i = 0; $i < $weaknessCount; $i++) {
            $q = Question::factory()->create([
                'dimension_id' => $dimension->id,
                'q_text' => "Weakness Question $i",
                'recommendations' => ["Rec $i"],
                'goals' => ["Goal $i"],
                'activities' => ["Act $i"],
            ]);

            EvaluationAnswer::factory()->create([
                'evaluation_id' => $evaluation->id,
                'question_id' => $q->id,
                'score' => Score::Always,
            ]);
        }

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $weaknesses = $data['measurements'][0]['dimensions'][0]['weaknesses'];
        $this->assertCount($weaknessCount, $weaknesses);

        foreach ($weaknesses as $index => $weakness) {
            $this->assertEquals("Weakness Question $index", $weakness['question_text']);
            $this->assertEquals(["Rec $index"], $weakness['recommendations']);
        }
    }

    public function test_build_report_data_with_mixed_severity_levels()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);

        $dim1 = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Dimension 1']);
        $dim2 = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Dimension 2']);
        $dim3 = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Dimension 3']);

        $q1 = Question::factory()->create(['dimension_id' => $dim1->id]);
        $q2 = Question::factory()->create(['dimension_id' => $dim2->id]);
        $q3 = Question::factory()->create(['dimension_id' => $dim3->id]);

        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $q1->id, 'score' => Score::Never]);
        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $q2->id, 'score' => Score::Often]);
        EvaluationAnswer::factory()->create(['evaluation_id' => $evaluation->id, 'question_id' => $q3->id, 'score' => Score::Always]);

        $reflection = new ReflectionClass(ReportService::class);
        $method = $reflection->getMethod('buildReportData');
        $method->setAccessible(true);

        $data = $method->invoke($this->service, $evaluation);

        $dimensions = $data['measurements'][0]['dimensions'];

        $this->assertEquals(\App\Enums\Severity::OK, $dimensions[0]['severity']);
        $this->assertEquals(\App\Enums\Severity::MID, $dimensions[1]['severity']);
        $this->assertEquals(\App\Enums\Severity::HIGH, $dimensions[2]['severity']);

        $this->assertEmpty($dimensions[0]['weaknesses']);
        $this->assertCount(1, $dimensions[1]['weaknesses']);
        $this->assertCount(1, $dimensions[2]['weaknesses']);
    }

    public function test_render_html_returns_valid_html()
    {
        $patient = Patient::factory()->create(['name' => 'Test Patient']);
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $measurement = Measurement::factory()->create(['name' => 'Test Measurement']);
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id, 'name' => 'Test Dimension']);
        $q = Question::factory()->create(['dimension_id' => $dimension->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $q->id,
            'score' => Score::Never,
        ]);

        $html = $this->service->renderGeneralReportHtml($evaluation);

        $this->assertStringContainsString('تقرير التقييم الشامل للمعالجة الحسية', $html);
        $this->assertStringContainsString('Test Patient', $html);
        $this->assertStringContainsString('Test Measurement', $html);
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('dir="rtl"', $html);
    }
}
