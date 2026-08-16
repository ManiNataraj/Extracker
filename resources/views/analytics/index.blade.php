@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div class="clay-card p-6 md:p-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 clay-badge">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <span>Monthly & Historical Analytics</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Deep financial insights, spending velocity, and category dynamics.</p>
        </div>
    </div>

    <!-- Analytics Top Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total All-Time Spent</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ auth()->user()->currency_symbol }}{{ number_format($totalSpentAllTime, 2) }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $totalTransactions }} Transactions</p>
        </div>

        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">Highest Expense Day</span>
            <div class="text-2xl font-black text-rose-500 dark:text-rose-400">
                {{ $highestExpenseDay ? \Carbon\Carbon::parse($highestExpenseDay->date)->format('M d') : 'N/A' }}
            </div>
            <p class="text-xs text-rose-500 font-bold">
                {{ auth()->user()->currency_symbol }}{{ number_format($highestExpenseDay ? $highestExpenseDay->total : 0, 2) }}
            </p>
        </div>

        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-purple-500">Single Max Expense</span>
            <div class="text-2xl font-black text-purple-500 dark:text-purple-400 truncate">
                {{ auth()->user()->currency_symbol }}{{ number_format($maxExpense ? $maxExpense->amount : 0, 2) }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate">{{ $maxExpense ? $maxExpense->title : 'N/A' }}</p>
        </div>

        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-500">Weekend Variance</span>
            <div class="text-2xl font-black text-amber-500 dark:text-amber-400">
                {{ $weekdayVsWeekend['diff_percent'] }}%
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Weekend vs Weekday ratio</p>
        </div>
    </div>

    <!-- Charts Row 1: Line Area Chart + Doughnut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 clay-card p-6 space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-indigo-500"></i>
                <span>Yearly Spending Trajectory (Area Chart)</span>
            </h3>
            <div class="relative w-full h-64">
                <canvas id="areaChart"></canvas>
            </div>
        </div>

        <div class="clay-card p-6 space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-purple-500"></i>
                <span>Category Share (Doughnut Chart)</span>
            </h3>
            <div class="relative w-full h-64">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';
        const tickColor = isDark ? '#94a3b8' : '#64748b';

        // Area Chart
        const areaCtx = document.getElementById('areaChart').getContext('2d');
        new Chart(areaCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend['labels']) !!},
                datasets: [{
                    label: 'Spent ({{ auth()->user()->currency_symbol }})',
                    data: {!! json_encode($monthlyTrend['totals']) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.25)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { family: 'Plus Jakarta Sans', weight: '600' } } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { family: 'Plus Jakarta Sans', weight: '600' } } }
                }
            }
        });

        // Doughnut Chart
        const doughCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryBreakdown['labels']) !!},
                datasets: [{
                    data: {!! json_encode($categoryBreakdown['values']) !!},
                    backgroundColor: {!! json_encode($categoryBreakdown['colors']) !!},
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: tickColor,
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
