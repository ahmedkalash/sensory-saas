<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationAnswer;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static ?string $title = 'تعديل التقييم';

    protected function afterSave(): void
    {
        $evaluation = $this->record;
        $draftAnswers = $evaluation->draft_answers ?? [];

        // Clear old answers and re-insert
        $evaluation->answers()->delete();

        $answerRecords = [];
        foreach ($draftAnswers as $questionId => $score) {
            if ($score !== null) {
                $answerRecords[] = [
                    'evaluation_id' => $evaluation->id,
                    'question_id' => $questionId,
                    'score' => (int) $score,
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
