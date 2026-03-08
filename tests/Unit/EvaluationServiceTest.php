<?php

namespace Tests\Unit;

use App\Enums\Severity;
use App\Models\Dimension;
use App\Models\Question;
use App\Services\EvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationServiceTest extends TestCase
{
    use RefreshDatabase;

    private EvaluationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EvaluationService;
    }

    private function createDimensionWithQuestions(int $count): Dimension
    {
        $dimension = Dimension::factory()->create();
        Question::factory()->count($count)->create(['dimension_id' => $dimension->id]);

        return $dimension;
    }

    public function test_get_severity_returns_ok_when_score_is_zero()
    {
        $dimension = $this->createDimensionWithQuestions(10);
        $severity = $this->service->getSeverity($dimension, 0);

        $this->assertEquals(Severity::OK, $severity);
    }

    public function test_get_severity_for_9_questions_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(9);

        // Mild: 1-9
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 9));

        // Moderate: 10-18
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 10));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 18));

        // Severe: 19-27
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 19));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 27));
    }

    public function test_get_severity_for_10_questions_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(10);

        // Mild: 1-10
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 10));

        // Moderate: 11-20
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 11));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 20));

        // Severe: 21-30
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 21));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 30));
    }

    public function test_get_severity_for_11_questions_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(11);

        // Mild: 1-11
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 11));

        // Moderate: 12-22
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 12));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 22));

        // Severe: 23-33
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 23));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 33));
    }

    public function test_get_severity_for_12_questions_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(12);

        // Mild: 1-12
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 12));

        // Moderate: 13-24
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 13));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 24));

        // Severe: 25-36
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 25));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 36));
    }

    public function test_get_severity_for_0_questions_dimension_returns_ok_on_zero_score()
    {
        $dimension = $this->createDimensionWithQuestions(0);
        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));
    }

    public function test_get_severity_throws_exception_on_invalid_negative_score()
    {
        $dimension = $this->createDimensionWithQuestions(10);

        $this->expectException(\InvalidArgumentException::class);
        $this->service->getSeverity($dimension, -1);
    }

    public function test_get_severity_throws_exception_on_invalid_exceeding_score()
    {
        $dimension = $this->createDimensionWithQuestions(10); // max score is 30

        $this->expectException(\InvalidArgumentException::class);
        $this->service->getSeverity($dimension, 31);
    }

    public function test_get_severity_with_minimum_question_count()
    {
        $dimension = $this->createDimensionWithQuestions(1);

        // Score 0 = OK
        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));

        // Score 1 = LOW (1 <= 1)
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));

        // Score 2 = MID (2 >= 1+1 and 2 <= 2)
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 2));

        // Score 3 = HIGH (3 >= 3 and 3 <= 3)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 3));
    }

    public function test_get_severity_with_large_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(50);

        // Score 0 = OK
        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));

        // Score 50 = LOW (50 <= 50)
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 50));

        // Score 51 = MID (51 >= 51 and 51 <= 100)
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 51));

        // Score 100 = MID (100 <= 100)
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 100));

        // Score 101 = HIGH (101 >= 101 and 101 <= 150)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 101));

        // Score 150 = HIGH (max score)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 150));
    }

    public function test_get_severity_boundary_values_for_9_questions()
    {
        $dimension = $this->createDimensionWithQuestions(9);

        // OK: exactly 0
        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));

        // LOW boundary: 1 to 9
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 9));

        // MID boundary: 10 to 18
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 10));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 18));

        // HIGH boundary: 19 to 27
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 19));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 27));
    }

    public function test_get_severity_boundary_values_for_10_questions()
    {
        $dimension = $this->createDimensionWithQuestions(10);

        // OK: exactly 0
        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));

        // LOW boundary: 1 to 10
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 10));

        // MID boundary: 11 to 20
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 11));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 20));

        // HIGH boundary: 21 to 30
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 21));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 30));
    }

    public function test_get_severity_all_severity_levels_same_dimension()
    {
        $dimension = $this->createDimensionWithQuestions(10);

        $this->assertEquals(Severity::OK, $this->service->getSeverity($dimension, 0));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity($dimension, 5));
        $this->assertEquals(Severity::MID, $this->service->getSeverity($dimension, 15));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity($dimension, 25));
    }

    public function test_get_severity_consecutive_scores_no_gaps()
    {
        $dimension = $this->createDimensionWithQuestions(10);

        $previousSeverity = null;
        for ($score = 0; $score <= 30; $score++) {
            $severity = $this->service->getSeverity($dimension, $score);
            $this->assertInstanceOf(Severity::class, $severity, "No severity returned for score $score");

            if ($previousSeverity !== null && $score > 0) {
                $severityOrder = [
                    Severity::OK->value => 0,
                    Severity::LOW->value => 1,
                    Severity::MID->value => 2,
                    Severity::HIGH->value => 3,
                ];

                $prevOrder = array_search($previousSeverity->value, array_keys($severityOrder));
                $currOrder = array_search($severity->value, array_keys($severityOrder));

                $this->assertGreaterThanOrEqual($prevOrder, $currOrder,
                    "Severity should not decrease as score increases. Score $score: $previousSeverity->value -> {$severity->value}");
            }
            $previousSeverity = $severity;
        }
    }
}
