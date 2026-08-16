@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ createCategoryModal: false, selectedIcon: 'tag' }">
    <!-- Header Title & Action -->
    <div class="clay-card p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-purple-500/10 text-purple-500 dark:text-purple-400 clay-badge">
                    <i data-lucide="grid" class="w-6 h-6"></i>
                </div>
                <span>Category Management & Statistics</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Manage custom spending categories with dynamic Lucide icons and financial metrics.</p>
        </div>
        <button @click="createCategoryModal = true" class="px-5 py-2.5 clay-btn-primary text-xs font-extrabold flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Custom Category</span>
        </button>
    </div>

    <!-- Category Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $cat)
        @php
            $stats = $categoryStats[$cat->id] ?? ['total_spent' => 0, 'monthly_spent' => 0, 'percentage_of_total' => 0, 'transaction_count' => 0];
            $catName = strtolower($cat->name ?? '');
            $icon = $cat->icon ?? 'tag';
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
        <div class="clay-card p-6 relative space-y-4 hover:-translate-y-1 transition duration-300">
            <!-- Top Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-white clay-badge" style="background-color: {{ $cat->color }};">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-400 font-medium">{{ $stats['transaction_count'] }} Transactions</p>
                    </div>
                </div>

                @if($cat->user_id)
                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Deleting category will keep existing expenses intact. Proceed?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 transition" title="Delete Category">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
                @else
                <span class="text-[10px] px-2.5 py-1 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-500 font-bold border border-slate-300 dark:border-slate-700">System</span>
                @endif
            </div>

            <!-- Main Spend Amount -->
            <div class="pt-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Spent</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ auth()->user()->currency_symbol }}{{ number_format($stats['total_spent'], 2) }}
                </div>
            </div>

            <!-- Stats Subgrid -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-800/80">
                <div class="p-3 rounded-2xl clay-inset">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase">This Month</div>
                    <div class="text-xs font-black text-slate-800 dark:text-slate-200 mt-0.5">
                        {{ auth()->user()->currency_symbol }}{{ number_format($stats['monthly_spent'], 2) }}
                    </div>
                </div>
                <div class="p-3 rounded-2xl clay-inset">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase">% of Total</div>
                    <div class="text-xs font-black text-indigo-500 dark:text-indigo-400 mt-0.5">
                        {{ $stats['percentage_of_total'] }}%
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full clay-inset h-2 overflow-hidden p-0.5">
                <div class="h-full rounded-full transition-all duration-500" style="width: {{ min(100, $stats['percentage_of_total']) }}%; background-color: {{ $cat->color }};"></div>
            </div>
        </div>
        @endforeach
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
                    <input type="text" name="name" required placeholder="e.g. Subscriptions, Gaming, Books" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Color Picker</label>
                        <input type="color" name="color" value="#8b5cf6" class="w-full h-10 bg-slate-900 border border-slate-700 rounded-2xl cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Icon Identifier</label>
                        <input type="text" name="icon" x-model="selectedIcon" placeholder="utensils, coffee, shirt, wifi" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                    </div>
                </div>

                <!-- Icon Quick Select Pills -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Quick Select Icon</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['utensils', 'coffee', 'heart-pulse', 'shirt', 'zap', 'bolt', 'smartphone', 'wifi', 'home', 'plane', 'graduation-cap', 'shopping-cart', 'gamepad-2', 'tv', 'dumbbell', 'car', 'briefcase'] as $ic)
                        <button type="button" @click="selectedIcon = '{{ $ic }}'" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" :class="selectedIcon === '{{ $ic }}' ? 'border border-indigo-500 text-indigo-400' : ''">
                            <i data-lucide="{{ $ic }}" class="w-4 h-4"></i>
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_food" id="cat_is_food" class="rounded bg-slate-900 border-slate-700 text-indigo-500">
                    <label for="cat_is_food" class="text-xs text-slate-300">Classify as Food-related Category</label>
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
