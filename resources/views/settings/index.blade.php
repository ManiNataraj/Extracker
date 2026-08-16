@extends('layouts.app')

@section('content')
<div class="max-w-4xl space-y-8">
    <!-- Header Title -->
    <div class="clay-card p-6 md:p-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 clay-badge">
                    <i data-lucide="settings" class="w-6 h-6"></i>
                </div>
                <span>User Profile & Application Settings</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Configure user profile, default currency, dark mode, and budget preferences.</p>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="clay-card p-6 md:p-8 space-y-6">
        <h3 class="text-base font-extrabold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800">Account Details</h3>

        <form action="{{ route('settings.profile') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Symbol *</label>
                    <select name="currency_symbol" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                        <option value="₹" {{ $user->currency_symbol === '₹' ? 'selected' : '' }}>₹ (INR)</option>
                        <option value="$" {{ $user->currency_symbol === '$' ? 'selected' : '' }}>$ (USD)</option>
                        <option value="€" {{ $user->currency_symbol === '€' ? 'selected' : '' }}>€ (EUR)</option>
                        <option value="£" {{ $user->currency_symbol === '£' ? 'selected' : '' }}>£ (GBP)</option>
                        <option value="AED" {{ $user->currency_symbol === 'AED' ? 'selected' : '' }}>AED (AED)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Monthly Budget Limit ({{ $user->currency_symbol }})</label>
                    <input type="number" step="1" name="monthly_budget_limit" value="{{ old('monthly_budget_limit', $user->monthly_budget_limit) }}" placeholder="60000" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
