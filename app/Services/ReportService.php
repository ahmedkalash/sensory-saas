<?php

namespace App\Services;

use App\Enums\Severity;
use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\Measurement;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class ReportService
{
    public function __construct(
        private EvaluationService $evaluationService,
    ) {}

    /**
     * Generate a PDF report for the given evaluation.
     *
     * @throws MpdfException|\Throwable
     */
    public function generate(Evaluation $evaluation, ?int $reportMeasurementId = null): Mpdf
    {
        $html = $this->renderGeneralReportHtml($evaluation, $reportMeasurementId);

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    /**
     * @throws MpdfException
     */
    public function generateParentReport(Evaluation $evaluation, ?int $reportMeasurementId = null): Mpdf
    {
        $html = $this->renderParentReportHtml($evaluation, $reportMeasurementId);

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    /**
     * @throws MpdfException
     */
    protected function newMpdf(): Mpdf
    {
        $mpdf =  new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'tajawal',
            'default_font_size' => 11,
            'tempDir' => storage_path('app/mpdf-temp'),
            'fontDir' => [storage_path('fonts/tajawal')],
            'fontdata' => [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'autoArabic' => true,
            'autoLangToFont' => false,
        ]);

        $mpdf->SetDirectionality('rtl');

        return $mpdf;
    }

    /**
     * Render the report as an HTML string directly.
     */
    public function renderGeneralReportHtml(Evaluation $evaluation, ?int $reportMeasurementId = null): string
    {
        $evaluation->load([
            'patient',
            'answers.question.dimension.measurement',
        ]);

        $reportData = $this->buildReportData($evaluation, $reportMeasurementId);

        return view('reports.evaluation-report', $reportData)->render();
    }

    /**
     * Build the structured report data for the Blade view.
     *
     * @return array<string, mixed>
     */
    private function buildReportData(Evaluation $evaluation, ?int $reportMeasurementId = null): array
    {
        // do not delete any of the following comments
        // measurement
        //  - dim
        //      - total questions score (sum answers.score)
        //      - Severity
        //      - weakness(q_text) where answers.score >=2 (==2 or ==3)
        //      - total recommendations for weak score questions (weakness)
        //      - total goals for weak score questions (weakness)
        //      - total activities for weak score questions (weakness)

        // Get unique measurements from the actual submitted answers
        $evaluatedMeasurementIds = $evaluation->answers()
            ->with('question.dimension')
            ->get()
            ->pluck('question.dimension.measurement_id')
            ->unique()
            ->toArray();

        // Base query for measurements and their relations
        $query = Measurement::with(['dimensions.questions']);

        // Limit query: if a specific scale is requested, only query that one.
        // If nothing is explicitly requested, only query the scales the child actually has answers for.
        if ($reportMeasurementId !== null) {
            $query->where('id', $reportMeasurementId);
        } else {
            $query->whereIn('id', $evaluatedMeasurementIds);
        }

        $measurements = $query->get();

        $answersByQuestion = $evaluation
            ->answers
            ->keyBy('question_id');

        $measurementResults = [];

        /**@var Measurement $measurement*/
        foreach ($measurements as $measurement) {
            $dimensionResults = [];

            /**@var Dimension $dimension*/
            foreach ($measurement->dimensions as $dimension) {
                $dimensionAnswers = $dimension->questions
                    ->map(fn ($q) => $answersByQuestion->get($q->id))
                    ->filter();

                // Total questions score (sum answers.score)
                $totalScore = $dimensionAnswers->sum(fn ($a) => $a->score->value);

                // Dim severity
                $severity = $this->evaluationService->getSeverity($dimension, $totalScore);

                $weaknesses = $dimensionAnswers
                    ->filter(fn ($a) => $a->score->value >= 2) // weakness(q_text) where answers.score >=2 (==2 or ==3)
                    ->map(fn ($a) => [
                        'question_text' => $a->question->q_text,
                        'score_label' => $a->score->label(),
                        'recommendations' => $a->question->recommendations ?? [], // total recommendations for weak score questions (weakness)
                        'goals' => $a->question->goals ?? [], // total goals for weak score questions (weakness)
                        'activities' => $a->question->activities ?? [], // total activities for weak score questions (weakness)
                    ])
                    ->values()
                    ->toArray();

                $dimensionResults[] = [
                    'name' => $dimension->name,
                    'total_score' => $totalScore,
                    'severity' => $severity,
                    'weaknesses' => $weaknesses,
                ];
            }

            $measurementResults[] = [
                'name' => $measurement->name,
                'dimensions' => $dimensionResults,
            ];
        }

        return [
            'evaluation' => $evaluation,
            'patient' => $evaluation->patient,
            'measurements' => $measurementResults,
        ];
    }

    public function renderParentReportHtml(Evaluation $evaluation, ?int $reportMeasurementId): string
    {
        $evaluation->load([
            'patient',
            'answers.question.dimension.measurement',
        ]);

        $reportData = $this->buildReportData($evaluation, $reportMeasurementId);

        return view('reports.parent-evaluation-report', $reportData)->render();
    }
}
