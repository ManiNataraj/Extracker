@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div class="clay-card p-6 md:p-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 clay-badge">
                    <i data-lucide="apple" class="w-6 h-6"></i>
                </div>
                <span>Food & Lifestyle Intelligence</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Deep analysis of eating habits, healthy choices vs junk food spending ratios.</p>
        </div>
    </div>

    <!-- Key Metrics Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Food Spend</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['total_food_spent'], 2) }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Current Month Total</p>
        </div>

        <div class="clay-card p-6 space-y-3 border-l-4 border-l-emerald-500">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-500">Healthy Food Spend</span>
            <div class="text-2xl font-black text-emerald-500 dark:text-emerald-400">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['healthy_spent'], 2) }}
            </div>
            <p class="text-xs text-emerald-500 font-bold">{{ $analytics['healthy_percent'] }}% of food budget</p>
        </div>

        <div class="clay-card p-6 space-y-3 border-l-4 border-l-rose-500">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">Junk / Fast Food Spend</span>
            <div class="text-2xl font-black text-rose-500 dark:text-rose-400">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['junk_spent'], 2) }}
            </div>
            <p class="text-xs text-rose-500 font-bold">{{ $analytics['junk_percent'] }}% of food budget</p>
        </div>

        <div class="clay-card p-6 space-y-3">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Lifestyle Health Score</span>
            <div class="text-2xl font-black text-indigo-500 dark:text-indigo-400">
                {{ $analytics['healthy_percent'] >= 60 ? 'A (Healthy)' : ($analytics['healthy_percent'] >= 40 ? 'B (Moderate)' : 'C (Needs Work)') }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Based on food ratios</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subcategory Breakdown -->
        <div class="clay-card p-6 space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-500"></i>
                <span>Subcategory Spend Breakdown</span>
            </h3>
            <div class="space-y-3">
                @foreach($analytics['subcategory_breakdown'] as $subName => $amount)
                <div class="p-4 rounded-2xl clay-inset flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $subName }}</span>
                    <span class="text-sm font-black text-emerald-500 dark:text-emerald-400">{{ auth()->user()->currency_symbol }}{{ number_format($amount, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Weekly Food Analysis -->
        <div class="clay-card p-6 space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-cyan-500"></i>
                <span>Weekly Food Progression</span>
            </h3>
            <div class="h-64">
                <canvas id="weeklyFoodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Food Expenses Table -->
    <div class="clay-card overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Food Transaction Logs</h3>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800/80">
            @forelse($foodExpenses as $exp)
            <div class="p-5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                <div class="space-y-1">
                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $exp->title }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <span>{{ $exp->foodSubcategory ? $exp->foodSubcategory->name : 'General Food' }}</span>
                        <span>•</span>
                        <span>{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 rounded-xl text-[10px] font-extrabold {{ $exp->is_healthy ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                        {{ $exp->is_healthy ? '🥗 Healthy' : '🍔 Fast Food' }}
                    </span>
                    <span class="text-base font-black text-slate-900 dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">No food expenses recorded for this month.</div>
            @endforelse
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

        const weeklyData = {!! json_encode($analytics['weekly_food_analysis']) !!};
        const ctx = document.getElementById('weeklyFoodChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(weeklyData),
                datasets: [{
                    label: 'Weekly Food Spend ({{ auth()->user()->currency_symbol }})',
                    data: Object.values(weeklyData),
                    backgroundColor: '#10b981',
                    borderRadius: 14
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: tickColor, font: { family: 'Plus Jakarta Sans', weight: '600' } } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { family: 'Plus Jakarta Sans', weight: '600' } } }
                }
            }
        });
    });
</script>
@endpush
