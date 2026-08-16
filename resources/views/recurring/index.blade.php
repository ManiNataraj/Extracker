@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ addRecurringModal: false }">
    <!-- Header Title & Action -->
    <div class="clay-card p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-cyan-500/10 text-cyan-500 dark:text-cyan-400 clay-badge">
                    <i data-lucide="repeat" class="w-6 h-6"></i>
                </div>
                <span>Recurring Subscriptions & Bills</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Automate recurring expenses like rent, subscriptions, utility bills, and EMI payments.</p>
        </div>
        <button @click="addRecurringModal = true" class="px-5 py-2.5 clay-btn-primary text-xs font-extrabold flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Recurring Entry</span>
        </button>
    </div>

    <!-- Recurring Items List Card -->
    <div class="clay-card overflow-hidden">
        <div class="divide-y divide-slate-200 dark:divide-slate-800/80">
            @forelse($recurringExpenses as $item)
            @php
                $catName = strtolower($item->category->name ?? '');
                $icon = $item->category->icon ?? 'tag';
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
            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold clay-badge shrink-0" style="background-color: {{ $item->category->color ?? '#6366f1' }};">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $item->title }}</div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                            <span class="font-bold" style="color: {{ $item->category->color ?? '#6366f1' }};">{{ $item->category->name ?? 'Uncategorized' }}</span>
                            <span>•</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 font-extrabold uppercase">{{ $item->frequency }}</span>
                            <span>•</span>
                            <span>Starts {{ \Carbon\Carbon::parse($item->start_date)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end gap-4">
                    <form action="{{ route('recurring.toggle', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl text-[10px] font-black transition {{ $item->is_active ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                            {{ $item->is_active ? '● ACTIVE' : 'PAUSED' }}
                        </button>
                    </form>
                    <span class="text-lg font-black text-slate-900 dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($item->amount, 2) }}
                    </span>
                    <form action="{{ route('recurring.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete recurring expense?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-slate-400 text-xs">No recurring expenses set up yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Create Recurring Modal -->
    <div x-show="addRecurringModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="clay-card rounded-3xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="addRecurringModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                <h3 class="text-lg font-extrabold text-white flex items-center space-x-2">
                    <i data-lucide="repeat" class="w-5 h-5 text-cyan-400"></i>
                    <span>Create Recurring Subscription</span>
                </h3>
                <button @click="addRecurringModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('recurring.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Netflix, Wifi Bill, Gym Membership" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount ({{ auth()->user()->currency_symbol }}) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Frequency *</label>
                        <select name="frequency" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category</label>
                        <select name="category_id" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                        <option value="Net Banking">Net Banking</option>
                        <option value="UPI">UPI</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="Auto Debit">Auto Debit</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700/60">
                    <button type="button" @click="addRecurringModal = false" class="px-5 py-2.5 clay-btn text-xs font-extrabold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">Save Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
