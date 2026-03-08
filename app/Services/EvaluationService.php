<?php

namespace App\Services;

use App\Enums\Score;
use App\Enums\Severity;
use App\Models\Dimension;
use InvalidArgumentException;

class EvaluationService
{
    /**
     * Calculate severity level for a dimension based on total score.
     * Thresholds depend on the number of questions in the dimension.
     */
    public function getSeverity(Dimension $dimension, int $totalScore): Severity
    {
        $questionCount = $dimension->questions()->count();

        $this->validateTotalScore($questionCount, $totalScore);

        if ($totalScore == Score::Never->value) {
            return Severity::OK;
        }

        if ($totalScore <= $questionCount) {
            return Severity::LOW;
        }

        if (
            ($totalScore >= ($questionCount + 1)) &&
            ($totalScore <= ($questionCount * 2))
        ) {
            return Severity::MID;
        }

        if (
            ($totalScore >= (2 * $questionCount + 1)) &&
            ($totalScore <= $questionCount * 3)
        ) {
            return Severity::HIGH;
        }
    }

    private function validateTotalScore(int $questionCount, int $totalScore): void
    {
        if ($totalScore < Score::Never->value) {
            throw new InvalidArgumentException('Total score cannot be less than 0.');
        }

        if ($totalScore > $questionCount * 3) {
            throw new InvalidArgumentException('Total score cannot exceed the number of questions * 3 in the dimension.');
        }
    }
}
