<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use App\Models\Question;
use App\Services\EvaluationService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImmutableEvaluationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a new evaluation is created, the EvaluationAnswer rows must
     * capture the question text and metadata at that moment.
     */
    public function test_evaluation_answer_stores_snapshot_at_creation_time(): void
    {
        $question = Question::factory()->create([
            'q_text' => 'Original Question Text',
            'recommendations' => ['Original Rec'],
            'activities' => ['Original Act'],
            'goals' => ['Original Goal'],
        ]);

        $answer = EvaluationAnswer::factory()->create([
            'question_text' => $question->q_text,
            'dimension_name' => 'Test Dimension',
            'measurement_name' => 'Test Measurement',
            'recommendations' => $question->recommendations,
            'activities' => $question->activities,
            'goals' => $question->goals,
            'score' => Score::Always,
        ]);

        $this->assertEquals('Original Question Text', $answer->question_text);
        $this->assertEquals(['Original Rec'], $answer->recommendations);
        $this->assertEquals(['Original Act'], $answer->activities);
        $this->assertEquals(['Original Goal'], $answer->goals);
    }

    /**
     * After changing the Question's text, historical answers should still
     * display the original (snapped) text.
     */
    public function test_editing_question_does_not_change_historical_snapshot(): void
    {
        $question = Question::factory()->create([
            'q_text' => 'Original Question Text',
            'recommendations' => ['Original Rec'],
        ]);

        // Create an answer with the original snapshot
        $answer = EvaluationAnswer::factory()->create([
            'question_text' => $question->q_text,
            'recommendations' => $question->recommendations,
            'score' => Score::Often,
        ]);

        // Now mutate the Question model
        $question->update([
            'q_text' => 'CHANGED Question Text',
            'recommendations' => ['CHANGED Rec'],
        ]);

        // Reload answer from DB
        $answer->refresh();

        // Snapshot must be unchanged
        $this->assertEquals('Original Question Text', $answer->question_text);
        $this->assertEquals(['Original Rec'], $answer->recommendations);
    }

    /**
     * After deleting a Question, historical answers and their reports should
     * still be intact and usable.
     */
    public function test_deleting_question_does_not_break_historical_answers(): void
    {
        $question = Question::factory()->create([
            'q_text' => 'About To Be Deleted Question',
            'recommendations' => ['Some Rec'],
        ]);

        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        $answer = EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_text' => $question->q_text,
            'dimension_name' => 'Test Dimension',
            'measurement_name' => 'Test Scale',
            'recommendations' => $question->recommendations,
            'score' => Score::Always,
        ]);

        // Delete the Question from the DB entirely
        $question->delete();

        // Reload answer from DB
        $answer->refresh();

        // Snapshot still works
        $this->assertEquals('About To Be Deleted Question', $answer->question_text);
        $this->assertEquals(['Some Rec'], $answer->recommendations);

        // Report can still be generated without errors
        $service = new ReportService(new EvaluationService);
        $html = $service->renderGeneralReportHtml($evaluation);

        $this->assertStringContainsString('About To Be Deleted Question', $html);
        $this->assertStringContainsString('Test Scale', $html);
    }

    /**
     * Multiple evaluations for the same patient can hold different snapshots
     * of the same original question (as it was at the time of each evaluation).
     */
    public function test_different_evaluations_can_have_different_snapshots_of_same_question(): void
    {
        $patient = Patient::factory()->create();
        $eval1 = Evaluation::factory()->create(['patient_id' => $patient->id]);
        $eval2 = Evaluation::factory()->create(['patient_id' => $patient->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $eval1->id,
            'question_text' => 'Version 1 of the Question',
            'dimension_name' => 'Dim A',
            'measurement_name' => 'Scale X',
            'score' => Score::Never,
        ]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $eval2->id,
            'question_text' => 'Version 2 of the Question (after edit)',
            'dimension_name' => 'Dim A',
            'measurement_name' => 'Scale X',
            'score' => Score::Always,
        ]);

        $answers1 = $eval1->answers;
        $answers2 = $eval2->answers;

        $this->assertEquals('Version 1 of the Question', $answers1->first()->question_text);
        $this->assertEquals('Version 2 of the Question (after edit)', $answers2->first()->question_text);
    }

    /**
     * Verify that EvaluationAnswer snapshot columns store arrays correctly via casts.
     */
    public function test_snapshot_array_columns_cast_correctly(): void
    {
        $answer = EvaluationAnswer::factory()->create([
            'recommendations' => ['Rec A', 'Rec B'],
            'activities' => ['Act 1'],
            'goals' => [],
        ]);

        $fresh = EvaluationAnswer::find($answer->id);

        $this->assertIsArray($fresh->recommendations);
        $this->assertIsArray($fresh->activities);
        $this->assertIsArray($fresh->goals);
        $this->assertEquals(['Rec A', 'Rec B'], $fresh->recommendations);
        $this->assertEquals(['Act 1'], $fresh->activities);
        $this->assertEmpty($fresh->goals);
    }
}
