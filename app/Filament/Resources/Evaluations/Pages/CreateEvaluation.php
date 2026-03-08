<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationAnswer;
use Filament\Resources\Pages\CreateRecord;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static ?string $title = 'تقييم جديد';

    protected function afterCreate(): void
    {
        $evaluation = $this->record;
        $draftAnswers = $this->data['draft_answers'] ?? [];
        $draftNotes = $this->data['draft_notes'] ?? [];

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
