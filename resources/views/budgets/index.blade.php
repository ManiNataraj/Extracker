@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="{ addBudgetModal: false }">
    <!-- Header Title & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i data-lucide="target" class="w-7 h-7 text-emerald-400"></i>
                <span>Budget Targets & Limits</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Set monthly and category spending limits with overspending warnings.</p>
        </div>
        <button @click="addBudgetModal = true" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Set Budget Goal</span>
        </button>
    </div>

    <!-- Global Monthly Limit Banner -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center md:text-left">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Overall Monthly Allowance</span>
            <div class="text-3xl font-extrabold text-white">
                {{ auth()->user()->currency_symbol }}{{ number_format($overview['global_limit'], 2) }}
            </div>
            <p class="text-xs text-slate-400">Total Spent This Month: <span class="text-cyan-400 font-bold">{{ auth()->user()->currency_symbol }}{{ number_format($overview['total_spent'], 2) }}</span></p>
        </div>

        <div class="w-full md:w-72 space-y-2">
            <div class="flex justify-between text-xs font-bold">
                <span class="text-slate-300">Budget Utilized</span>
                <span class="{{ $overview['global_used_percent'] > 90 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $overview['global_used_percent'] }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full {{ $overview['global_used_percent'] > 90 ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-400 to-cyan-500' }}" style="width: {{ min(100, $overview['global_used_percent']) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Category Budgets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($overview['items'] as $b)
        <div class="glass-card p-6 rounded-3xl border {{ $b['is_overspent'] ? 'border-rose-500/50 bg-rose-950/10' : 'border-slate-800' }} relative">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-md" style="background-color: {{ $b['category_color'] }};">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">{{ $b['category_name'] }}</h3>
                        <p class="text-xs text-slate-400 uppercase font-semibold">{{ $b['type'] }} Limit</p>
                    </div>
                </div>

                <form action="{{ route('budgets.destroy', $b['id']) }}" method="POST" onsubmit="return confirm('Remove this budget goal?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Spend & Limit -->
            <div class="mt-4 flex justify-between items-baseline">
                <span class="text-xl font-extrabold text-white">{{ auth()->user()->currency_symbol }}{{ number_format($b['spent'], 2) }}</span>
                <span class="text-xs text-slate-400">of {{ auth()->user()->currency_symbol }}{{ number_format($b['amount'], 2) }}</span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-800 rounded-full h-2 mt-3 overflow-hidden">
                <div class="h-2 rounded-full {{ $b['is_overspent'] ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $b['used_percent']) }}%"></div>
            </div>

            <div class="mt-3 flex justify-between text-xs">
                <span class="text-slate-400">Remaining: <strong class="{{ $b['is_overspent'] ? 'text-rose-400' : 'text-emerald-400' }}">{{ auth()->user()->currency_symbol }}{{ number_format($b['remaining'], 2) }}</strong></span>
                <span class="font-bold text-slate-300">{{ $b['used_percent'] }}%</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Set Budget Modal -->
    <div x-show="addBudgetModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card rounded-2xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="addBudgetModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                    <i data-lucide="target" class="w-5 h-5 text-emerald-400"></i>
                    <span>Set New Budget Limit</span>
                </h3>
                <button @click="addBudgetModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Category</label>
                    <select name="category_id" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                        <option value="">Global Monthly Overall Limit</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Budget Type</label>
                        <select name="type" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="category">Category Specific</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount Limit ({{ auth()->user()->currency_symbol }}) *</label>
                        <input type="number" step="1" name="amount" required placeholder="5000" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="addBudgetModal = false" class="px-4 py-2 text-sm text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20">Save Budget Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
