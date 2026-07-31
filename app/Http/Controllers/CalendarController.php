<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $user = $request->user();
        $start = $request->input('start', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $dailyExpenses = $user->expenses()
            ->whereBetween('date', [$start, $end])
            ->select('date', DB::raw('SUM(amount) as total'), DB::raw('COUNT(id) as count'))
            ->groupBy('date')
            ->get();

        $symbol = $user->currency_symbol ?? '₹';
        $events = [];

        foreach ($dailyExpenses as $daily) {
            $events[] = [
                'title' => $symbol . number_format($daily->total, 2) . " ({$daily->count})",
                'start' => $daily->date,
                'color' => '#6366f1',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'date' => $daily->date,
                    'total' => $daily->total,
                    'count' => $daily->count,
                ],
            ];
        }

        return response()->json($events);
    }

    public function dateDetails(Request $request, string $date)
    {
        $user = $request->user();
        $expenses = $user->expenses()
            ->whereDate('date', $date)
            ->with(['category', 'tags'])
            ->get();

        $totalSpent = $expenses->sum('amount');
        $categoryBreakdown = $expenses->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount');
        });

        return response()->json([
            'date' => Carbon::parse($date)->format('M d, Y'),
            'total_spent' => (float) $totalSpent,
            'expenses' => $expenses,
            'category_breakdown' => $categoryBreakdown,
        ]);
    }
}
