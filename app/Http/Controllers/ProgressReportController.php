<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Patient;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ProgressReportController extends Controller
{
    public function download(Request $request, Patient $patient, ReportService $reportService)
    {
        $eval1Id = $request->input('eval_1');
        $eval2Id = $request->input('eval_2');

        $eval1 = Evaluation::findOrFail($eval1Id);
        $eval2 = Evaluation::findOrFail($eval2Id);

        // Ensure eval1 is the older baseline
        if ($eval1->evaluation_date > $eval2->evaluation_date) {
            $temp = $eval1;
            $eval1 = $eval2;
            $eval2 = $temp;
        }

        return response($reportService->renderProgressReportHtml($eval1, $eval2))
            ->header('Content-Type', 'text/html; charset=utf-8');
    }
}
