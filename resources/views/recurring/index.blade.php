@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ addRecurringModal: false }">
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i data-lucide="repeat" class="w-7 h-7 text-cyan-400"></i>
                <span>Recurring Subscriptions & Bills</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Automate recurring expenses like rent, subscriptions, utility bills, and EMI payments.</p>
        </div>
        <button @click="addRecurringModal = true" class="px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Recurring Entry</span>
        </button>
    </div>

    <!-- Recurring Items Table Card -->
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Title & Details</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Frequency</th>
                        <th class="px-6 py-4">Start Date</th>
                        <th class="px-6 py-4">Last Processed</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($recurringExpenses as $item)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-white text-sm">{{ $item->title }}</div>
                            <div class="text-[11px] text-slate-400">{{ $item->payment_method }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->category)
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold" style="background-color: {{ $item->category->color }}20; color: {{ $item->category->color }}; border: 1px solid {{ $item->category->color }}40;">
                                {{ $item->category->name }}
                            </span>
                            @else
                            <span class="text-slate-500">Uncategorized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-cyan-400 font-bold uppercase text-[10px]">
                                {{ $item->frequency }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-medium">{{ \Carbon\Carbon::parse($item->start_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $item->last_processed_at ? \Carbon\Carbon::parse($item->last_processed_at)->format('M d, Y H:i') : 'Pending' }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('recurring.toggle', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-xl text-[10px] font-extrabold transition {{ $item->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $item->is_active ? 'ACTIVE' : 'PAUSED' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right font-extrabold text-white text-base">
                            {{ auth()->user()->currency_symbol }}{{ number_format($item->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('recurring.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete recurring expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500">No recurring expenses setup yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Recurring Modal -->
    <div x-show="addRecurringModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card rounded-2xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="addRecurringModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                    <i data-lucide="repeat" class="w-5 h-5 text-cyan-400"></i>
                    <span>Create Recurring Subscription</span>
                </h3>
                <button @click="addRecurringModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('recurring.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Netflix, Wifi Bill, Gym Membership" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount ({{ auth()->user()->currency_symbol }}) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Frequency *</label>
                        <select name="frequency" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
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
                        <select name="category_id" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                        <option value="Net Banking">Net Banking</option>
                        <option value="UPI">UPI</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="Auto Debit">Auto Debit</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="addRecurringModal = false" class="px-4 py-2 text-sm text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-cyan-500/20">Save Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
