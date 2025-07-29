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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('set null');
            
            // Moyasar payment details
            $table->string('payment_gateway')->default('moyasar'); // moyasar, stripe, etc.
            $table->string('payment_gateway_id')->unique(); // Moyasar payment ID
            $table->string('payment_method')->nullable(); // creditcard, applepay, stcpay, etc.
            
            // Payment information
            $table->decimal('amount', 10, 2); // Amount in SAR
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('pending'); // pending, paid, failed, canceled, refunded
            $table->text('description')->nullable();
            
            // Moyasar specific fields
            $table->string('moyasar_invoice_id')->nullable(); // For hosted payments
            $table->string('moyasar_source_id')->nullable(); // Source ID from Moyasar
            $table->decimal('fee', 8, 2)->nullable(); // Moyasar fee
            $table->json('metadata')->nullable(); // Additional data
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['payment_gateway', 'payment_gateway_id']);
            $table->index(['subscription_id', 'status']);
            $table->index(['template_id', 'status']);
            $table->index('status');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
