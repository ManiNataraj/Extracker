@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div class="clay-card p-6 md:p-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 clay-badge">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <span>Financial Reports & Export Hub</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Generate comprehensive daily, weekly, monthly, and custom period reports.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="clay-card p-6 md:p-8 space-y-6">
        <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Report Period</label>
                    <select name="period" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                        <option value="monthly" {{ request('period') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                        <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Daily Report</option>
                        <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                        <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Yearly Report</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date (Custom Range)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date (Custom Range)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-800/80">
                <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">
                    Generate Report
                </button>

                <!-- Export Action Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-5 py-2.5 clay-btn text-xs font-extrabold flex items-center space-x-2">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                        <span>Export CSV / Excel</span>
                    </a>
                    <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'print'])) }}" target="_blank" class="px-5 py-2.5 clay-btn text-xs font-extrabold flex items-center space-x-2">
                        <i data-lucide="printer" class="w-4 h-4 text-indigo-500"></i>
                        <span>Print Preview</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Generated Report View -->
    <div class="clay-card p-6 md:p-8 space-y-6">
        <!-- Summary Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $reportData['title'] }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Total Records: <strong class="text-slate-900 dark:text-white">{{ $reportData['total_count'] }}</strong></p>
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Amount</span>
                <div class="text-2xl font-black text-indigo-500 dark:text-indigo-400">
                    {{ auth()->user()->currency_symbol }}{{ number_format($reportData['total_amount'], 2) }}
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="divide-y divide-slate-200 dark:divide-slate-800/80">
            @forelse($reportData['expenses'] as $exp)
            <div class="py-3.5 flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $exp->title }}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        <span>{{ $exp->category ? $exp->category->name : 'Uncategorized' }}</span>
                        <span>•</span>
                        <span>{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-slate-900 dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                    </span>
                    <div class="text-[10px] text-slate-400 font-semibold">{{ $exp->payment_method }}</div>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-slate-400 text-xs font-medium">No records found for specified report criteria.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
