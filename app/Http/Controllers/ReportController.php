<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reportService)
    {
        $user = $request->user();
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $reportData = $reportService->getReportData($user, $period, $startDate, $endDate);

        if ($request->has('export')) {
            $format = $request->input('export');
            if ($format === 'csv' || $format === 'excel') {
                return $reportService->generateCsvResponse($reportData);
            } elseif ($format === 'print') {
                return view('reports.print', compact('reportData'));
            }
        }

        return view('reports.index', compact('reportData'));
    }
}
