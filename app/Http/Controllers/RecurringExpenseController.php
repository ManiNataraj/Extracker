<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecurringExpense;
use App\Models\Category;
use App\Services\RecurringExpenseService;

class RecurringExpenseController extends Controller
{
    public function index(Request $request, RecurringExpenseService $recurringService)
    {
        $user = $request->user();
        
        // Trigger auto-check for due items
        $recurringService->processDueRecurringExpenses($user);

        $recurringExpenses = $user->recurringExpenses()->with('category')->get();
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('recurring.index', compact('recurringExpenses', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'frequency' => 'required|string|in:daily,weekly,monthly,yearly',
            'payment_method' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['is_active'] = true;

        RecurringExpense::create($validated);

        return redirect()->back()->with('success', 'Recurring expense created successfully!');
    }

    public function toggle(Request $request, RecurringExpense $recurringExpense)
    {
        if ($recurringExpense->user_id !== $request->user()->id) {
            abort(403);
        }

        $recurringExpense->update(['is_active' => !$recurringExpense->is_active]);

        return redirect()->back()->with('success', 'Recurring expense status updated!');
    }

    public function destroy(Request $request, RecurringExpense $recurringExpense)
    {
        if ($recurringExpense->user_id !== $request->user()->id) {
            abort(403);
        }

        $recurringExpense->delete();

        return redirect()->back()->with('success', 'Recurring expense deleted!');
    }
}
