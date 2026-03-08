<?php

namespace Tests\Unit;

use App\Enums\Score;
use App\Enums\Severity;
use Tests\TestCase;

class EnumsTest extends TestCase
{
    /**
     * Test Score enum values.
     */
    public function test_score_enum_values()
    {
        $this->assertEquals(0, Score::Never->value);
        $this->assertEquals(1, Score::Sometimes->value);
        $this->assertEquals(2, Score::Often->value);
        $this->assertEquals(3, Score::Always->value);
    }

    /**
     * Test Score enum labels.
     */
    public function test_score_enum_labels()
    {
        $this->assertEquals('لايوجد', Score::Never->label());
        $this->assertEquals('أحيانا', Score::Sometimes->label());
        $this->assertEquals('غالبا', Score::Often->label());
        $this->assertEquals('موجود دائما', Score::Always->label());
    }

    /**
     * Test Score enum cases exist.
     */
    public function test_score_enum_cases()
    {
        $cases = Score::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(Score::Never, $cases);
        $this->assertContains(Score::Sometimes, $cases);
        $this->assertContains(Score::Often, $cases);
        $this->assertContains(Score::Always, $cases);
    }

    /**
     * Test Score enum from value.
     */
    public function test_score_enum_from_value()
    {
        $this->assertEquals(Score::Never, Score::from(0));
        $this->assertEquals(Score::Sometimes, Score::from(1));
        $this->assertEquals(Score::Often, Score::from(2));
        $this->assertEquals(Score::Always, Score::from(3));
    }

    /**
     * Test Score enum invalid value throws exception.
     */
    public function test_score_enum_invalid_value_throws_exception()
    {
        $this->expectException(\ValueError::class);
        Score::from(4);
    }

    public function test_score_enum_negative_value_throws_exception()
    {
        $this->expectException(\ValueError::class);
        Score::from(-1);
    }

    /**
     * Test Score enum string representation.
     */
    public function test_score_enum_string_representation()
    {
        $this->assertEquals('0', (string) Score::Never->value);
        $this->assertIsString(Score::Never->label());
        $this->assertEquals('لايوجد', Score::Never->label());
    }

    /**
     * Test Score enum is method helpers.
     */
    public function test_score_enum_comparison()
    {
        $this->assertTrue(Score::Never->value < Score::Sometimes->value);
        $this->assertTrue(Score::Sometimes->value < Score::Often->value);
        $this->assertTrue(Score::Often->value < Score::Always->value);

        $this->assertFalse(Score::Never->value > Score::Sometimes->value);
        $this->assertFalse(Score::Always->value < Score::Often->value);
    }

    /**
     * Test Score enum all values are non-negative.
     */
    public function test_score_enum_all_values_are_non_negative()
    {
        foreach (Score::cases() as $case) {
            $this->assertGreaterThanOrEqual(0, $case->value, "Score {$case->name} has negative value");
        }
    }

    /**
     * Test Score enum values are sequential.
     */
    public function test_score_enum_values_are_sequential()
    {
        $values = collect(Score::cases())->map(fn ($case) => $case->value)->sort()->values();

        $this->assertEquals([0, 1, 2, 3], $values->toArray());
    }

    /**
     * Test Severity enum values.
     */
    public function test_severity_enum_values()
    {
        $this->assertEquals('لا يوجد اضراب', Severity::OK->value);
        $this->assertEquals('بسيط', Severity::LOW->value);
        $this->assertEquals('متوسط', Severity::MID->value);
        $this->assertEquals('شديد', Severity::HIGH->value);
    }

    /**
     * Test Severity enum cases exist.
     */
    public function test_severity_enum_cases()
    {
        $cases = Severity::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(Severity::OK, $cases);
        $this->assertContains(Severity::LOW, $cases);
        $this->assertContains(Severity::MID, $cases);
        $this->assertContains(Severity::HIGH, $cases);
    }

    /**
     * Test Severity enum from value.
     */
    public function test_severity_enum_from_value()
    {
        $this->assertEquals(Severity::OK, Severity::from('لا يوجد اضراب'));
        $this->assertEquals(Severity::LOW, Severity::from('بسيط'));
        $this->assertEquals(Severity::MID, Severity::from('متوسط'));
        $this->assertEquals(Severity::HIGH, Severity::from('شديد'));
    }

    /**
     * Test Severity enum invalid value throws exception.
     */
    public function test_severity_enum_invalid_value_throws_exception()
    {
        $this->expectException(\ValueError::class);
        Severity::from('invalid');
    }

    /**
     * Test Severity enum string representation.
     */
    public function test_severity_enum_string_representation()
    {
        $this->assertIsString(Severity::OK->value);
        $this->assertIsString(Severity::LOW->value);
        $this->assertIsString(Severity::MID->value);
        $this->assertIsString(Severity::HIGH->value);
    }

    /**
     * Test Severity enum severity progression.
     */
    public function test_severity_order()
    {
        $cases = Severity::cases();

        $this->assertEquals('لا يوجد اضراب', $cases[0]->value);
        $this->assertEquals('بسيط', $cases[1]->value);
        $this->assertEquals('متوسط', $cases[2]->value);
        $this->assertEquals('شديد', $cases[3]->value);
    }

    /**
     * Test Severity enum all values are Arabic strings.
     */
    public function test_severity_enum_all_values_are_arabic()
    {
        foreach (Severity::cases() as $case) {
            $this->assertMatchesRegularExpression('/[\x{0600}-\x{06FF}]/u', $case->value, "Severity {$case->name} is not Arabic");
        }
    }

    /**
     * Test Score and Severity enums are different types.
     */
    public function test_score_and_severity_are_different_enums()
    {
        $this->assertNotEquals(Score::class, Severity::class);
        $this->assertNotEquals(Score::Never->value, Severity::OK->value);
    }

    /**
     * Test enum usage in arrays.
     */
    public function test_score_enum_in_array()
    {
        $scores = [Score::Never, Score::Sometimes, Score::Often, Score::Always];
        $scoreValues = collect($scores)->map(fn ($s) => $s->value)->toArray();

        $this->assertEquals([0, 1, 2, 3], $scoreValues);
    }

    public function test_severity_enum_in_array()
    {
        $severities = [Severity::OK, Severity::LOW, Severity::MID, Severity::HIGH];
        $severityValues = collect($severities)->map(fn ($s) => $s->value)->toArray();

        $this->assertEquals(['لا يوجد اضراب', 'بسيط', 'متوسط', 'شديد'], $severityValues);
    }

    /**
     * Test enum keys for array usage.
     */
    public function test_score_enum_as_array_keys()
    {
        $scoreMap = [
            Score::Never->value => 'Never',
            Score::Sometimes->value => 'Sometimes',
            Score::Often->value => 'Often',
            Score::Always->value => 'Always',
        ];

        $this->assertEquals('Never', $scoreMap[0]);
        $this->assertEquals('Sometimes', $scoreMap[1]);
        $this->assertEquals('Often', $scoreMap[2]);
        $this->assertEquals('Always', $scoreMap[3]);
    }
}
