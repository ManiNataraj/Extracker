<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->string('frequency')->default('monthly'); // daily, weekly, monthly, yearly
            $table->string('payment_method')->default('UPI');
            $table->text('notes')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};
