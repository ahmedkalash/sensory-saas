<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use App\Models\Question;
use App\Services\EvaluationService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A question can be created with all its associated fields.
     */
    public function test_can_create_question_with_all_fields(): void
    {
        $dimension = Dimension::factory()->create();

        $question = Question::create([
            'dimension_id' => $dimension->id,
            'q_text' => 'هل يستجيب الطفل للأصوات العالية؟',
            'recommendations' => ['التدريب السمعي', 'تقليل الضوضاء'],
            'activities' => ['استخدام سماعات خافتة'],
            'goals' => ['تحسين الاستجابة السمعية'],
        ]);

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'dimension_id' => $dimension->id,
            'q_text' => 'هل يستجيب الطفل للأصوات العالية؟',
        ]);

        $this->assertIsArray($question->recommendations);
        $this->assertIsArray($question->activities);
        $this->assertIsArray($question->goals);
        $this->assertCount(2, $question->recommendations);
    }

    /**
     * A question cannot be created without a dimension_id.
     */
    public function test_cannot_create_question_without_dimension(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Question::create([
            'dimension_id' => null,
            'q_text' => 'سؤال بدون بُعد',
        ]);
    }

    /**
     * Editing a question does not retroactively change existing evaluation answer snapshots.
     */
    public function test_editing_question_does_not_change_existing_snapshots(): void
    {
        $question = Question::factory()->create([
            'q_text' => 'النص الأصلي',
            'recommendations' => ['توصية أصلية'],
        ]);

        $answer = EvaluationAnswer::factory()->create([
            'question_text' => $question->q_text,
            'recommendations' => $question->recommendations,
            'score' => Score::Often,
        ]);

        $question->update([
            'q_text' => 'النص المعدَّل',
            'recommendations' => ['توصية معدلة'],
        ]);

        $answer->refresh();

        $this->assertEquals('النص الأصلي', $answer->question_text);
        $this->assertEquals(['توصية أصلية'], $answer->recommendations);
    }

    /**
     * Deleting a question does not break existing evaluation reports.
     */
    public function test_deleting_question_does_not_break_existing_reports(): void
    {
        $question = Question::factory()->create([
            'q_text' => 'سؤال سيتم حذفه',
            'recommendations' => ['توصية مهمة'],
        ]);

        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'question_text' => $question->q_text,
            'dimension_name' => 'بُعد تجريبي',
            'measurement_name' => 'مقياس تجريبي',
            'recommendations' => $question->recommendations,
            'score' => Score::Always,
        ]);

        $question->delete();

        $service = new ReportService(new EvaluationService);
        $html = $service->renderGeneralReportHtml($evaluation);

        $this->assertStringContainsString('سؤال سيتم حذفه', $html);
    }

    /**
     * JSON array columns for recommendations, activities and goals cast correctly.
     */
    public function test_repeater_json_fields_are_stored_and_retrieved_as_arrays(): void
    {
        $question = Question::factory()->create([
            'recommendations' => ['r1', 'r2'],
            'activities' => ['a1'],
            'goals' => [],
        ]);

        $fresh = Question::find($question->id);

        $this->assertIsArray($fresh->recommendations);
        $this->assertIsArray($fresh->activities);
        $this->assertIsArray($fresh->goals);
        $this->assertEquals(['r1', 'r2'], $fresh->recommendations);
        $this->assertEquals(['a1'], $fresh->activities);
        $this->assertEmpty($fresh->goals);
    }

    /**
     * A newly created question appears in the correct dimension when fetched.
     */
    public function test_new_question_is_linked_to_correct_dimension(): void
    {
        $dimension = Dimension::factory()->create();

        $question = Question::factory()->for($dimension)->create();

        $this->assertEquals($dimension->id, $question->dimension->id);
    }
}
