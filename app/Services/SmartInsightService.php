<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SmartInsightService
{
    public function generateInsights(User $user): array
    {
        $insights = [];
        $symbol = $user->currency_symbol ?? '₹';

        $now = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $endThisMonth = $now->copy()->endOfMonth();

        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth = $now->copy()->subMonth()->endOfMonth();

        // 1. Monthly change insight
        $thisMonthSpent = (float) $user->expenses()->whereBetween('date', [$startThisMonth, $endThisMonth])->sum('amount');
        $lastMonthSpent = (float) $user->expenses()->whereBetween('date', [$startLastMonth, $endLastMonth])->sum('amount');

        if ($lastMonthSpent > 0) {
            $diff = (($thisMonthSpent - $lastMonthSpent) / $lastMonthSpent) * 100;
            if ($diff > 0) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'trending-up',
                    'text' => sprintf("You spent %.1f%% more this month compared to last month.", abs($diff)),
                ];
            } else {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'trending-down',
                    'text' => sprintf("Great job! Your spending decreased by %.1f%% compared to last month.", abs($diff)),
                ];
            }
        }

        // 2. Highest expense category
        $topCat = $user->expenses()
            ->whereBetween('date', [$startThisMonth, $endThisMonth])
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->first();

        if ($topCat && $topCat->category) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'pie-chart',
                'text' => sprintf("%s is your highest expense category this month (%s%s).", $topCat->category->name, $symbol, number_format($topCat->total, 2)),
            ];
        }

        // 3. Weekend vs Weekday spending
        $analytics = app(AnalyticsService::class)->getWeekdayVsWeekend($user, $now);
        if ($analytics['weekend_spending'] > $analytics['weekday_spending']) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'calendar',
                'text' => "You spend more on weekends than during weekdays. Consider tracking leisure activities.",
            ];
        } else {
            $insights[] = [
                'type' => 'info',
                'icon' => 'briefcase',
                'text' => "Your weekday spending accounts for most of your expenses this month.",
            ];
        }

        // 4. Snacks / Junk food alert
        $snackSpend = $user->expenses()
            ->whereBetween('date', [$startThisMonth, $endThisMonth])
            ->where(function ($q) {
                $q->where('title', 'LIKE', '%snack%')
                  ->orWhere('title', 'LIKE', '%tea%')
                  ->orWhere('title', 'LIKE', '%fast food%');
            })
            ->sum('amount');

        if ($snackSpend > 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'coffee',
                'text' => sprintf("You spent %s%s on snacks and fast food this month.", $symbol, number_format($snackSpend, 2)),
            ];
        }

        // 5. Budget usage insight
        $budgetLimit = $user->monthly_budget_limit ?? 50000;
        $usedPct = ($thisMonthSpent / $budgetLimit) * 100;
        if ($usedPct > 90) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'alert-triangle',
                'text' => sprintf("Overspending Alert: You have used %.1f%% of your monthly budget limit!", $usedPct),
            ];
        } elseif ($usedPct > 75) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'alert-circle',
                'text' => sprintf("Caution: You have utilized %.1f%% of your monthly budget.", $usedPct),
            ];
        }

        return $insights;
    }
}
