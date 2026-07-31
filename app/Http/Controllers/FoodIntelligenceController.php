<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FoodIntelligenceService;
use App\Models\FoodSubcategory;

class FoodIntelligenceController extends Controller
{
    public function index(Request $request, FoodIntelligenceService $foodService)
    {
        $user = $request->user();
        $analytics = $foodService->getFoodAnalytics($user);

        $foodExpenses = $user->expenses()
            ->whereHas('category', function ($q) {
                $q->where('is_food', true)->orWhere('slug', 'food')->orWhere('name', 'Food');
            })
            ->orWhereNotNull('food_subcategory_id')
            ->where('user_id', $user->id)
            ->with(['foodSubcategory', 'category'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        $subcategories = FoodSubcategory::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('food.index', compact('analytics', 'foodExpenses', 'subcategories'));
    }
}
