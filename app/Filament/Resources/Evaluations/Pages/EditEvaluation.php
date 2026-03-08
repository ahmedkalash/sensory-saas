<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationAnswer;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static ?string $title = 'تعديل التقييم';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $answers = $this->record->answers;

        $draftAnswers = [];
        $draftNotes = [];

        foreach ($answers as $answer) {
            $draftAnswers[$answer->question_id] = $answer->score->value;
            $draftNotes[$answer->question_id] = $answer->notes;
        }

        $data['draft_answers'] = $draftAnswers;
        $data['draft_notes'] = $draftNotes;

        return $data;
    }

    protected function afterSave(): void
    {
        $evaluation = $this->record;
        $draftAnswers = $this->data['draft_answers'] ?? [];
        $draftNotes = $this->data['draft_notes'] ?? [];

        // Clear old answers and re-insert
        $evaluation->answers()->delete();

        $answerRecords = [];
        foreach ($draftAnswers as $questionId => $score) {
            if ($score !== null) {
                $answerRecords[] = [
                    'evaluation_id' => $evaluation->id,
                    'question_id' => $questionId,
                    'score' => (int) $score,
                    'notes' => $draftNotes[$questionId] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($answerRecords)) {
            EvaluationAnswer::insert($answerRecords);
        }
    }
}
