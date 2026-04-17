<?php


use App\Models\Evaluation;
use App\Services\ReportService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::post('/clear-cache-recovery', function () {
    Cache::forget('migrated_version');
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return redirect('/');
})->name('recovery.clear-cache');


Route::middleware(['auth'])->group(function () {
    Route::get('/evaluations/{evaluation}/report', function (Evaluation $evaluation) {
        $reportService = app(ReportService::class);
        $measurementId = request('measurement_id');
        $mpdf = $reportService->generate($evaluation, $measurementId);

        $filename = 'تقرير_'.($evaluation->patient->name ?? 'طفل').'_'.$evaluation->evaluation_date?->format('Y-m-d').'.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    })->name('evaluations.report');

    Route::get('/evaluations/{evaluation}/parent_report', function (Evaluation $evaluation) {
        $reportService = app(ReportService::class);
        $measurementId = request('measurement_id');
        $mpdf = $reportService->generateParentReport($evaluation, $measurementId);
        // for testing only do not delete
        //    return response($reportService->renderParentReportHtml($evaluation, $measurementId))
        //        ->header('Content-Type', 'text/html; charset=utf-8');
        $filename = 'تقرير_'.($evaluation->patient->name ?? 'طفل').'_'.$evaluation->evaluation_date?->format('Y-m-d').'.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    })->name('evaluations.parent_report');

    Route::get('/evaluations/{evaluation}/report/html', function (Evaluation $evaluation) {
        $reportService = app(ReportService::class);
        $measurementId = request('measurement_id');

        return response($reportService->renderGeneralReportHtml($evaluation, $measurementId))
            ->header('Content-Type', 'text/html; charset=utf-8');
    })->name('evaluations.report.html');

    Route::get('/patients/{patient}/progress-report', [\App\Http\Controllers\ProgressReportController::class, 'download'])
        ->name('reports.progress');
});

