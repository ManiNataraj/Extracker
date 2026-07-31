<?php

namespace App\Services;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getCategoryStats(User $user, Category $category): array
    {
        $expenses = $category->expenses()->where('user_id', $user->id)->get();
        $totalSpent = $expenses->sum('amount');

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $monthlySpent = $expenses->filter(function ($exp) use ($startOfMonth, $endOfMonth) {
            return $exp->date >= $startOfMonth->format('Y-m-d') && $exp->date <= $endOfMonth->format('Y-m-d');
        })->sum('amount');

        $userTotalSpent = $user->expenses()->sum('amount');
        $percentageOfTotal = $userTotalSpent > 0 ? round(($totalSpent / $userTotalSpent) * 100, 1) : 0;

        return [
            'total_spent' => (float) $totalSpent,
            'monthly_spent' => (float) $monthlySpent,
            'percentage_of_total' => $percentageOfTotal,
            'transaction_count' => $expenses->count(),
        ];
    }

    public function createCustomCategory(User $user, array $data): Category
    {
        return Category::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'] ?? 'tag',
            'color' => $data['color'] ?? '#6366f1',
            'is_food' => $data['is_food'] ?? false,
        ]);
    }
}
