@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
            <i data-lucide="file-text" class="w-7 h-7 text-cyan-400"></i>
            <span>Financial Reports & Export Hub</span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Generate comprehensive daily, weekly, monthly, and custom period reports.</p>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800">
        <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Report Period</label>
                    <select name="period" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                        <option value="monthly" {{ request('period') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                        <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Daily Report</option>
                        <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                        <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Yearly Report</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date (Custom Range)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date (Custom Range)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-800">
                <button type="submit" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs rounded-xl shadow transition">
                    Generate Report
                </button>

                <!-- Export Action Buttons -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center space-x-1.5">
                        <i data-lucide="file-spread-sheet" class="w-4 h-4 text-emerald-400"></i>
                        <span>Export CSV / Excel</span>
                    </a>
                    <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'print'])) }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center space-x-1.5">
                        <i data-lucide="printer" class="w-4 h-4 text-cyan-400"></i>
                        <span>Print Preview</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Generated Report View -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800 space-y-6">
        <!-- Summary Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-slate-800">
            <div>
                <h2 class="text-xl font-extrabold text-white">{{ $reportData['title'] }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">Total Records: <strong class="text-white">{{ $reportData['total_count'] }}</strong></p>
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <span class="text-xs font-semibold text-slate-400 uppercase">Total Amount</span>
                <div class="text-2xl font-extrabold text-cyan-400">
                    {{ auth()->user()->currency_symbol }}{{ number_format($reportData['total_amount'], 2) }}
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Payment</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($reportData['expenses'] as $exp)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3 font-bold text-white">{{ $exp->title }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $exp->category ? $exp->category->name : 'Uncategorized' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $exp->payment_method }}</td>
                        <td class="px-4 py-3 text-right font-bold text-white">
                            {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No records found for specified report criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
