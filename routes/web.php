<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FoodIntelligenceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\RecurringExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Application Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Expense Module
    Route::resource('expenses', ExpenseController::class);

    // Dynamic Categories
    Route::resource('categories', CategoryController::class);

    // Food Intelligence
    Route::get('/food-intelligence', [FoodIntelligenceController::class, 'index'])->name('food.index');

    // Calendar View
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/details/{date}', [CalendarController::class, 'dateDetails'])->name('calendar.details');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Budgets
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'destroy']);

    // Recurring Expenses
    Route::resource('recurring', RecurringExpenseController::class)->only(['index', 'store', 'destroy']);
    Route::post('/recurring/{recurringExpense}/toggle', [RecurringExpenseController::class, 'toggle'])->name('recurring.toggle');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Settings & Dark Mode
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/dark-mode', [SettingController::class, 'toggleDarkMode'])->name('settings.dark-mode');
});
