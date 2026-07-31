<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request, CategoryService $categoryService)
    {
        $user = $request->user();
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        $categoryStats = [];
        foreach ($categories as $cat) {
            $categoryStats[$cat->id] = $categoryService->getCategoryStats($user, $cat);
        }

        return view('categories.index', compact('categories', 'categoryStats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_food' => 'nullable|boolean',
        ]);

        $user = $request->user();

        $category = Category::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? 'tag',
            'color' => $validated['color'] ?? '#6366f1',
            'is_food' => $request->has('is_food'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id && $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_food' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'is_food' => $request->has('is_food'),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id && $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully! Existing expenses remain intact.');
    }
}
