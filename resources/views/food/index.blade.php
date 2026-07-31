@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
            <i data-lucide="apple" class="w-7 h-7 text-emerald-400"></i>
            <span>Food & Lifestyle Intelligence</span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Deep analysis of eating habits, healthy choices vs junk food spending ratios.</p>
    </div>

    <!-- Key Metrics Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-card p-5 rounded-3xl border border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Food Spend</div>
            <div class="text-2xl font-extrabold text-white mt-1">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['total_food_spent'], 2) }}
            </div>
            <p class="text-xs text-slate-400 mt-2">Current Month Total</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-emerald-500/30 bg-emerald-950/10">
            <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Healthy Food Spend</div>
            <div class="text-2xl font-extrabold text-emerald-400 mt-1">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['healthy_spent'], 2) }}
            </div>
            <p class="text-xs text-emerald-400 font-bold mt-2">{{ $analytics['healthy_percent'] }}% of food budget</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-rose-500/30 bg-rose-950/10">
            <div class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Junk / Fast Food Spend</div>
            <div class="text-2xl font-extrabold text-rose-400 mt-1">
                {{ auth()->user()->currency_symbol }}{{ number_format($analytics['junk_spent'], 2) }}
            </div>
            <p class="text-xs text-rose-400 font-bold mt-2">{{ $analytics['junk_percent'] }}% of food budget</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-cyan-500/30">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lifestyle Health Score</div>
            <div class="text-2xl font-extrabold text-cyan-400 mt-1">
                {{ $analytics['healthy_percent'] >= 60 ? 'A (Healthy)' : ($analytics['healthy_percent'] >= 40 ? 'B (Moderate)' : 'C (Needs Work)') }}
            </div>
            <p class="text-xs text-slate-400 mt-2">Based on food ratios</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subcategory Breakdown -->
        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-400"></i>
                <span>Subcategory Spend Breakdown</span>
            </h3>
            <div class="space-y-3">
                @foreach($analytics['subcategory_breakdown'] as $subName => $amount)
                <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-200">{{ $subName }}</span>
                    <span class="text-sm font-extrabold text-emerald-400">{{ auth()->user()->currency_symbol }}{{ number_format($amount, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Weekly Food Analysis -->
        <div class="glass-card p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-cyan-400"></i>
                <span>Weekly Food Progression</span>
            </h3>
            <div class="h-64">
                <canvas id="weeklyFoodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Food Expenses Table -->
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-800">
            <h3 class="text-base font-bold text-white">Food Transaction Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Subcategory</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Classification</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($foodExpenses as $exp)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4 font-bold text-white">{{ $exp->title }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $exp->foodSubcategory ? $exp->foodSubcategory->name : 'General Food' }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $exp->is_healthy ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                                {{ $exp->is_healthy ? '🥗 Healthy' : '🍔 Fast Food' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-extrabold text-white text-sm">
                            {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No food expenses recorded for this month.</td>
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
                    borderRadius: 10
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
