<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardMetrics(User $user): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $spendToday = (float) $user->expenses()->whereDate('date', $today)->sum('amount');
        $spendThisWeek = (float) $user->expenses()->whereBetween('date', [$startOfWeek, $endOfWeek])->sum('amount');
        $spendThisMonth = (float) $user->expenses()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
        $spendThisYear = (float) $user->expenses()->whereBetween('date', [$startOfYear, $endOfYear])->sum('amount');

        // Days elapsed in current month
        $daysInMonth = $today->day;
        $avgDailyExpense = $daysInMonth > 0 ? round($spendThisMonth / $daysInMonth, 2) : 0;

        // Highest & Lowest category spending this month (or fallback all-time)
        $categorySums = $user->expenses()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        if ($categorySums->isEmpty()) {
            $categorySums = $user->expenses()
                ->whereNotNull('category_id')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category')
                ->get();
        }

        $highestCategory = $categorySums->sortByDesc('total')->first();
        $lowestCategory = $categorySums->sortBy('total')->first();

        // Recent transactions
        $recentTransactions = $user->expenses()
            ->with(['category', 'tags'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Previous month comparison
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $spendLastMonth = (float) $user->expenses()->whereBetween('date', [$prevMonthStart, $prevMonthEnd])->sum('amount');
        
        $monthlyDiffPercent = $spendLastMonth > 0 
            ? round((($spendThisMonth - $spendLastMonth) / $spendLastMonth) * 100, 1) 
            : 0;

        // Budget remaining & savings calculation
        $monthlyBudget = $user->monthly_budget_limit ?? 50000;
        $budgetRemaining = max(0, $monthlyBudget - $spendThisMonth);
        $savingsAmount = max(0, $monthlyBudget - $spendThisMonth);

        return [
            'spend_today' => $spendToday,
            'spend_this_week' => $spendThisWeek,
            'spend_this_month' => $spendThisMonth,
            'spend_this_year' => $spendThisYear,
            'avg_daily_expense' => $avgDailyExpense,
            'highest_category' => $highestCategory ? $highestCategory->category->name : 'N/A',
            'highest_category_amount' => $highestCategory ? $highestCategory->total : 0,
            'lowest_category' => $lowestCategory ? $lowestCategory->category->name : 'N/A',
            'lowest_category_amount' => $lowestCategory ? $lowestCategory->total : 0,
            'recent_transactions' => $recentTransactions,
            'spend_last_month' => $spendLastMonth,
            'monthly_diff_percent' => $monthlyDiffPercent,
            'budget_limit' => $monthlyBudget,
            'budget_remaining' => $budgetRemaining,
            'savings_amount' => $savingsAmount,
        ];
    }

    public function getWeekdayVsWeekend(User $user, ?Carbon $month = null): array
    {
        $targetMonth = $month ?? Carbon::now();
        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        $expenses = $user->expenses()->whereBetween('date', [$start, $end])->get();
        if ($expenses->isEmpty()) {
            $expenses = $user->expenses()->get();
        }

        $weekdayTotal = 0;
        $weekendTotal = 0;

        foreach ($expenses as $expense) {
            $dayOfWeek = Carbon::parse($expense->date)->dayOfWeek;
            if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                $weekendTotal += $expense->amount;
            } else {
                $weekdayTotal += $expense->amount;
            }
        }

        $total = $weekdayTotal + $weekendTotal;
        $diffPercent = $weekdayTotal > 0 ? round((($weekendTotal - $weekdayTotal) / $weekdayTotal) * 100, 1) : 0;

        return [
            'weekday_spending' => round($weekdayTotal, 2),
            'weekend_spending' => round($weekendTotal, 2),
            'diff_percent' => $diffPercent,
            'weekday_percent' => $total > 0 ? round(($weekdayTotal / $total) * 100, 1) : 0,
            'weekend_percent' => $total > 0 ? round(($weekendTotal / $total) * 100, 1) : 0,
        ];
    }

    public function getMonthlyTrend(User $user, int $months = 6): array
    {
        $labels = [];
        $totals = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $totals[] = (float) $user->expenses()->whereBetween('date', [$start, $end])->sum('amount');
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
        ];
    }

    public function getCategoryBreakdown(User $user, ?Carbon $month = null): array
    {
        $targetMonth = $month ?? Carbon::now();
        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        // Query current month categories
        $categories = $user->expenses()
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Fallback to all-time if current month has no category expenses
        if ($categories->isEmpty()) {
            $categories = $user->expenses()
                ->whereNotNull('category_id')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category')
                ->get();
        }

        $labels = [];
        $values = [];
        $colors = [];

        foreach ($categories as $cat) {
            if ($cat->category) {
                $labels[] = $cat->category->name;
                $values[] = (float) $cat->total;
                $colors[] = $cat->category->color ?? '#6366f1';
            }
        }

        // Placeholder if no expenses exist at all
        if (empty($labels)) {
            $labels = ['Uncategorized'];
            $values = [1];
            $colors = ['#94a3b8'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    public function getDailyHeatmap(User $user, ?Carbon $month = null): array
    {
        $targetMonth = $month ?? Carbon::now();
        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        $dailySums = $user->expenses()
            ->whereBetween('date', [$start, $end])
            ->select('date', DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $heatmap = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $heatmap[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'day_name' => $current->format('D'),
                'amount' => (float) ($dailySums[$dateStr] ?? 0),
            ];
            $current->addDay();
        }

        return $heatmap;
    }
}
