@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-7 h-7 text-cyan-400"></i>
            <span>Monthly & Historical Analytics</span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Deep financial insights, spending velocity, and category dynamics.</p>
    </div>

    <!-- Analytics Top Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-card p-5 rounded-3xl border border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Total All-Time Spent</div>
            <div class="text-2xl font-extrabold text-white mt-1">
                {{ auth()->user()->currency_symbol }}{{ number_format($totalSpentAllTime, 2) }}
            </div>
            <p class="text-xs text-slate-400 mt-2">{{ $totalTransactions }} Recorded Transactions</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Highest Expense Day</div>
            <div class="text-xl font-extrabold text-rose-400 mt-1">
                {{ $highestExpenseDay ? \Carbon\Carbon::parse($highestExpenseDay->date)->format('M d') : 'N/A' }}
            </div>
            <p class="text-xs text-slate-400 mt-2">
                {{ auth()->user()->currency_symbol }}{{ number_format($highestExpenseDay ? $highestExpenseDay->total : 0, 2) }}
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Single Max Expense</div>
            <div class="text-xl font-extrabold text-purple-400 mt-1">
                {{ auth()->user()->currency_symbol }}{{ number_format($maxExpense ? $maxExpense->amount : 0, 2) }}
            </div>
            <p class="text-xs text-slate-400 mt-2 truncate">{{ $maxExpense ? $maxExpense->title : 'N/A' }}</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Weekend vs Weekday Diff</div>
            <div class="text-xl font-extrabold text-amber-400 mt-1">
                {{ $weekdayVsWeekend['diff_percent'] }}%
            </div>
            <p class="text-xs text-slate-400 mt-2">Weekend spending variance</p>
        </div>
    </div>

    <!-- Charts Row 1: Line Area Chart + Doughnut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass-card p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-cyan-400"></i>
                <span>Yearly Spending Trajectory (Area Chart)</span>
            </h3>
            <div class="h-64">
                <canvas id="areaChart"></canvas>
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-purple-400"></i>
                <span>Category Share (Doughnut Chart)</span>
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Area Chart
        const areaCtx = document.getElementById('areaChart').getContext('2d');
        new Chart(areaCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend['labels']) !!},
                datasets: [{
                    label: 'Spent ({{ auth()->user()->currency_symbol }})',
                    data: {!! json_encode($monthlyTrend['totals']) !!},
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.25)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } }
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
                    borderColor: '#0f172a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } } }
            }
        });
    });
</script>
@endpush
