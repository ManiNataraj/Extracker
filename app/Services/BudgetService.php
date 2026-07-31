<?php

namespace App\Services;

use App\Models\User;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    public function getBudgetsOverview(User $user): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $userBudgets = $user->budgets()->with('category')->get();

        $items = [];
        $globalMonthlyLimit = $user->monthly_budget_limit ?? 50000;
        $totalSpentThisMonth = (float) $user->expenses()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');

        $globalBudgetUsedPct = $globalMonthlyLimit > 0 ? min(100, round(($totalSpentThisMonth / $globalMonthlyLimit) * 100, 1)) : 0;

        foreach ($userBudgets as $budget) {
            if ($budget->category_id) {
                $spent = (float) $user->expenses()
                    ->where('category_id', $budget->category_id)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');
            } else {
                $spent = $totalSpentThisMonth;
            }

            $remaining = max(0, $budget->amount - $spent);
            $usedPct = $budget->amount > 0 ? min(100, round(($spent / $budget->amount) * 100, 1)) : 0;
            $isOverspent = $spent > $budget->amount;

            $items[] = [
                'id' => $budget->id,
                'category_name' => $budget->category ? $budget->category->name : 'Global Budget',
                'category_color' => $budget->category ? $budget->category->color : '#6366f1',
                'type' => $budget->type,
                'amount' => (float) $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'used_percent' => $usedPct,
                'is_overspent' => $isOverspent,
            ];
        }

        return [
            'global_limit' => $globalMonthlyLimit,
            'total_spent' => $totalSpentThisMonth,
            'global_used_percent' => $globalBudgetUsedPct,
            'global_remaining' => max(0, $globalMonthlyLimit - $totalSpentThisMonth),
            'items' => $items,
        ];
    }
}
