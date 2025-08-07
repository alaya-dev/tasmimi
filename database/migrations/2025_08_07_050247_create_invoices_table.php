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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Invoice identification
            $table->string('invoice_number')->unique(); // INV-YYYY-MM-DDDD format

            // User relationship
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Polymorphic relationship to payments or template_purchases
            $table->string('invoiceable_type'); // Payment or TemplatePurchase
            $table->unsignedBigInteger('invoiceable_id');
            $table->index(['invoiceable_type', 'invoiceable_id']);

            // Invoice details
            $table->decimal('amount', 10, 2); // Invoice amount
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('pending'); // pending, paid, failed, canceled, refunded
            $table->string('type'); // subscription, template (for easy filtering)

            // Invoice dates
            $table->timestamp('invoice_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Additional details
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional invoice data

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'invoice_date']);
            $table->index(['type', 'status']);
            $table->index('invoice_number');
            $table->index('invoice_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
