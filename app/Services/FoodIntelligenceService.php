<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use Carbon\Carbon;

class FoodIntelligenceService
{
    public function getFoodAnalytics(User $user, ?Carbon $month = null): array
    {
        $targetMonth = $month ?? Carbon::now();
        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        // Get food expenses
        $foodExpenses = $user->expenses()
            ->whereBetween('date', [$start, $end])
            ->whereHas('category', function ($query) {
                $query->where('is_food', true)->orWhere('slug', 'food')->orWhere('name', 'Food');
            })
            ->orWhereNotNull('food_subcategory_id')
            ->whereBetween('date', [$start, $end])
            ->where('user_id', $user->id)
            ->with(['foodSubcategory', 'category'])
            ->get();

        $totalFoodSpent = $foodExpenses->sum('amount');

        $healthySpent = 0;
        $junkSpent = 0;

        foreach ($foodExpenses as $expense) {
            $isHealthy = $expense->is_healthy;
            if ($isHealthy === null && $expense->foodSubcategory) {
                $isHealthy = $expense->foodSubcategory->is_healthy;
            }
            // Fallback heuristics if unspecified
            if ($isHealthy === null) {
                $title = strtolower($expense->title);
                if (str_contains($title, 'pizza') || str_contains($title, 'burger') || str_contains($title, 'fast food') || str_contains($title, 'coke') || str_contains($title, 'fries')) {
                    $isHealthy = false;
                } else {
                    $isHealthy = true;
                }
            }

            if ($isHealthy) {
                $healthySpent += $expense->amount;
            } else {
                $junkSpent += $expense->amount;
            }
        }

        $healthyPercent = $totalFoodSpent > 0 ? round(($healthySpent / $totalFoodSpent) * 100, 1) : 0;
        $junkPercent = $totalFoodSpent > 0 ? round(($junkSpent / $totalFoodSpent) * 100, 1) : 0;

        // Subcategory Breakdown
        $subcategoryTotals = [];
        foreach ($foodExpenses as $expense) {
            $subName = $expense->foodSubcategory ? $expense->foodSubcategory->name : 'Other Food';
            if (!isset($subcategoryTotals[$subName])) {
                $subcategoryTotals[$subName] = 0;
            }
            $subcategoryTotals[$subName] += $expense->amount;
        }

        // Weekly food analysis
        $weeklyFood = [];
        for ($w = 1; $w <= 4; $w++) {
            $wStart = $start->copy()->addWeeks($w - 1);
            $wEnd = $wStart->copy()->endOfWeek()->min($end);
            $weeklyFood["Week $w"] = (float) $foodExpenses->filter(function ($item) use ($wStart, $wEnd) {
                $d = Carbon::parse($item->date);
                return $d->between($wStart, $wEnd);
            })->sum('amount');
        }

        return [
            'total_food_spent' => round($totalFoodSpent, 2),
            'healthy_spent' => round($healthySpent, 2),
            'junk_spent' => round($junkSpent, 2),
            'healthy_percent' => $healthyPercent,
            'junk_percent' => $junkPercent,
            'subcategory_breakdown' => $subcategoryTotals,
            'weekly_food_analysis' => $weeklyFood,
        ];
    }
}
