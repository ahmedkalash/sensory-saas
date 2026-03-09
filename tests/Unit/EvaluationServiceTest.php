<?php

namespace Tests\Unit;

use App\Enums\Severity;
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

    public function test_get_severity_returns_ok_when_score_is_zero(): void
    {
        $severity = $this->service->getSeverity(10, 0);
        $this->assertEquals(Severity::OK, $severity);
    }

    public function test_get_severity_for_9_questions_dimension(): void
    {
        // Mild: 1-9
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(9, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(9, 9));

        // Moderate: 10-18
        $this->assertEquals(Severity::MID, $this->service->getSeverity(9, 10));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(9, 18));

        // Severe: 19-27
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(9, 19));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(9, 27));
    }

    public function test_get_severity_for_10_questions_dimension(): void
    {
        // Mild: 1-10
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(10, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(10, 10));

        // Moderate: 11-20
        $this->assertEquals(Severity::MID, $this->service->getSeverity(10, 11));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(10, 20));

        // Severe: 21-30
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(10, 21));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(10, 30));
    }

    public function test_get_severity_for_11_questions_dimension(): void
    {
        // Mild: 1-11
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(11, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(11, 11));

        // Moderate: 12-22
        $this->assertEquals(Severity::MID, $this->service->getSeverity(11, 12));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(11, 22));

        // Severe: 23-33
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(11, 23));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(11, 33));
    }

    public function test_get_severity_for_12_questions_dimension(): void
    {
        // Mild: 1-12
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(12, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(12, 12));

        // Moderate: 13-24
        $this->assertEquals(Severity::MID, $this->service->getSeverity(12, 13));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(12, 24));

        // Severe: 25-36
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(12, 25));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(12, 36));
    }

    public function test_get_severity_for_0_questions_dimension_returns_ok_on_zero_score(): void
    {
        $this->assertEquals(Severity::OK, $this->service->getSeverity(0, 0));
    }

    public function test_get_severity_throws_exception_on_invalid_negative_score(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->getSeverity(10, -1);
    }

    public function test_get_severity_throws_exception_on_invalid_exceeding_score(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->getSeverity(10, 31); // max score is 10 * 3 = 30
    }

    public function test_get_severity_with_minimum_question_count(): void
    {
        // Score 0 = OK
        $this->assertEquals(Severity::OK, $this->service->getSeverity(1, 0));

        // Score 1 = LOW (1 <= 1)
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(1, 1));

        // Score 2 = MID (2 >= 1+1 and 2 <= 2)
        $this->assertEquals(Severity::MID, $this->service->getSeverity(1, 2));

        // Score 3 = HIGH (3 >= 3 and 3 <= 3)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(1, 3));
    }

    public function test_get_severity_with_large_dimension(): void
    {
        // Score 0 = OK
        $this->assertEquals(Severity::OK, $this->service->getSeverity(50, 0));

        // Score 50 = LOW (50 <= 50)
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(50, 50));

        // Score 51 = MID (51 >= 51 and 51 <= 100)
        $this->assertEquals(Severity::MID, $this->service->getSeverity(50, 51));

        // Score 100 = MID (100 <= 100)
        $this->assertEquals(Severity::MID, $this->service->getSeverity(50, 100));

        // Score 101 = HIGH (101 >= 101 and 101 <= 150)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(50, 101));

        // Score 150 = HIGH (max score)
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(50, 150));
    }

    public function test_get_severity_boundary_values_for_9_questions(): void
    {
        // OK: exactly 0
        $this->assertEquals(Severity::OK, $this->service->getSeverity(9, 0));

        // LOW boundary: 1 to 9
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(9, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(9, 9));

        // MID boundary: 10 to 18
        $this->assertEquals(Severity::MID, $this->service->getSeverity(9, 10));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(9, 18));

        // HIGH boundary: 19 to 27
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(9, 19));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(9, 27));
    }

    public function test_get_severity_boundary_values_for_10_questions(): void
    {
        // OK: exactly 0
        $this->assertEquals(Severity::OK, $this->service->getSeverity(10, 0));

        // LOW boundary: 1 to 10
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(10, 1));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(10, 10));

        // MID boundary: 11 to 20
        $this->assertEquals(Severity::MID, $this->service->getSeverity(10, 11));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(10, 20));

        // HIGH boundary: 21 to 30
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(10, 21));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(10, 30));
    }

    public function test_get_severity_all_severity_levels_same_dimension(): void
    {
        $this->assertEquals(Severity::OK, $this->service->getSeverity(10, 0));
        $this->assertEquals(Severity::LOW, $this->service->getSeverity(10, 5));
        $this->assertEquals(Severity::MID, $this->service->getSeverity(10, 15));
        $this->assertEquals(Severity::HIGH, $this->service->getSeverity(10, 25));
    }

    public function test_get_severity_consecutive_scores_no_gaps(): void
    {
        $previousSeverity = null;
        for ($score = 0; $score <= 30; $score++) {
            $severity = $this->service->getSeverity(10, $score);
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

                $this->assertGreaterThanOrEqual(
                    $prevOrder,
                    $currOrder,
                    "Severity should not decrease as score increases. Score $score: $previousSeverity->value -> {$severity->value}"
                );
            }
            $previousSeverity = $severity;
        }
    }
}
