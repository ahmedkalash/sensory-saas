<?php

namespace App\Services;

use App\Enums\Score;
use App\Enums\Severity;
use InvalidArgumentException;

class EvaluationService
{
    /**
     * Calculate severity level for a dimension based on total score.
     * Thresholds depend on the number of questions in the dimension.
     *
     * @param  int  $questionCount  The number of questions in the dimension (from snapshot).
     * @param  int  $totalScore  The summed score for all answers in the dimension.
     */
    public function getSeverity(int $questionCount, int $totalScore): Severity
    {
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

        return Severity::OK;
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
