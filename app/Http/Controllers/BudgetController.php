<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;

class BudgetController extends Controller
{
    public function index(Request $request, BudgetService $budgetService)
    {
        $user = $request->user();
        $overview = $budgetService->getBudgetsOverview($user);
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('budgets.index', compact('overview', 'categories'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|string|in:monthly,weekly,category',
            'amount' => 'required|numeric|min:1',
        ]);

        if (empty($validated['category_id'])) {
            // Update global monthly limit
            $user->update(['monthly_budget_limit' => $validated['amount']]);
        }

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'] ?? null,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        return redirect()->back()->with('success', 'Budget target saved successfully!');
    }

    public function destroy(Request $request, Budget $budget)
    {
        if ($budget->user_id !== $request->user()->id) {
            abort(403);
        }

        $budget->delete();

        return redirect()->back()->with('success', 'Budget removed successfully!');
    }
}
