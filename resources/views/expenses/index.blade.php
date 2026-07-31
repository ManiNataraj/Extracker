@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ createCategoryModal: false, filterOpen: false, editModalOpen: false, currentExpense: {} }">
    <!-- Header Title & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i data-lucide="receipt" class="w-7 h-7 text-cyan-400"></i>
                <span>Expense History & Transactions</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage, filter, search, and track all your recorded expenses.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="createCategoryModal = true" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center space-x-2">
                <i data-lucide="folder-plus" class="w-4 h-4 text-purple-400"></i>
                <span>New Category</span>
            </button>
            <button @click="quickModalOpen = true" class="px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Expense</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="glass-card p-5 rounded-3xl border border-slate-800 space-y-4">
        <form action="{{ route('expenses.index') }}" method="GET" class="space-y-4">
            <!-- Date Preset Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 mr-2">Quick Presets:</span>
                <a href="{{ route('expenses.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ !request()->filled('date_preset') ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">All Time</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'today']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('date_preset') === 'today' ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">Today</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'yesterday']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('date_preset') === 'yesterday' ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">Yesterday</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'last_7_days']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('date_preset') === 'last_7_days' ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">Last 7 Days</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'this_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('date_preset') === 'this_month' ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">This Month</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'last_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('date_preset') === 'last_month' ? 'bg-cyan-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">Last Month</a>
            </div>

            <!-- Search Inputs & Dropdowns Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <!-- Search Box -->
                <div class="relative col-span-1 sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, notes, location, amount..." class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 focus:border-cyan-500 focus:outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                </div>

                <!-- Category Dropdown -->
                <div>
                    <select name="category_id" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:border-cyan-500 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tag Dropdown -->
                <div>
                    <select name="tag_id" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:border-cyan-500 focus:outline-none">
                        <option value="">All Tags</option>
                        @foreach($tags as $t)
                        <option value="{{ $t->id }}" {{ request('tag_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Submit & Clear Row -->
            <div class="flex items-center justify-between border-t border-slate-800/80 pt-3">
                <div class="flex items-center space-x-3">
                    <button type="submit" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs rounded-xl shadow transition">Apply Filters</button>
                    <a href="{{ route('expenses.index') }}" class="text-xs text-slate-400 hover:text-white">Reset Filters</a>
                </div>
                <div class="text-xs text-slate-400">
                    Showing <span class="text-cyan-400 font-bold">{{ $expenses->total() }}</span> expenses
                </div>
            </div>
        </form>
    </div>

    <!-- Expenses Table Card -->
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Title & Details</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4">Tags & Mood</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($expenses as $exp)
                    <tr class="hover:bg-slate-800/40 transition">
                        <!-- Title & Details -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-white text-sm flex items-center gap-2">
                                <span>{{ $exp->title }}</span>
                                @if($exp->attachment_path)
                                <a href="{{ asset('storage/' . $exp->attachment_path) }}" target="_blank" title="View Attachment Receipt" class="text-cyan-400 hover:text-cyan-300">
                                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                                </a>
                                @endif
                            </div>
                            @if($exp->notes || $exp->location)
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                @if($exp->location)<span class="text-slate-300">📍 {{ $exp->location }}</span> • @endif
                                {{ Str::limit($exp->notes, 40) }}
                            </div>
                            @endif
                        </td>

                        <!-- Category -->
                        <td class="px-6 py-4">
                            @if($exp->category)
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold inline-flex items-center gap-1.5" style="background-color: {{ $exp->category->color }}20; color: {{ $exp->category->color }}; border: 1px solid {{ $exp->category->color }}40;">
                                <span>{{ $exp->category->name }}</span>
                            </span>
                            @else
                            <span class="text-slate-500">Uncategorized</span>
                            @endif
                        </td>

                        <!-- Date & Time -->
                        <td class="px-6 py-4 text-slate-300 font-medium">
                            <div>{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</div>
                            @if($exp->time)<div class="text-[10px] text-slate-500">{{ $exp->time }}</div>@endif
                        </td>

                        <!-- Payment Method -->
                        <td class="px-6 py-4 text-slate-300 font-medium">
                            <span class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-[11px]">{{ $exp->payment_method }}</span>
                        </td>

                        <!-- Tags & Mood -->
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($exp->tags as $t)
                                <span class="px-2 py-0.5 rounded text-[10px] bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">#{{ $t->name }}</span>
                                @endforeach
                                @if($exp->mood)
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-cyan-300 border border-slate-700">{{ $exp->mood }}</span>
                                @endif
                                @if($exp->is_healthy !== null)
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $exp->is_healthy ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ $exp->is_healthy ? '🥗 Healthy' : '🍔 Fast Food' }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="px-6 py-4 text-right font-extrabold text-white text-base">
                            {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-slate-600"></i>
                            <p class="text-sm font-semibold">No expenses found matching your query.</p>
                            <p class="text-xs text-slate-600 mt-1">Try resetting filters or adding a new expense.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

    <!-- Create Custom Category Modal -->
    <div x-show="createCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card rounded-2xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="createCategoryModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                    <i data-lucide="folder-plus" class="w-5 h-5 text-purple-400"></i>
                    <span>Create Custom Category</span>
                </h3>
                <button @click="createCategoryModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Subscriptions, Pet Care, Gaming" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Color Picker</label>
                        <input type="color" name="color" value="#6366f1" class="w-full h-10 bg-slate-900 border border-slate-700 rounded-xl cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Icon Identifier</label>
                        <input type="text" name="icon" value="tag" placeholder="tag, coffee, car" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_food" id="is_food" class="rounded bg-slate-900 border-slate-700 text-cyan-500">
                    <label for="is_food" class="text-xs text-slate-300">Classify as Food-related Category</label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="createCategoryModal = false" class="px-4 py-2 text-sm text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-purple-500/20">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
