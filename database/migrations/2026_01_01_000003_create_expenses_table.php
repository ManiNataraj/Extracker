<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('food_subcategory_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('payment_method')->default('Cash');
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_healthy')->nullable();
            $table->string('mood')->nullable();
            $table->boolean('is_recurring_instance')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
