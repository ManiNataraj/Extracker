@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ createCategoryModal: false }">
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i data-lucide="grid" class="w-7 h-7 text-purple-400"></i>
                <span>Category Management & Statistics</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage custom spending categories with full financial breakdown.</p>
        </div>
        <button @click="createCategoryModal = true" class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-purple-500/20 transition flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Custom Category</span>
        </button>
    </div>

    <!-- Category Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $cat)
        @php
            $stats = $categoryStats[$cat->id] ?? ['total_spent' => 0, 'monthly_spent' => 0, 'percentage_of_total' => 0, 'transaction_count' => 0];
        @endphp
        <div class="glass-card p-6 rounded-3xl border border-slate-800 relative group hover:border-slate-700 transition duration-300">
            <!-- Top Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg" style="background-color: {{ $cat->color }};">
                        <i data-lucide="{{ $cat->icon ?? 'tag' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-400 font-medium">{{ $stats['transaction_count'] }} Transactions</p>
                    </div>
                </div>

                @if($cat->user_id)
                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Deleting category will keep existing expenses intact. Proceed?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-500 hover:text-rose-400 rounded-xl hover:bg-slate-800 transition" title="Delete Category">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
                @else
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-semibold border border-slate-700">System</span>
                @endif
            </div>

            <!-- Main Spend Amount -->
            <div class="mt-6">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Spent</div>
                <div class="text-2xl font-extrabold text-white mt-1">
                    {{ auth()->user()->currency_symbol }}{{ number_format($stats['total_spent'], 2) }}
                </div>
            </div>

            <!-- Stats Subgrid -->
            <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t border-slate-800">
                <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase">This Month</div>
                    <div class="text-sm font-bold text-slate-200 mt-0.5">
                        {{ auth()->user()->currency_symbol }}{{ number_format($stats['monthly_spent'], 2) }}
                    </div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase">% of Total</div>
                    <div class="text-sm font-bold text-cyan-400 mt-0.5">
                        {{ $stats['percentage_of_total'] }}%
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="h-1.5 rounded-full" style="width: {{ min(100, $stats['percentage_of_total']) }}%; background-color: {{ $cat->color }};"></div>
            </div>
        </div>
        @endforeach
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
                    <input type="text" name="name" required placeholder="e.g. Subscriptions, Gaming, Books" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Color Picker</label>
                        <input type="color" name="color" value="#8b5cf6" class="w-full h-10 bg-slate-900 border border-slate-700 rounded-xl cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Icon Identifier</label>
                        <input type="text" name="icon" value="tag" placeholder="tag, coffee, car" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_food" id="cat_is_food" class="rounded bg-slate-900 border-slate-700 text-cyan-500">
                    <label for="cat_is_food" class="text-xs text-slate-300">Classify as Food-related Category</label>
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
