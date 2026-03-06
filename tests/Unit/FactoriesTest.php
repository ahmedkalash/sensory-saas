<?php

namespace Tests\Unit;

use App\Enums\Score;
use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Patient;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Patient factory.
     */
    public function test_patient_factory_creates_valid_model()
    {
        $patient = Patient::factory()->create();

        $this->assertInstanceOf(Patient::class, $patient);
        $this->assertNotNull($patient->name);
        $this->assertIsString($patient->name);
    }

    public function test_patient_factory_with_custom_attributes()
    {
        $patient = Patient::factory()->create([
            'name' => 'Custom Name',
            'gender' => 'أنثى',
            'school' => 'Custom School',
            'grade' => 'Custom Grade',
        ]);

        $this->assertEquals('Custom Name', $patient->name);
        $this->assertEquals('أنثى', $patient->gender);
        $this->assertEquals('Custom School', $patient->school);
        $this->assertEquals('Custom Grade', $patient->grade);
    }

    /**
     * Test Measurement factory.
     */
    public function test_measurement_factory_creates_valid_model()
    {
        $measurement = Measurement::factory()->create();

        $this->assertInstanceOf(Measurement::class, $measurement);
        $this->assertNotNull($measurement->name);
    }

    public function test_measurement_factory_with_custom_name()
    {
        $measurement = Measurement::factory()->create(['name' => 'Custom Measurement']);

        $this->assertEquals('Custom Measurement', $measurement->name);
    }

    /**
     * Test Dimension factory.
     */
    public function test_dimension_factory_creates_valid_model()
    {
        $dimension = Dimension::factory()->create();

        $this->assertInstanceOf(Dimension::class, $dimension);
        $this->assertNotNull($dimension->name);
        $this->assertNotNull($dimension->measurement_id);
    }

    public function test_dimension_factory_with_measurement_relationship()
    {
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id]);

        $this->assertEquals($measurement->id, $dimension->measurement_id);
        $this->assertInstanceOf(Measurement::class, $dimension->measurement);
    }

    public function test_dimension_factory_with_custom_name()
    {
        $dimension = Dimension::factory()->create(['name' => 'Custom Dimension']);

        $this->assertEquals('Custom Dimension', $dimension->name);
    }

    /**
     * Test Question factory.
     */
    public function test_question_factory_creates_valid_model()
    {
        $question = Question::factory()->create();

        $this->assertInstanceOf(Question::class, $question);
        $this->assertNotNull($question->q_text);
        $this->assertNotNull($question->dimension_id);
        $this->assertIsArray($question->recommendations);
        $this->assertIsArray($question->goals);
        $this->assertIsArray($question->activities);
    }

    public function test_question_factory_with_custom_attributes()
    {
        $question = Question::factory()->create([
            'q_text' => 'Custom Question Text',
            'recommendations' => ['Custom Rec 1', 'Custom Rec 2'],
            'goals' => ['Custom Goal 1'],
            'activities' => ['Custom Activity 1', 'Custom Activity 2'],
        ]);

        $this->assertEquals('Custom Question Text', $question->q_text);
        $this->assertEquals(['Custom Rec 1', 'Custom Rec 2'], $question->recommendations);
        $this->assertEquals(['Custom Goal 1'], $question->goals);
        $this->assertEquals(['Custom Activity 1', 'Custom Activity 2'], $question->activities);
    }

    public function test_question_factory_with_empty_arrays()
    {
        $question = Question::factory()->create([
            'recommendations' => [],
            'goals' => [],
            'activities' => [],
        ]);

        $this->assertIsArray($question->recommendations);
        $this->assertEmpty($question->recommendations);
        $this->assertIsArray($question->goals);
        $this->assertEmpty($question->goals);
        $this->assertIsArray($question->activities);
        $this->assertEmpty($question->activities);
    }

    /**
     * Test Evaluation factory.
     */
    public function test_evaluation_factory_creates_valid_model()
    {
        $evaluation = Evaluation::factory()->create();

        $this->assertInstanceOf(Evaluation::class, $evaluation);
        $this->assertNotNull($evaluation->patient_id);
        $this->assertNotNull($evaluation->evaluation_date);
    }

    public function test_evaluation_factory_with_patient_relationship()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->assertEquals($patient->id, $evaluation->patient_id);
        $this->assertInstanceOf(Patient::class, $evaluation->patient);
    }

    public function test_evaluation_factory_with_custom_attributes()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'specialist_name' => 'Custom Specialist',
            'child_age' => '10 سنوات',
            'evaluation_date' => '2024-06-15',
        ]);

        $this->assertEquals('Custom Specialist', $evaluation->specialist_name);
        $this->assertEquals('10 سنوات', $evaluation->child_age);
        $this->assertEquals('2024-06-15', $evaluation->evaluation_date->format('Y-m-d'));
    }

    public function test_evaluation_factory_with_null_optional_fields()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'specialist_name' => null,
            'child_age' => null,
        ]);

        $this->assertNull($evaluation->specialist_name);
        $this->assertNull($evaluation->child_age);
    }

    /**
     * Test EvaluationAnswer factory.
     */
    public function test_evaluation_answer_factory_creates_valid_model()
    {
        $answer = EvaluationAnswer::factory()->create();

        $this->assertInstanceOf(EvaluationAnswer::class, $answer);
        $this->assertNotNull($answer->evaluation_id);
        $this->assertNotNull($answer->question_id);
        $this->assertInstanceOf(Score::class, $answer->score);
    }

    public function test_evaluation_answer_factory_score_values()
    {
        $scores = [];
        for ($i = 0; $i < 20; $i++) {
            $answer = EvaluationAnswer::factory()->create();
            $scores[] = $answer->score->value;
        }

        // All scores should be valid Score enum values
        foreach ($scores as $score) {
            $this->assertContains($score, [0, 1, 2, 3], "Invalid score value: $score");
        }
    }

    public function test_evaluation_answer_factory_with_custom_score()
    {
        $patient = Patient::factory()->create();
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id]);
        $question = Question::factory()->create(['dimension_id' => $dimension->id]);
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $answer = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question->id,
            'score' => Score::Often,
        ]);

        $this->assertEquals(Score::Often, $answer->score);
        $this->assertEquals(2, $answer->score->value);
    }

    public function test_evaluation_answer_factory_with_all_score_types()
    {
        $patient = Patient::factory()->create();
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id]);
        $question = Question::factory()->create(['dimension_id' => $dimension->id]);
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $answerNever = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question->id,
            'score' => Score::Never,
        ]);

        $question2 = Question::factory()->create(['dimension_id' => $dimension->id]);
        $answerSometimes = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question2->id,
            'score' => Score::Sometimes,
        ]);

        $question3 = Question::factory()->create(['dimension_id' => $dimension->id]);
        $answerOften = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question3->id,
            'score' => Score::Often,
        ]);

        $question4 = Question::factory()->create(['dimension_id' => $dimension->id]);
        $answerAlways = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question4->id,
            'score' => Score::Always,
        ]);

        $this->assertEquals(Score::Never, $answerNever->score);
        $this->assertEquals(Score::Sometimes, $answerSometimes->score);
        $this->assertEquals(Score::Often, $answerOften->score);
        $this->assertEquals(Score::Always, $answerAlways->score);
    }

    /**
     * Test User factory.
     */
    public function test_user_factory_creates_valid_model()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    public function test_user_factory_with_custom_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Custom User',
            'email' => 'custom@example.com',
        ]);

        $this->assertEquals('Custom User', $user->name);
        $this->assertEquals('custom@example.com', $user->email);
    }

    /**
     * Test factory count method.
     */
    public function test_patient_factory_count()
    {
        $patients = Patient::factory()->count(5)->create();

        $this->assertCount(5, $patients);
        $this->assertEquals(5, Patient::count());
    }

    public function test_measurement_factory_count()
    {
        $measurements = Measurement::factory()->count(10)->create();

        $this->assertCount(10, $measurements);
        $this->assertEquals(10, Measurement::count());
    }

    public function test_dimension_factory_count()
    {
        $dimensions = Dimension::factory()->count(7)->create();

        $this->assertCount(7, $dimensions);
        $this->assertEquals(7, Dimension::count());
    }

    public function test_question_factory_count()
    {
        $questions = Question::factory()->count(15)->create();

        $this->assertCount(15, $questions);
        $this->assertEquals(15, Question::count());
    }

    public function test_evaluation_answer_factory_count()
    {
        $answers = EvaluationAnswer::factory()->count(20)->create();

        $this->assertCount(20, $answers);
        $this->assertEquals(20, EvaluationAnswer::count());
    }

    /**
     * Test factory sequences and states.
     */
    public function test_complete_evaluation_setup_with_factories()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->for($patient)->create();

        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->for($measurement)->create();

        $questions = Question::factory()->count(10)->for($dimension)->create();

        foreach ($questions as $question) {
            EvaluationAnswer::factory()
                ->for($evaluation)
                ->for($question)
                ->create();
        }

        $this->assertEquals(1, Patient::count());
        $this->assertEquals(1, Evaluation::count());
        $this->assertEquals(1, Measurement::count());
        $this->assertEquals(1, Dimension::count());
        $this->assertEquals(10, Question::count());
        $this->assertEquals(10, EvaluationAnswer::count());
    }

    public function test_factory_with_state_for_high_score_answers()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->for($patient)->create();
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->for($measurement)->create();
        $question = Question::factory()->for($dimension)->create();

        // Create answers with high scores (weaknesses)
        $highScoreAnswer = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question->id,
            'score' => Score::Always,
        ]);

        $this->assertEquals(3, $highScoreAnswer->score->value);
    }

    public function test_factory_with_state_for_low_score_answers()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->for($patient)->create();
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->for($measurement)->create();
        $question = Question::factory()->for($dimension)->create();

        // Create answers with low scores (no weakness)
        $lowScoreAnswer = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $question->id,
            'score' => Score::Never,
        ]);

        $this->assertEquals(0, $lowScoreAnswer->score->value);
    }

    /**
     * Test factory relationships with mixed data.
     */
    public function test_multiple_patients_with_multiple_evaluations()
    {
        $patients = Patient::factory()->count(3)->create();

        foreach ($patients as $patient) {
            Evaluation::factory()->count(rand(1, 5))->for($patient)->create();
        }

        $totalEvaluations = Evaluation::count();
        $this->assertGreaterThanOrEqual(3, $totalEvaluations);
        $this->assertLessThanOrEqual(15, $totalEvaluations);

        foreach ($patients as $patient) {
            $this->assertGreaterThanOrEqual(1, $patient->evaluations()->count());
        }
    }
}
