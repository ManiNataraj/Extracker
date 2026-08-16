@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Welcome & Quick Actions (Clay Soft Banner) -->
    <div class="clay-card p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-1 z-10">
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <span class="px-3 py-1 text-[11px] font-extrabold rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-500/20 clay-badge">
                    Pro Member
                </span>
            </div>
            <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 font-medium">
                Here is your soft financial pulse and spending intelligence for {{ date('F Y') }}.
            </p>
        </div>

        <div class="flex items-center gap-3 z-10">
            <a href="{{ route('reports.index') }}" class="px-5 py-2.5 clay-btn text-xs font-extrabold flex items-center space-x-2">
                <i data-lucide="download" class="w-4 h-4 text-indigo-500"></i>
                <span>Download Report</span>
            </a>
            <button @click="quickModalOpen = true" class="px-5 py-2.5 clay-btn-primary text-xs font-extrabold flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Quick Expense</span>
            </button>
        </div>
    </div>

    <!-- Smart Spending Insights Feed -->
    @if(!empty($insights))
    <div class="space-y-3">
        <h2 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-400 flex items-center gap-2 px-1">
            <i data-lucide="sparkles" class="w-4 h-4 text-indigo-500"></i>
            <span>Smart Spending Insights</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($insights as $insight)
            <div class="clay-card p-4 flex items-start space-x-3.5 border-l-4 
                {{ $insight['type'] === 'danger' ? 'border-l-rose-500' : ($insight['type'] === 'warning' ? 'border-l-amber-500' : 'border-l-indigo-500') }}">
                <div class="p-2.5 rounded-2xl clay-badge 
                    {{ $insight['type'] === 'danger' ? 'bg-rose-500/10 text-rose-500' : ($insight['type'] === 'warning' ? 'bg-amber-500/10 text-amber-500' : 'bg-indigo-500/10 text-indigo-400') }}">
                    <i data-lucide="{{ $insight['icon'] }}" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 leading-relaxed">{{ $insight['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Key Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Spend -->
        <div class="clay-card p-6 space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Spent Today</span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_today'], 2) }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 flex items-center justify-center clay-badge">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span>Daily Avg Limit</span>
                <span class="font-bold text-indigo-500 dark:text-indigo-400">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['avg_daily_expense'], 2) }}</span>
            </div>
        </div>

        <!-- This Week -->
        <div class="clay-card p-6 space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">This Week</span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_this_week'], 2) }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-500 dark:text-cyan-400 flex items-center justify-center clay-badge">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span>7-Day Run</span>
                <span class="font-bold text-cyan-500 dark:text-cyan-400">Active</span>
            </div>
        </div>

        <!-- This Month -->
        <div class="clay-card p-6 space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">This Month</span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_this_month'], 2) }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 flex items-center justify-center clay-badge">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span>vs Last Month</span>
                @if($metrics['monthly_diff_percent'] > 0)
                <span class="text-rose-500 font-extrabold">↑ {{ $metrics['monthly_diff_percent'] }}%</span>
                @else
                <span class="text-emerald-500 font-extrabold">↓ {{ abs($metrics['monthly_diff_percent']) }}%</span>
                @endif
            </div>
        </div>

        <!-- Remaining Budget -->
        <div class="clay-card p-6 space-y-4 hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Remaining Budget</span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['budget_remaining'], 2) }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-500 dark:text-purple-400 flex items-center justify-center clay-badge">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[11px] font-bold text-slate-400">
                    <span>Used</span>
                    <span>{{ $budgets['global_used_percent'] }}%</span>
                </div>
                <div class="w-full clay-inset h-2 overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r from-indigo-500 to-cyan-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $budgets['global_used_percent']) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Soft Clay Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="clay-card p-5 flex items-center space-x-4">
            <div class="p-3.5 rounded-2xl bg-purple-500/10 text-purple-500 dark:text-purple-400 clay-badge">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Highest Category</span>
                <p class="text-base font-extrabold text-slate-900 dark:text-white">{{ $metrics['highest_category'] }}</p>
                <p class="text-xs text-purple-500 font-extrabold">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['highest_category_amount'], 2) }}</p>
            </div>
        </div>

        <div class="clay-card p-5 flex items-center space-x-4">
            <div class="p-3.5 rounded-2xl bg-cyan-500/10 text-cyan-500 dark:text-cyan-400 clay-badge">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Avg Daily Spend</span>
                <p class="text-base font-extrabold text-slate-900 dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['avg_daily_expense'], 2) }}</p>
                <p class="text-xs text-cyan-500 font-extrabold">Per calendar day</p>
            </div>
        </div>

        <div class="clay-card p-5 flex items-center space-x-4">
            <div class="p-3.5 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 clay-badge">
                <i data-lucide="piggy-bank" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Est. Monthly Savings</span>
                <p class="text-base font-extrabold text-slate-900 dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['savings_amount'], 2) }}</p>
                <p class="text-xs text-emerald-500 font-extrabold">Allowance target</p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Monthly Trend Line & Category Breakdown Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Spending Trend (2 Cols) -->
        <div class="lg:col-span-2 clay-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="line-chart" class="w-5 h-5 text-indigo-500"></i>
                        <span>Monthly Spending Trend</span>
                    </h3>
                    <p class="text-xs text-slate-400">Track month-over-month expense progression</p>
                </div>
            </div>
            <div class="relative w-full h-64">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution (1 Col) -->
        <div class="clay-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-purple-500"></i>
                        <span>Category Breakdown</span>
                    </h3>
                    <p class="text-xs text-slate-400">Current month distribution</p>
                </div>
            </div>
            <div class="relative w-full h-64">
                <canvas id="categoryPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Food Intel & Weekday vs Weekend -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Food Intelligence Pie -->
        <div class="clay-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="apple" class="w-5 h-5 text-emerald-500"></i>
                        <span>Food Intelligence Ratio</span>
                    </h3>
                    <p class="text-xs text-slate-400">Healthy vs Junk Food spending analysis</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 font-extrabold clay-badge">
                    {{ $foodAnalytics['healthy_percent'] }}% Healthy
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <div class="relative w-full h-52">
                    <canvas id="foodPieChart"></canvas>
                </div>
                <div class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Healthy Food Spend</div>
                        <div class="text-lg font-black text-emerald-500 dark:text-emerald-400 mt-0.5">
                            {{ auth()->user()->currency_symbol }}{{ number_format($foodAnalytics['healthy_spent'], 2) }}
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Junk / Fast Food Spend</div>
                        <div class="text-lg font-black text-rose-500 dark:text-rose-400 mt-0.5">
                            {{ auth()->user()->currency_symbol }}{{ number_format($foodAnalytics['junk_spent'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekday vs Weekend Spending Bar Chart -->
        <div class="clay-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-5 h-5 text-cyan-500"></i>
                        <span>Weekend vs Weekday</span>
                    </h3>
                    <p class="text-xs text-slate-400">Behavioral spending pattern comparison</p>
                </div>
            </div>
            <div class="relative w-full h-52">
                <canvas id="weekdayVsWeekendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Daily Expense Activity Heatmap Grid -->
    <div class="clay-card p-6 space-y-4">
        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="grid" class="w-5 h-5 text-indigo-500"></i>
            <span>Daily Expense Activity Heatmap ({{ date('F Y') }})</span>
        </h3>
        <div class="grid grid-cols-7 sm:grid-cols-10 md:grid-cols-16 gap-2.5">
            @foreach($heatmap as $day)
            <div title="{{ $day['date'] }}: {{ auth()->user()->currency_symbol }}{{ number_format($day['amount'], 2) }}"
                 class="h-10 rounded-2xl p-1 text-[10px] font-extrabold flex flex-col justify-between items-center transition hover:scale-110 cursor-pointer clay-badge
                 {{ $day['amount'] > 3000 ? 'bg-indigo-500 text-white' : ($day['amount'] > 1000 ? 'bg-indigo-600/70 text-white' : ($day['amount'] > 0 ? 'bg-indigo-950/60 text-indigo-300 border border-indigo-500/30' : 'bg-slate-800/40 text-slate-500')) }}">
                <span>{{ $day['day'] }}</span>
                @if($day['amount'] > 0)
                <span class="truncate max-w-full">k</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Expenses Table / Soft Clay List -->
    <div class="clay-card overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-indigo-500"></i>
                <span>Recent Expenses</span>
            </h3>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 clay-btn text-xs font-extrabold flex items-center space-x-1.5">
                <span>View All Expenses</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="divide-y divide-slate-200 dark:divide-slate-800/80">
            @forelse($metrics['recent_transactions'] as $exp)
            @php
                $catName = strtolower($exp->category->name ?? '');
                $icon = $exp->category->icon ?? 'tag';
                if ($icon === 'tag' || empty($icon)) {
                    if (str_contains($catName, 'food')) $icon = 'utensils';
                    elseif (str_contains($catName, 'snack')) $icon = 'coffee';
                    elseif (str_contains($catName, 'medical')) $icon = 'heart-pulse';
                    elseif (str_contains($catName, 'clot')) $icon = 'shirt';
                    elseif (str_contains($catName, 'fuel')) $icon = 'zap';
                    elseif (str_contains($catName, 'electricity')) $icon = 'bolt';
                    elseif (str_contains($catName, 'mobile') || str_contains($catName, 'phone')) $icon = 'smartphone';
                    elseif (str_contains($catName, 'internet')) $icon = 'wifi';
                    elseif (str_contains($catName, 'rent')) $icon = 'home';
                    elseif (str_contains($catName, 'travel')) $icon = 'plane';
                    elseif (str_contains($catName, 'education')) $icon = 'graduation-cap';
                    elseif (str_contains($catName, 'shop')) $icon = 'shopping-cart';
                    elseif (str_contains($catName, 'entertain')) $icon = 'gamepad-2';
                    elseif (str_contains($catName, 'subscript')) $icon = 'tv';
                }
            @endphp
            <div class="p-5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold clay-badge" style="background-color: {{ $exp->category->color ?? '#6366f1' }};">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $exp->title }}</div>
                        <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            <span class="font-bold" style="color: {{ $exp->category->color ?? '#6366f1' }};">{{ $exp->category->name ?? 'Uncategorized' }}</span>
                            <span>•</span>
                            <span>{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</span>
                            @if($exp->payment_method)
                            <span>•</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-200 dark:bg-slate-800 font-semibold">{{ $exp->payment_method }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-base font-black text-slate-900 dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                    </span>
                    @if($exp->is_healthy !== null)
                    <div class="text-[10px] font-extrabold {{ $exp->is_healthy ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $exp->is_healthy ? '🥗 Healthy' : '🍔 Fast Food' }}
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">No recent transactions recorded yet.</div>
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

        // 1. Monthly Trend Line Chart
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend['labels']) !!},
                datasets: [{
                    label: 'Monthly Expense ({{ auth()->user()->currency_symbol }})',
                    data: {!! json_encode($monthlyTrend['totals']) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.15)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 5
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

        // 2. Category Pie / Doughnut Chart
        const pieCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(pieCtx, {
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

        // 3. Healthy vs Junk Food Pie Chart
        const foodCtx = document.getElementById('foodPieChart').getContext('2d');
        new Chart(foodCtx, {
            type: 'pie',
            data: {
                labels: ['Healthy Food', 'Junk / Fast Food'],
                datasets: [{
                    data: [{{ $foodAnalytics['healthy_spent'] }}, {{ $foodAnalytics['junk_spent'] }}],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 4. Weekday vs Weekend Bar Chart
        const barCtx = document.getElementById('weekdayVsWeekendChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Weekday Spending', 'Weekend Spending'],
                datasets: [{
                    label: 'Amount ({{ auth()->user()->currency_symbol }})',
                    data: [{{ $weekdayVsWeekend['weekday_spending'] }}, {{ $weekdayVsWeekend['weekend_spending'] }}],
                    backgroundColor: ['#0ea5e9', '#f59e0b'],
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
