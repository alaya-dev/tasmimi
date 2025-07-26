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
            $table->decimal('amount', 8, 2)->comment('المبلغ المدفوع بالريال السعودي');
            $table->string('currency', 3)->default('SAR');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->default('moyasar');
            $table->string('payment_gateway_id')->nullable()->comment('معرف الدفع من Moyasar');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable()->comment('بيانات إضافية من Moyasar');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'template_id']);
            $table->index(['status', 'paid_at']);
            $table->index('payment_gateway_id');
            
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
