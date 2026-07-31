<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\FoodSubcategory;
use App\Models\Tag;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\RecurringExpense;
use App\Models\AppNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Demo User
        $user = User::create([
            'name' => 'Alex Morgan',
            'email' => 'alex@tracker.com',
            'password' => Hash::make('password'),
            'currency_symbol' => '₹',
            'dark_mode' => true,
            'monthly_budget_limit' => 60000.00,
            'weekly_budget_limit' => 15000.00,
        ]);

        // 2. Categories
        $defaultCategories = [
            ['name' => 'Food', 'icon' => 'utensils', 'color' => '#ef4444', 'is_food' => true],
            ['name' => 'Snacks', 'icon' => 'coffee', 'color' => '#f97316', 'is_food' => true],
            ['name' => 'Medical', 'icon' => 'activity', 'color' => '#ec4899', 'is_food' => false],
            ['name' => 'Clothes', 'icon' => 'shopping-bag', 'color' => '#a855f7', 'is_food' => false],
            ['name' => 'Fuel', 'icon' => 'zap', 'color' => '#eab308', 'is_food' => false],
            ['name' => 'Electricity', 'icon' => 'bolt', 'color' => '#14b8a6', 'is_food' => false],
            ['name' => 'Mobile Recharge', 'icon' => 'smartphone', 'color' => '#06b6d4', 'is_food' => false],
            ['name' => 'Internet', 'icon' => 'wifi', 'color' => '#3b82f6', 'is_food' => false],
            ['name' => 'Rent', 'icon' => 'home', 'color' => '#6366f1', 'is_food' => false],
            ['name' => 'House', 'icon' => 'box', 'color' => '#8b5cf6', 'is_food' => false],
            ['name' => 'Family', 'icon' => 'heart', 'color' => '#f43f5e', 'is_food' => false],
            ['name' => 'Travel', 'icon' => 'map-pin', 'color' => '#10b981', 'is_food' => false],
            ['name' => 'Education', 'icon' => 'book-open', 'color' => '#0284c7', 'is_food' => false],
            ['name' => 'Shopping', 'icon' => 'shopping-cart', 'color' => '#d97706', 'is_food' => false],
            ['name' => 'Entertainment', 'icon' => 'film', 'color' => '#c026d3', 'is_food' => false],
            ['name' => 'Subscription', 'icon' => 'tv', 'color' => '#475569', 'is_food' => false],
        ];

        $createdCategories = [];
        foreach ($defaultCategories as $cat) {
            $createdCategories[$cat['name']] = Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'is_food' => $cat['is_food'],
            ]);
        }

        // 3. Food Subcategories
        $foodSubcats = [
            ['name' => 'Breakfast', 'is_healthy' => true],
            ['name' => 'Lunch', 'is_healthy' => true],
            ['name' => 'Dinner', 'is_healthy' => true],
            ['name' => 'Snacks', 'is_healthy' => false],
            ['name' => 'Tea', 'is_healthy' => true],
            ['name' => 'Coffee', 'is_healthy' => true],
            ['name' => 'Juice', 'is_healthy' => true],
            ['name' => 'Fruits', 'is_healthy' => true],
            ['name' => 'Fast Food', 'is_healthy' => false],
            ['name' => 'Healthy Food', 'is_healthy' => true],
        ];

        $createdSubcats = [];
        foreach ($foodSubcats as $sub) {
            $createdSubcats[$sub['name']] = FoodSubcategory::create([
                'user_id' => $user->id,
                'name' => $sub['name'],
                'is_healthy' => $sub['is_healthy'],
            ]);
        }

        // 4. Tags
        $tagNames = ['Office', 'Home', 'Friends', 'Family', 'Travel', 'Vacation', 'Emergency', 'Festival'];
        $createdTags = [];
        foreach ($tagNames as $tn) {
            $createdTags[$tn] = Tag::create([
                'user_id' => $user->id,
                'name' => $tn,
                'color' => '#' . substr(md5($tn), 0, 6),
            ]);
        }

        // 5. Seed realistic historical & recent expenses
        $expensesData = [
            // Today / Recent
            ['title' => 'Organic Groceries & Fruits', 'amount' => 1450.00, 'category' => 'Food', 'subcat' => 'Healthy Food', 'days_ago' => 0, 'payment' => 'UPI', 'healthy' => true, 'mood' => 'Happy', 'tags' => ['Home']],
            ['title' => 'Evening Tea & Samosa Snacks', 'amount' => 180.00, 'category' => 'Snacks', 'subcat' => 'Snacks', 'days_ago' => 0, 'payment' => 'Cash', 'healthy' => false, 'mood' => 'Neutral', 'tags' => ['Office']],
            ['title' => 'Quarterly Health Checkup', 'amount' => 3200.00, 'category' => 'Medical', 'subcat' => null, 'days_ago' => 1, 'payment' => 'Credit Card', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Emergency']],
            ['title' => 'Cheeseburger & Fries Combo', 'amount' => 650.00, 'category' => 'Food', 'subcat' => 'Fast Food', 'days_ago' => 2, 'payment' => 'UPI', 'healthy' => false, 'mood' => 'Impulsive', 'tags' => ['Friends']],
            ['title' => 'Car Fuel Refill', 'amount' => 2800.00, 'category' => 'Fuel', 'subcat' => null, 'days_ago' => 3, 'payment' => 'Credit Card', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Office']],
            ['title' => 'Monthly Apartment Rent', 'amount' => 22000.00, 'category' => 'Rent', 'subcat' => null, 'days_ago' => 4, 'payment' => 'Net Banking', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Home']],
            ['title' => 'High-Speed Broadband Bill', 'amount' => 1199.00, 'category' => 'Internet', 'subcat' => null, 'days_ago' => 5, 'payment' => 'UPI', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Home']],
            ['title' => 'Weekend Dinner at Italian Bistro', 'amount' => 2450.00, 'category' => 'Food', 'subcat' => 'Dinner', 'days_ago' => 5, 'payment' => 'Credit Card', 'healthy' => true, 'mood' => 'Happy', 'tags' => ['Friends', 'Family']],
            ['title' => 'Electricity Utility Bill', 'amount' => 1850.00, 'category' => 'Electricity', 'subcat' => null, 'days_ago' => 7, 'payment' => 'Net Banking', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Home']],
            ['title' => 'Netflix & Spotify Subscriptions', 'amount' => 849.00, 'category' => 'Subscription', 'subcat' => null, 'days_ago' => 10, 'payment' => 'Credit Card', 'healthy' => null, 'mood' => 'Happy', 'tags' => ['Home']],
            ['title' => 'Designer Apparel Shopping', 'amount' => 4500.00, 'category' => 'Clothes', 'subcat' => null, 'days_ago' => 12, 'payment' => 'Credit Card', 'healthy' => null, 'mood' => 'Happy', 'tags' => ['Vacation']],
            ['title' => 'Weekend Road Trip Fuel & Toll', 'amount' => 3100.00, 'category' => 'Travel', 'subcat' => null, 'days_ago' => 14, 'payment' => 'UPI', 'healthy' => null, 'mood' => 'Happy', 'tags' => ['Travel', 'Vacation']],
            ['title' => 'Cold Brew Coffee & Pastry', 'amount' => 340.00, 'category' => 'Snacks', 'subcat' => 'Coffee', 'days_ago' => 15, 'payment' => 'UPI', 'healthy' => false, 'mood' => 'Stressed', 'tags' => ['Office']],
            ['title' => 'Protein Shake & Fresh Juices', 'amount' => 420.00, 'category' => 'Food', 'subcat' => 'Juice', 'days_ago' => 18, 'payment' => 'UPI', 'healthy' => true, 'mood' => 'Happy', 'tags' => ['Office']],
            ['title' => 'Movie Tickets & Popcorn', 'amount' => 950.00, 'category' => 'Entertainment', 'subcat' => null, 'days_ago' => 20, 'payment' => 'Debit Card', 'healthy' => false, 'mood' => 'Happy', 'tags' => ['Friends']],

            // 1-3 Months Ago
            ['title' => 'Supermarket Monthly Groceries', 'amount' => 8400.00, 'category' => 'Food', 'subcat' => 'Healthy Food', 'days_ago' => 35, 'payment' => 'Credit Card', 'healthy' => true, 'mood' => 'Neutral', 'tags' => ['Home']],
            ['title' => 'Family Dinner Party', 'amount' => 3800.00, 'category' => 'Food', 'subcat' => 'Dinner', 'days_ago' => 42, 'payment' => 'Credit Card', 'healthy' => true, 'mood' => 'Happy', 'tags' => ['Family']],
            ['title' => 'New Smartphone Cover & Screen Guard', 'amount' => 890.00, 'category' => 'Shopping', 'subcat' => null, 'days_ago' => 50, 'payment' => 'UPI', 'healthy' => null, 'mood' => 'Happy', 'tags' => ['Office']],
            ['title' => 'Dentist Consultation & Cleaning', 'amount' => 1500.00, 'category' => 'Medical', 'subcat' => null, 'days_ago' => 65, 'payment' => 'Cash', 'healthy' => null, 'mood' => 'Neutral', 'tags' => ['Emergency']],
            ['title' => 'Festival Sweets & Gifts', 'amount' => 2700.00, 'category' => 'Family', 'subcat' => null, 'days_ago' => 80, 'payment' => 'UPI', 'healthy' => false, 'mood' => 'Happy', 'tags' => ['Festival']],
            ['title' => 'Online Certification Course', 'amount' => 5999.00, 'category' => 'Education', 'subcat' => null, 'days_ago' => 95, 'payment' => 'Net Banking', 'healthy' => null, 'mood' => 'Happy', 'tags' => ['Office']],
        ];

        foreach ($expensesData as $exp) {
            $cat = $createdCategories[$exp['category']] ?? null;
            $subcat = $exp['subcat'] ? ($createdSubcats[$exp['subcat']] ?? null) : null;
            $date = Carbon::now()->subDays($exp['days_ago'])->format('Y-m-d');

            $expenseModel = Expense::create([
                'user_id' => $user->id,
                'category_id' => $cat ? $cat->id : null,
                'food_subcategory_id' => $subcat ? $subcat->id : null,
                'title' => $exp['title'],
                'amount' => $exp['amount'],
                'date' => $date,
                'time' => '14:30:00',
                'payment_method' => $exp['payment'],
                'notes' => 'Sample transaction record for intelligent expense tracking analysis.',
                'location' => 'Downtown Central',
                'is_healthy' => $exp['healthy'],
                'mood' => $exp['mood'],
            ]);

            if (!empty($exp['tags'])) {
                $tagIds = [];
                foreach ($exp['tags'] as $tagName) {
                    if (isset($createdTags[$tagName])) {
                        $tagIds[] = $createdTags[$tagName]->id;
                    }
                }
                $expenseModel->tags()->sync($tagIds);
            }
        }

        // 6. Seed Budgets
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['Food']->id,
            'type' => 'category',
            'amount' => 15000.00,
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
        ]);
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['Shopping']->id,
            'type' => 'category',
            'amount' => 10000.00,
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
        ]);

        // 7. Seed Recurring Expenses
        RecurringExpense::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['Rent']->id,
            'title' => 'Monthly Apartment Rent',
            'amount' => 22000.00,
            'frequency' => 'monthly',
            'payment_method' => 'Net Banking',
            'start_date' => Carbon::now()->startOfMonth(),
            'last_processed_at' => Carbon::now()->startOfMonth(),
            'is_active' => true,
        ]);

        RecurringExpense::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['Subscription']->id,
            'title' => 'Netflix & Spotify Bundle',
            'amount' => 849.00,
            'frequency' => 'monthly',
            'payment_method' => 'Credit Card',
            'start_date' => Carbon::now()->startOfMonth(),
            'last_processed_at' => Carbon::now()->startOfMonth(),
            'is_active' => true,
        ]);

        // 8. Notifications
        AppNotification::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => 'Welcome to Smart Tracker',
            'message' => 'Your intelligent expense dashboard is ready with live analytics and insights.',
            'action_url' => route('dashboard'),
        ]);
    }
}
