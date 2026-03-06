<?php

use App\Models\Evaluation;
use App\Services\ReportService;
use Illuminate\Support\Facades\Route;


Route::get('/evaluations/{evaluation}/report', function (Evaluation $evaluation) {
    $reportService = app(ReportService::class);
    $mpdf = $reportService->generate($evaluation);

    $filename = 'تقرير_'.($evaluation->patient->name ?? 'مريض').'_'.$evaluation->evaluation_date?->format('Y-m-d').'.pdf';

    return response($mpdf->Output($filename, 'S'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
    ]);
})->name('evaluations.report');

Route::get('/evaluations/{evaluation}/report/html', function (Evaluation $evaluation) {
    $reportService = app(ReportService::class);

    return response($reportService->renderHtml($evaluation))
        ->header('Content-Type', 'text/html; charset=utf-8');
})->name('evaluations.report.html');
