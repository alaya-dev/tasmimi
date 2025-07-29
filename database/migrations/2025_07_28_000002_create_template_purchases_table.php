<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('template_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('templates')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            
            // Purchase details
            $table->decimal('amount', 8, 2); // Amount paid for the template
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            
            // Purchase metadata
            $table->json('metadata')->nullable(); // Additional purchase data
            $table->text('notes')->nullable(); // Purchase notes
            
            // Timestamps
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['template_id', 'status']);
            $table->index(['user_id', 'template_id']);
            $table->index('status');
            $table->index('purchased_at');
            
            // Unique constraint to prevent duplicate purchases
            $table->unique(['user_id', 'template_id'], 'unique_user_template_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_purchases');
    }
};
