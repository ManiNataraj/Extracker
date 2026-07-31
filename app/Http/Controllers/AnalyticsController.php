<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analyticsService)
    {
        $user = $request->user();

        $monthlyTrend = $analyticsService->getMonthlyTrend($user, 12);
        $categoryBreakdown = $analyticsService->getCategoryBreakdown($user);
        $weekdayVsWeekend = $analyticsService->getWeekdayVsWeekend($user);
        $heatmap = $analyticsService->getDailyHeatmap($user);

        // Overall stats
        $allExpenses = $user->expenses();
        $totalSpentAllTime = (float) $allExpenses->sum('amount');
        $totalTransactions = $allExpenses->count();
        $maxExpense = $allExpenses->orderBy('amount', 'desc')->first();
        $minExpense = $allExpenses->orderBy('amount', 'asc')->first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $thisMonthSpent = (float) $user->expenses()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');

        $highestExpenseDay = $user->expenses()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('date', DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->first();

        $lowestExpenseDay = $user->expenses()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('date', DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('total', 'asc')
            ->first();

        return view('analytics.index', compact(
            'monthlyTrend',
            'categoryBreakdown',
            'weekdayVsWeekend',
            'heatmap',
            'totalSpentAllTime',
            'totalTransactions',
            'maxExpense',
            'minExpense',
            'thisMonthSpent',
            'highestExpenseDay',
            'lowestExpenseDay'
        ));
    }
}
