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
                    ->map(fn($q) => $answersByQuestion->get($q->id))
                    ->filter();

                // Total questions score (sum answers.score)
                $totalScore = $dimensionAnswers->sum(fn($a) => $a->score->value);

                // Dim severity
                $severity = $this->evaluationService->getSeverity($dimension, $totalScore);

                $weaknesses = $dimensionAnswers
                    ->filter(fn($a) => $a->score->value >= 2) // weakness(q_text) where answers.score >=2 (==2 or ==3)
                    ->map(fn($a) => [
                        'question_text' => $a->question->q_text,
                        'score_label' => $a->score->label(),
                        'recommendations' => $a->question->recommendations ?? [], // total recommendations for weak score questions (weakness)
                        'goals' => $a->question->goals ?? [], // total goals for weak score questions (weakness)
                        'activities' => $a->question->activities ?? [], // total activities for weak score questions (weakness)
                    ])
                    ->values()
                    ->toArray();

                $observations = $dimensionAnswers
                    ->filter(fn($a) => !empty($a->notes))
                    ->map(fn($a) => [
                        'question_text' => $a->question->q_text,
                        'notes' => $a->notes,
                    ])
                    ->values()
                    ->toArray();

                $dimensionResults[] = [
                    'name' => $dimension->name,
                    'total_score' => $totalScore,
                    'severity' => $severity,
                    'weaknesses' => $weaknesses,
                    'observations' => $observations,
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

    /**
     * @throws MpdfException
     */
    public function generateProgressReport(Evaluation $eval1, Evaluation $eval2): Mpdf
    {
        $html = $this->renderProgressReportHtml($eval1, $eval2);

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    public function renderProgressReportHtml(Evaluation $eval1, Evaluation $eval2): string
    {
        $eval1->load(['patient', 'answers.question.dimension.measurement']);
        $eval2->load(['patient', 'answers.question.dimension.measurement']);

        $reportData = $this->buildProgressReportData($eval1, $eval2);

        return view('reports.progress-report', $reportData)->render();
    }

    private function buildProgressReportData(Evaluation $eval1, Evaluation $eval2): array
    {
        $data1 = $this->buildReportData($eval1);
        $data2 = $this->buildReportData($eval2);

        $measurements1 = collect($data1['measurements'])->keyBy('name');
        $measurements2 = collect($data2['measurements'])->keyBy('name');

        $comparisonResults = [];

        foreach ($measurements1 as $name => $m1) {
            if (! $measurements2->has($name)) {
                continue; // Only compare scales that exist in both
            }

            $m2 = $measurements2->get($name);

            $dims1 = collect($m1['dimensions'])->keyBy('name');
            $dims2 = collect($m2['dimensions'])->keyBy('name');

            $dimensionComparisons = [];
            foreach ($dims1 as $dimName => $d1) {
                if (! $dims2->has($dimName)) {
                    continue; // Compare dimensions present in both
                }

                $d2 = $dims2->get($dimName);

                $score1 = $d1['total_score'];
                $score2 = $d2['total_score'];

                // Lower score means fewer weaknesses (better state).
                $status = 'ثابت';
                $statusColor = '#6b7280'; // gray
                $statusLabel = 'لا يوجد تغيير';

                if ($score2 < $score1) {
                    $status = 'تحسن';
                    $statusColor = '#10b981'; // green
                    $statusLabel = 'تحسن (-' . ($score1 - $score2) . ')';
                } elseif ($score2 > $score1) {
                    $status = 'تراجع';
                    $statusColor = '#ef4444'; // red
                    $statusLabel = 'تراجع (+' . ($score2 - $score1) . ')';
                }

                $dimensionComparisons[] = [
                    'name' => $dimName,
                    'score_1' => $score1,
                    'severity_1' => $d1['severity'],
                    'score_2' => $score2,
                    'severity_2' => $d2['severity'],
                    'status' => $status,
                    'status_color' => $statusColor,
                    'status_label' => $statusLabel,
                ];
            }

            $comparisonResults[] = [
                'name' => $name,
                'dimensions' => $dimensionComparisons,
            ];
        }

        return [
            'patient' => $eval1->patient,
            'eval1' => $eval1,
            'eval2' => $eval2,
            'measurements' => $comparisonResults,
        ];
    }
}
