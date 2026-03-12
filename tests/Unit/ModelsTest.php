<?php

namespace Tests\Unit;

use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Patient;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Patient model relationships and casts.
     */
    public function test_patient_model_has_evaluations_relationship()
    {
        $patient = Patient::factory()->create();
        Evaluation::factory()->count(3)->create(['patient_id' => $patient->id]);

        $this->assertEquals(3, $patient->evaluations()->count());
        $this->assertInstanceOf(Evaluation::class, $patient->evaluations->first());
    }

    public function test_patient_model_fillable_attributes()
    {
        $patient = Patient::factory()->create([
            'name' => 'Test Patient',
            'gender' => 'ذكر',
            'school' => 'Test School',
            'grade' => 'Test Grade',
        ]);

        $this->assertEquals('Test Patient', $patient->name);
        $this->assertEquals('ذكر', $patient->gender);
        $this->assertEquals('Test School', $patient->school);
        $this->assertEquals('Test Grade', $patient->grade);
    }

    /**
     * Test Evaluation model relationships and casts.
     */
    public function test_evaluation_model_has_patient_relationship()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $this->assertInstanceOf(Patient::class, $evaluation->patient);
        $this->assertEquals($patient->id, $evaluation->patient->id);
    }

    public function test_evaluation_model_has_answers_relationship()
    {
        $evaluation = Evaluation::factory()->create();

        EvaluationAnswer::factory()->count(5)->create([
            'evaluation_id' => $evaluation->id,
        ]);

        $this->assertEquals(5, $evaluation->answers()->count());
        $this->assertInstanceOf(EvaluationAnswer::class, $evaluation->answers->first());
    }

    public function test_evaluation_model_date_casting()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'evaluation_date' => '2024-01-15',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $evaluation->evaluation_date);
        $this->assertEquals('2024-01-15', $evaluation->evaluation_date->format('Y-m-d'));
    }

    public function test_evaluation_model_null_specialist_name()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'specialist_name' => null,
        ]);

        $this->assertNull($evaluation->specialist_name);
    }

    public function test_evaluation_model_null_child_age()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'patient_id' => $patient->id,
            'child_age' => null,
        ]);

        $this->assertNull($evaluation->child_age);
    }

    /**
     * Test Measurement model relationships.
     */
    public function test_measurement_model_has_dimensions_relationship()
    {
        $measurement = Measurement::factory()->create();
        Dimension::factory()->count(4)->create(['measurement_id' => $measurement->id]);

        $this->assertEquals(4, $measurement->dimensions()->count());
        $this->assertInstanceOf(Dimension::class, $measurement->dimensions->first());
    }

    /**
     * Test Dimension model relationships.
     */
    public function test_dimension_model_has_measurement_relationship()
    {
        $measurement = Measurement::factory()->create();
        $dimension = Dimension::factory()->create(['measurement_id' => $measurement->id]);

        $this->assertInstanceOf(Measurement::class, $dimension->measurement);
        $this->assertEquals($measurement->id, $dimension->measurement->id);
    }

    public function test_dimension_model_has_questions_relationship()
    {
        $dimension = Dimension::factory()->create();
        Question::factory()->count(7)->create(['dimension_id' => $dimension->id]);

        $this->assertEquals(7, $dimension->questions()->count());
        $this->assertInstanceOf(Question::class, $dimension->questions->first());
    }

    /**
     * Test Question model relationships and casts.
     */
    public function test_question_model_has_dimension_relationship()
    {
        $dimension = Dimension::factory()->create();
        $question = Question::factory()->create(['dimension_id' => $dimension->id]);

        $this->assertInstanceOf(Dimension::class, $question->dimension);
        $this->assertEquals($dimension->id, $question->dimension->id);
    }

    public function test_question_model_recommendations_array_casting()
    {
        $dimension = Dimension::factory()->create();
        $question = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'recommendations' => ['Rec 1', 'Rec 2', 'Rec 3'],
        ]);

        $this->assertIsArray($question->recommendations);
        $this->assertEquals(['Rec 1', 'Rec 2', 'Rec 3'], $question->recommendations);
    }

    public function test_question_model_goals_array_casting()
    {
        $dimension = Dimension::factory()->create();
        $question = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'goals' => ['Goal 1', 'Goal 2'],
        ]);

        $this->assertIsArray($question->goals);
        $this->assertEquals(['Goal 1', 'Goal 2'], $question->goals);
    }

    public function test_question_model_activities_array_casting()
    {
        $dimension = Dimension::factory()->create();
        $question = Question::factory()->create([
            'dimension_id' => $dimension->id,
            'activities' => ['Activity 1', 'Activity 2', 'Activity 3', 'Activity 4'],
        ]);

        $this->assertIsArray($question->activities);
        $this->assertEquals(['Activity 1', 'Activity 2', 'Activity 3', 'Activity 4'], $question->activities);
    }

    public function test_question_model_empty_array_casting()
    {
        $dimension = Dimension::factory()->create();
        $question = Question::factory()->create([
            'dimension_id' => $dimension->id,
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
     * Test EvaluationAnswer model relationships and casts.
     */
    public function test_evaluation_answer_model_has_evaluation_relationship()
    {
        $evaluation = Evaluation::factory()->create();

        $answer = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_text' => 'Sample Question',
            'dimension_name' => 'Sample Dimension',
            'measurement_name' => 'Sample Measurement',
        ]);

        $this->assertInstanceOf(Evaluation::class, $answer->evaluation);
        $this->assertEquals($evaluation->id, $answer->evaluation->id);
    }

    public function test_evaluation_answer_model_snapshot_columns_are_stored()
    {
        $answer = EvaluationAnswer::factory()->create([
            'question_text' => 'Stored Question Text',
            'dimension_name' => 'Stored Dimension',
            'measurement_name' => 'Stored Measurement',
            'recommendations' => ['Rec A'],
            'activities' => ['Act A'],
            'goals' => ['Goal A'],
        ]);

        $this->assertEquals('Stored Question Text', $answer->question_text);
        $this->assertEquals('Stored Dimension', $answer->dimension_name);
        $this->assertEquals('Stored Measurement', $answer->measurement_name);
        $this->assertEquals(['Rec A'], $answer->recommendations);
        $this->assertEquals(['Act A'], $answer->activities);
        $this->assertEquals(['Goal A'], $answer->goals);
    }

    /**
     * Test User model (if applicable).
     */
    public function test_user_model_exists()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * Test model factory states and relationships together.
     */
    public function test_complete_evaluation_flow()
    {
        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        EvaluationAnswer::factory()->count(10)->create([
            'evaluation_id' => $evaluation->id,
        ]);

        $this->assertEquals(1, $patient->evaluations()->count());
        $this->assertEquals(10, $evaluation->answers()->count());
    }

    public function test_multiple_evaluations_for_same_patient()
    {
        $patient = Patient::factory()->create();
        Evaluation::factory()->count(5)->create(['patient_id' => $patient->id]);

        $this->assertEquals(5, $patient->evaluations()->count());

        $evaluations = $patient->evaluations()->orderBy('evaluation_date', 'desc')->get();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $evaluations);
    }

    public function test_cascading_deletes_for_patient()
    {
        $patient = Patient::factory()->create();
        $evaluations = Evaluation::factory()->count(3)->create(['patient_id' => $patient->id]);

        $patient->delete();

        $this->assertEquals(0, Evaluation::whereIn('id', $evaluations->pluck('id'))->count());
    }

    public function test_patient_model_new_fields()
    {
        $patient = Patient::factory()->create([
            'medical_plan' => ['surgery' => 'Tonsillectomy', 'date' => '2024-05-01'],
            'status' => 'completed',
        ]);

        $this->assertIsArray($patient->medical_plan);
        $this->assertEquals('Tonsillectomy', $patient->medical_plan['surgery']);
        $this->assertEquals(\App\Enums\PatientStatus::COMPLETED, $patient->status);
    }

    public function test_evaluation_answer_model_new_fields()
    {
        $answer = EvaluationAnswer::factory()->create([
            'notes' => 'The child struggled with this task.',
        ]);

        $this->assertEquals('The child struggled with this task.', $answer->notes);
    }
}
