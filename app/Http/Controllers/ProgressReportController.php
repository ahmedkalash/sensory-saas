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

        $mpdf = $reportService->generateProgressReport($eval1, $eval2);

        $filename = 'مقارنة_تقدم_'.($patient->name ?? 'طفل').'_'.now()->format('Y-m-d').'.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
