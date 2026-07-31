<?php

namespace App\Services;

use App\Models\User;
use App\Models\RecurringExpense;
use App\Models\Expense;
use App\Models\AppNotification;
use Carbon\Carbon;

class RecurringExpenseService
{
    public function processDueRecurringExpenses(User $user): int
    {
        $today = Carbon::today();
        $dueItems = $user->recurringExpenses()
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->get();

        $processedCount = 0;

        foreach ($dueItems as $recurring) {
            $shouldProcess = false;

            if (!$recurring->last_processed_at) {
                $shouldProcess = true;
            } else {
                $last = Carbon::parse($recurring->last_processed_at);
                switch ($recurring->frequency) {
                    case 'daily':
                        $shouldProcess = $last->diffInDays($today) >= 1;
                        break;
                    case 'weekly':
                        $shouldProcess = $last->diffInWeeks($today) >= 1;
                        break;
                    case 'monthly':
                        $shouldProcess = $last->diffInMonths($today) >= 1;
                        break;
                    case 'yearly':
                        $shouldProcess = $last->diffInYears($today) >= 1;
                        break;
                }
            }

            if ($shouldProcess) {
                Expense::create([
                    'user_id' => $user->id,
                    'category_id' => $recurring->category_id,
                    'title' => $recurring->title . ' (Recurring)',
                    'amount' => $recurring->amount,
                    'date' => $today->format('Y-m-d'),
                    'time' => Carbon::now()->format('H:i:s'),
                    'payment_method' => $recurring->payment_method ?? 'UPI',
                    'notes' => $recurring->notes,
                    'is_recurring_instance' => true,
                ]);

                $recurring->update(['last_processed_at' => Carbon::now()]);

                AppNotification::create([
                    'user_id' => $user->id,
                    'type' => 'recurring',
                    'title' => 'Recurring Expense Processed',
                    'message' => sprintf("Auto-added recurring expense '%s' for %s%s.", $recurring->title, $user->currency_symbol, number_format($recurring->amount, 2)),
                    'action_url' => route('expenses.index'),
                ]);

                $processedCount++;
            }
        }

        return $processedCount;
    }
}
