@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="{ addBudgetModal: false }">
    <!-- Header Title & Actions -->
    <div class="clay-card p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 clay-badge">
                    <i data-lucide="target" class="w-6 h-6"></i>
                </div>
                <span>Budget Targets & Limits</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Set monthly and category spending limits with overspending warnings.</p>
        </div>
        <button @click="addBudgetModal = true" class="px-5 py-2.5 clay-btn-primary text-xs font-extrabold flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Set Budget Goal</span>
        </button>
    </div>

    <!-- Global Monthly Limit Banner -->
    <div class="clay-card p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center md:text-left">
            <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Overall Monthly Allowance</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white">
                {{ auth()->user()->currency_symbol }}{{ number_format($overview['global_limit'], 2) }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Spent This Month: <span class="text-indigo-500 font-black">{{ auth()->user()->currency_symbol }}{{ number_format($overview['total_spent'], 2) }}</span></p>
        </div>

        <div class="w-full md:w-80 space-y-2">
            <div class="flex justify-between text-xs font-extrabold">
                <span class="text-slate-700 dark:text-slate-300">Budget Utilized</span>
                <span class="{{ $overview['global_used_percent'] > 90 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $overview['global_used_percent'] }}%</span>
            </div>
            <div class="w-full clay-inset h-3 overflow-hidden p-0.5">
                <div class="h-full rounded-full transition-all duration-500 {{ $overview['global_used_percent'] > 90 ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-400 to-indigo-500' }}" style="width: {{ min(100, $overview['global_used_percent']) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Category Budgets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($overview['items'] as $b)
        <div class="clay-card p-6 space-y-4 relative hover:-translate-y-1 transition duration-300 {{ $b['is_overspent'] ? 'border-l-4 border-l-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white clay-badge" style="background-color: {{ $b['category_color'] }};">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $b['category_name'] }}</h3>
                        <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">{{ $b['type'] }} Limit</p>
                    </div>
                </div>

                <form action="{{ route('budgets.destroy', $b['id']) }}" method="POST" onsubmit="return confirm('Remove this budget goal?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Spend & Limit -->
            <div class="flex justify-between items-baseline pt-2">
                <span class="text-xl font-black text-slate-900 dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($b['spent'], 2) }}</span>
                <span class="text-xs text-slate-400 font-medium">of {{ auth()->user()->currency_symbol }}{{ number_format($b['amount'], 2) }}</span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full clay-inset h-2 overflow-hidden p-0.5">
                <div class="h-full rounded-full transition-all duration-500 {{ $b['is_overspent'] ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $b['used_percent']) }}%"></div>
            </div>

            <div class="flex justify-between text-xs pt-1">
                <span class="text-slate-500 dark:text-slate-400 font-medium">Remaining: <strong class="{{ $b['is_overspent'] ? 'text-rose-500' : 'text-emerald-500' }}">{{ auth()->user()->currency_symbol }}{{ number_format($b['remaining'], 2) }}</strong></span>
                <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ $b['used_percent'] }}%</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Set Budget Modal -->
    <div x-show="addBudgetModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="clay-card rounded-3xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="addBudgetModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                <h3 class="text-lg font-extrabold text-white flex items-center space-x-2">
                    <i data-lucide="target" class="w-5 h-5 text-emerald-400"></i>
                    <span>Set New Budget Limit</span>
                </h3>
                <button @click="addBudgetModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Category</label>
                    <select name="category_id" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                        <option value="">Global Monthly Overall Limit</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Budget Type</label>
                        <select name="type" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="category">Category Specific</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount Limit ({{ auth()->user()->currency_symbol }}) *</label>
                        <input type="number" step="1" name="amount" required placeholder="5000" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700/60">
                    <button type="button" @click="addBudgetModal = false" class="px-5 py-2.5 clay-btn text-xs font-extrabold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">Save Budget Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
