<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Tag;
use App\Models\FoodSubcategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->expenses()->with(['category', 'tags', 'foodSubcategory']);

        // Date Range Preset Filter
        if ($request->filled('date_preset')) {
            switch ($request->date_preset) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('date', Carbon::yesterday());
                    break;
                case 'last_7_days':
                    $query->whereBetween('date', [Carbon::now()->subDays(6), Carbon::now()]);
                    break;
                case 'this_month':
                    $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'last_month':
                    $start = Carbon::now()->subMonth()->startOfMonth();
                    $end = Carbon::now()->subMonth()->endOfMonth();
                    $query->whereBetween('date', [$start, $end]);
                    break;
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Tag Filter
        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('amount', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortField = $request->input('sort_by', 'date');
        $sortDirection = $request->input('sort_dir', 'desc');

        if (in_array($sortField, ['date', 'amount', 'title'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('date', 'desc');
        }

        $expenses = $query->paginate(12)->withQueryString();

        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();
        $tags = Tag::where('user_id', $user->id)->orWhereNull('user_id')->get();
        $foodSubcategories = FoodSubcategory::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('expenses.index', compact('expenses', 'categories', 'tags', 'foodSubcategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'food_subcategory_id' => 'nullable|exists:food_subcategories,id',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'location' => 'nullable|string',
            'is_healthy' => 'nullable|boolean',
            'mood' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $user = $request->user();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        $validated['user_id'] = $user->id;

        $expense = Expense::create($validated);

        if (!empty($validated['tags'])) {
            $expense->tags()->sync($validated['tags']);
        }

        return redirect()->back()->with('success', 'Expense recorded successfully!');
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'food_subcategory_id' => 'nullable|exists:food_subcategories,id',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'location' => 'nullable|string',
            'is_healthy' => 'nullable|boolean',
            'mood' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'tags' => 'nullable|array',
        ]);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path) {
                Storage::disk('public')->delete($expense->attachment_path);
            }
            $validated['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $expense->update($validated);

        if (isset($validated['tags'])) {
            $expense->tags()->sync($validated['tags']);
        }

        return redirect()->back()->with('success', 'Expense updated successfully!');
    }

    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully!');
    }
}
