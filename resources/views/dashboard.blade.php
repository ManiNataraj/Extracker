@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Welcome & Quick Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-cyan-900/40 via-slate-900 to-blue-900/40 p-6 rounded-3xl border border-cyan-500/20 shadow-xl">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <span>Welcome back, {{ auth()->user()->name }}!</span>
                <span class="text-xs px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-400 font-semibold border border-cyan-500/30">Pro Member</span>
            </h1>
            <p class="text-sm text-slate-300 mt-1">Here is your financial pulse and spending intelligence for {{ date('F Y') }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center space-x-2">
                <i data-lucide="download" class="w-4 h-4 text-cyan-400"></i>
                <span>Download Report</span>
            </a>
            <button @click="quickModalOpen = true" class="px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Quick Expense</span>
            </button>
        </div>
    </div>

    <!-- Smart Insights Banner Cards -->
    @if(!empty($insights))
    <div class="space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
            <span>Smart Spending Insights</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($insights as $insight)
            <div class="glass-card p-4 rounded-2xl border flex items-start space-x-3 
                {{ $insight['type'] === 'danger' ? 'border-rose-500/30 bg-rose-950/20' : ($insight['type'] === 'warning' ? 'border-amber-500/30 bg-amber-950/20' : 'border-cyan-500/30 bg-cyan-950/20') }}">
                <div class="p-2.5 rounded-xl 
                    {{ $insight['type'] === 'danger' ? 'bg-rose-500/20 text-rose-400' : ($insight['type'] === 'warning' ? 'bg-amber-500/20 text-amber-400' : 'bg-cyan-500/20 text-cyan-400') }}">
                    <i data-lucide="{{ $insight['icon'] }}" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-200 leading-relaxed">{{ $insight['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Key Metrics Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Today's Spend -->
        <div class="glass-card p-5 rounded-3xl relative overflow-hidden group hover:border-cyan-500/50 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Spent Today</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_today'], 2) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1">
                <span class="text-cyan-400 font-bold">Daily Limit:</span> {{ auth()->user()->currency_symbol }}{{ number_format($metrics['avg_daily_expense'], 2) }}/avg
            </p>
        </div>

        <!-- This Week -->
        <div class="glass-card p-5 rounded-3xl relative overflow-hidden group hover:border-blue-500/50 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">This Week</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_this_week'], 2) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1">
                <span class="text-blue-400 font-bold">7-Day Run</span>
            </p>
        </div>

        <!-- This Month -->
        <div class="glass-card p-5 rounded-3xl relative overflow-hidden group hover:border-indigo-500/50 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">This Month</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['spend_this_month'], 2) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">
                @if($metrics['monthly_diff_percent'] > 0)
                <span class="text-rose-400 font-bold">↑ {{ $metrics['monthly_diff_percent'] }}%</span> vs last month
                @else
                <span class="text-emerald-400 font-bold">↓ {{ abs($metrics['monthly_diff_percent']) }}%</span> vs last month
                @endif
            </p>
        </div>

        <!-- Remaining Budget -->
        <div class="glass-card p-5 rounded-3xl relative overflow-hidden group hover:border-emerald-500/50 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Budget Remaining</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">
                        {{ auth()->user()->currency_symbol }}{{ number_format($metrics['budget_remaining'], 2) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-cyan-500 h-1.5 rounded-full" style="width: {{ $budgets['global_used_percent'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4">
            <div class="p-3 rounded-2xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase">Highest Category</p>
                <p class="text-base font-extrabold text-white">{{ $metrics['highest_category'] }}</p>
                <p class="text-xs text-purple-400 font-bold">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['highest_category_amount'], 2) }}</p>
            </div>
        </div>

        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4">
            <div class="p-3 rounded-2xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase">Avg Daily Spend</p>
                <p class="text-base font-extrabold text-white">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['avg_daily_expense'], 2) }}</p>
                <p class="text-xs text-cyan-400 font-bold">Calculated per day</p>
            </div>
        </div>

        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4">
            <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <i data-lucide="piggy-bank" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase">Est. Monthly Savings</p>
                <p class="text-base font-extrabold text-white">{{ auth()->user()->currency_symbol }}{{ number_format($metrics['savings_amount'], 2) }}</p>
                <p class="text-xs text-emerald-400 font-bold">Based on budget limit</p>
            </div>
        </div>
    </div>

    <!-- Charts Grid Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Spending Trend (2 Cols) -->
        <div class="lg:col-span-2 glass-card p-6 rounded-3xl border border-slate-800">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="line-chart" class="w-5 h-5 text-cyan-400"></i>
                        <span>Monthly Spending Trend</span>
                    </h3>
                    <p class="text-xs text-slate-400">Track month-over-month expense progression</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution (1 Col) -->
        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-purple-400"></i>
                        <span>Category Breakdown</span>
                    </h3>
                    <p class="text-xs text-slate-400">Current month distribution</p>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Grid Row 2: Food Intel & Weekday vs Weekend -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Food Intelligence Pie -->
        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="apple" class="w-5 h-5 text-emerald-400"></i>
                        <span>Food Intelligence Ratio</span>
                    </h3>
                    <p class="text-xs text-slate-400">Healthy vs Junk Food spending analysis</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20">
                    {{ $foodAnalytics['healthy_percent'] }}% Healthy
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <div class="h-52">
                    <canvas id="foodPieChart"></canvas>
                </div>
                <div class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <div class="text-xs text-slate-400 font-semibold">Healthy Food Spend</div>
                        <div class="text-lg font-extrabold text-emerald-400">
                            {{ auth()->user()->currency_symbol }}{{ number_format($foodAnalytics['healthy_spent'], 2) }}
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <div class="text-xs text-slate-400 font-semibold">Junk / Fast Food Spend</div>
                        <div class="text-lg font-extrabold text-rose-400">
                            {{ auth()->user()->currency_symbol }}{{ number_format($foodAnalytics['junk_spent'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekday vs Weekend Spending Bar Chart -->
        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-5 h-5 text-blue-400"></i>
                        <span>Weekend vs Weekday</span>
                    </h3>
                    <p class="text-xs text-slate-400">Behavioral spending pattern comparison</p>
                </div>
            </div>
            <div class="h-52">
                <canvas id="weekdayVsWeekendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Daily Expense Heatmap Grid -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800">
        <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <i data-lucide="grid" class="w-5 h-5 text-cyan-400"></i>
            <span>Daily Expense Activity Heatmap ({{ date('F Y') }})</span>
        </h3>
        <div class="grid grid-cols-7 sm:grid-cols-10 md:grid-cols-16 gap-2">
            @foreach($heatmap as $day)
            <div title="{{ $day['date'] }}: {{ auth()->user()->currency_symbol }}{{ number_format($day['amount'], 2) }}"
                 class="h-10 rounded-xl p-1 text-[10px] font-bold flex flex-col justify-between items-center transition hover:scale-110 cursor-pointer 
                 {{ $day['amount'] > 3000 ? 'bg-cyan-500 text-slate-950' : ($day['amount'] > 1000 ? 'bg-cyan-600/70 text-white' : ($day['amount'] > 0 ? 'bg-cyan-950/60 text-cyan-300 border border-cyan-500/30' : 'bg-slate-900/60 text-slate-600 border border-slate-800')) }}">
                <span>{{ $day['day'] }}</span>
                @if($day['amount'] > 0)
                <span class="truncate max-w-full">k</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-cyan-400"></i>
                <span>Recent Transactions</span>
            </h3>
            <a href="{{ route('expenses.index') }}" class="text-xs font-bold text-cyan-400 hover:underline">View All Expenses →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Title & Details</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($metrics['recent_transactions'] as $exp)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-white">{{ $exp->title }}</div>
                            @if($exp->notes)
                            <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $exp->notes }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($exp->category)
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold" style="background-color: {{ $exp->category->color }}20; color: {{ $exp->category->color }}; border: 1px solid {{ $exp->category->color }}40;">
                                {{ $exp->category->name }}
                            </span>
                            @else
                            <span class="text-slate-500">Uncategorized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-medium">{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-300 font-medium">{{ $exp->payment_method }}</td>
                        <td class="px-6 py-4 text-right font-extrabold text-white text-sm">
                            {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No recent transactions recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Monthly Trend Line Chart
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend['labels']) !!},
                datasets: [{
                    label: 'Monthly Expense ({{ auth()->user()->currency_symbol }})',
                    data: {!! json_encode($monthlyTrend['totals']) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.15)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#0ea5e9',
                    pointRadius: 4
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
                    borderColor: '#0f172a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } }
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
                    borderColor: '#0f172a'
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
                    backgroundColor: ['#3b82f6', '#f59e0b'],
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    });
</script>
@endpush
