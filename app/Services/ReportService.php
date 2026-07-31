<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function getReportData(User $user, string $period = 'monthly', ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $user->expenses()->with(['category', 'tags', 'foodSubcategory']);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
            $title = "Expense Report ($startDate to $endDate)";
        } else {
            switch ($period) {
                case 'daily':
                    $query->whereDate('date', Carbon::today());
                    $title = "Daily Expense Report (" . Carbon::today()->format('M d, Y') . ")";
                    break;
                case 'weekly':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    $title = "Weekly Expense Report (" . Carbon::now()->startOfWeek()->format('M d') . " - " . Carbon::now()->endOfWeek()->format('M d, Y') . ")";
                    break;
                case 'yearly':
                    $query->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    $title = "Yearly Expense Report (" . Carbon::now()->format('Y') . ")";
                    break;
                case 'monthly':
                default:
                    $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    $title = "Monthly Expense Report (" . Carbon::now()->format('F Y') . ")";
                    break;
            }
        }

        $expenses = $query->orderBy('date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        $categorySummary = $expenses->groupBy('category.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        return [
            'title' => $title,
            'period' => $period,
            'expenses' => $expenses,
            'total_amount' => (float) $totalAmount,
            'total_count' => $expenses->count(),
            'category_summary' => $categorySummary,
            'user' => $user,
        ];
    }

    public function generateCsvResponse(array $reportData)
    {
        $filename = "Expense_Report_" . date('Y_m_d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Category', 'Amount', 'Date', 'Time', 'Payment Method', 'Location', 'Notes', 'Healthy']);

            foreach ($reportData['expenses'] as $exp) {
                fputcsv($file, [
                    $exp->id,
                    $exp->title,
                    $exp->category ? $exp->category->name : 'Uncategorized',
                    $exp->amount,
                    $exp->date,
                    $exp->time,
                    $exp->payment_method,
                    $exp->location,
                    $exp->notes,
                    $exp->is_healthy === null ? 'N/A' : ($exp->is_healthy ? 'Yes' : 'No')
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['TOTAL', '', '', $reportData['total_amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
