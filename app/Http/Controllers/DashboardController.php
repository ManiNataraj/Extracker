<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use App\Services\FoodIntelligenceService;
use App\Services\SmartInsightService;
use App\Services\BudgetService;

class DashboardController extends Controller
{
    public function index(Request $request, AnalyticsService $analyticsService, FoodIntelligenceService $foodService, SmartInsightService $insightService, BudgetService $budgetService)
    {
        $user = $request->user();

        $metrics = $analyticsService->getDashboardMetrics($user);
        $foodAnalytics = $foodService->getFoodAnalytics($user);
        $insights = $insightService->generateInsights($user);
        $budgets = $budgetService->getBudgetsOverview($user);
        $weekdayVsWeekend = $analyticsService->getWeekdayVsWeekend($user);
        $monthlyTrend = $analyticsService->getMonthlyTrend($user, 6);
        $categoryBreakdown = $analyticsService->getCategoryBreakdown($user);
        $heatmap = $analyticsService->getDailyHeatmap($user);

        return view('dashboard', compact(
            'metrics',
            'foodAnalytics',
            'insights',
            'budgets',
            'weekdayVsWeekend',
            'monthlyTrend',
            'categoryBreakdown',
            'heatmap'
        ));
    }
}
