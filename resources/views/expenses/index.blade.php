@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ createCategoryModal: false, filterOpen: false, editModalOpen: false, currentExpense: {} }">
    <!-- Header Title & Actions -->
    <div class="clay-card p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 clay-badge">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <span>Expense History & Transactions</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Manage, filter, search, and track all your recorded expenses.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="createCategoryModal = true" class="px-4 py-2.5 clay-btn text-xs font-extrabold flex items-center space-x-2">
                <i data-lucide="folder-plus" class="w-4 h-4 text-purple-500"></i>
                <span>New Category</span>
            </button>
            <button @click="quickModalOpen = true" class="px-5 py-2.5 clay-btn-primary text-xs font-extrabold flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Expense</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="clay-card p-6 space-y-5">
        <form action="{{ route('expenses.index') }}" method="GET" class="space-y-4">
            <!-- Date Preset Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mr-2">Quick Presets:</span>
                <a href="{{ route('expenses.index') }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ !request()->filled('date_preset') ? 'clay-btn-primary text-white' : 'clay-btn' }}">All Time</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'today']) }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ request('date_preset') === 'today' ? 'clay-btn-primary text-white' : 'clay-btn' }}">Today</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'yesterday']) }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ request('date_preset') === 'yesterday' ? 'clay-btn-primary text-white' : 'clay-btn' }}">Yesterday</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'last_7_days']) }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ request('date_preset') === 'last_7_days' ? 'clay-btn-primary text-white' : 'clay-btn' }}">Last 7 Days</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'this_month']) }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ request('date_preset') === 'this_month' ? 'clay-btn-primary text-white' : 'clay-btn' }}">This Month</a>
                <a href="{{ route('expenses.index', ['date_preset' => 'last_month']) }}" class="px-3.5 py-1.5 rounded-2xl text-xs font-extrabold transition {{ request('date_preset') === 'last_month' ? 'clay-btn-primary text-white' : 'clay-btn' }}">Last Month</a>
            </div>

            <!-- Search Inputs & Dropdowns Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Search Box -->
                <div class="relative col-span-1 sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, notes, location, amount..." class="w-full clay-inset py-2.5 pl-10 pr-4 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                </div>

                <!-- Category Dropdown -->
                <div>
                    <select name="category_id" class="w-full clay-inset py-2.5 px-4 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tag Dropdown -->
                <div>
                    <select name="tag_id" class="w-full clay-inset py-2.5 px-4 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                        <option value="">All Tags</option>
                        @foreach($tags as $t)
                        <option value="{{ $t->id }}" {{ request('tag_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Submit & Clear Row -->
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800/80 pt-4">
                <div class="flex items-center space-x-3">
                    <button type="submit" class="px-5 py-2.5 clay-btn-cyan text-white font-extrabold text-xs">Apply Filters</button>
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2.5 clay-btn text-xs font-extrabold">Reset Filters</a>
                </div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    Showing <span class="text-indigo-500 font-extrabold">{{ $expenses->total() }}</span> expenses
                </div>
            </div>
        </form>
    </div>

    <!-- Expenses List Card -->
    <div class="clay-card overflow-hidden">
        <div class="divide-y divide-slate-200 dark:divide-slate-800/80">
            @forelse($expenses as $exp)
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
            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold clay-badge shrink-0" style="background-color: {{ $exp->category->color ?? '#6366f1' }};">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                            <span>{{ $exp->title }}</span>
                            @if($exp->attachment_path)
                            <a href="{{ asset('storage/' . $exp->attachment_path) }}" target="_blank" title="View Attachment Receipt" class="text-indigo-500 hover:text-indigo-400">
                                <i data-lucide="paperclip" class="w-4 h-4"></i>
                            </a>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-bold" style="color: {{ $exp->category->color ?? '#6366f1' }};">{{ $exp->category->name ?? 'Uncategorized' }}</span>
                            <span>•</span>
                            <span>{{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}</span>
                            @if($exp->payment_method)
                            <span>•</span>
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] bg-slate-200 dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300">{{ $exp->payment_method }}</span>
                            @endif
                        </div>
                        @if($exp->notes || $exp->location)
                        <p class="text-xs text-slate-400 italic">
                            @if($exp->location)📍 {{ $exp->location }} — @endif {{ Str::limit($exp->notes, 60) }}
                        </p>
                        @endif
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($exp->tags as $t)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">#{{ $t->name }}</span>
                            @endforeach
                            @if($exp->mood)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-cyan-400">{{ $exp->mood }}</span>
                            @endif
                            @if($exp->is_healthy !== null)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $exp->is_healthy ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                {{ $exp->is_healthy ? '🥗 Healthy' : '🍔 Fast Food' }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end gap-4">
                    <span class="text-lg font-black text-slate-900 dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($exp->amount, 2) }}
                    </span>
                    <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Delete this expense entry?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 transition" title="Delete">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-slate-400">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-slate-500"></i>
                <p class="text-sm font-extrabold text-slate-300">No expenses found matching your filter query.</p>
            </div>
            @endforelse
        </div>

        @if($expenses->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

    <!-- Create Custom Category Modal -->
    <div x-show="createCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="clay-card rounded-3xl max-w-md w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="createCategoryModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                <h3 class="text-lg font-extrabold text-white flex items-center space-x-2">
                    <i data-lucide="folder-plus" class="w-5 h-5 text-purple-400"></i>
                    <span>Create Custom Category</span>
                </h3>
                <button @click="createCategoryModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Subscriptions, Pet Care, Gaming" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Color Picker</label>
                        <input type="color" name="color" value="#6366f1" class="w-full h-10 bg-slate-900 border border-slate-700 rounded-xl cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Icon Identifier</label>
                        <input type="text" name="icon" value="tag" placeholder="tag, coffee, car, shirt" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_food" id="is_food" class="rounded bg-slate-900 border-slate-700 text-indigo-500">
                    <label for="is_food" class="text-xs text-slate-300">Classify as Food-related Category</label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700/60">
                    <button type="button" @click="createCategoryModal = false" class="px-5 py-2.5 clay-btn text-xs font-extrabold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
