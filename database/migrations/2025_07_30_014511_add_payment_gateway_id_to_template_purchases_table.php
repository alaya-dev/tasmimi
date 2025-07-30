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
        Schema::table('template_purchases', function (Blueprint $table) {
            // Add payment_gateway_id column to store Moyasar payment ID
            $table->string('payment_gateway_id')->nullable()->after('template_id');
            
            // Add payment_method column
            $table->string('payment_method')->default('moyasar')->after('status');
            
            // Add paid_at timestamp
            $table->timestamp('paid_at')->nullable()->after('metadata');
            
            // Drop the foreign key constraint for payment_id if it exists
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
            
            // Add index for payment_gateway_id
            $table->index('payment_gateway_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_purchases', function (Blueprint $table) {
            // Remove the added columns
            $table->dropIndex(['payment_gateway_id']);
            $table->dropColumn(['payment_gateway_id', 'payment_method', 'paid_at']);
            
            // Restore payment_id column
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null')->after('template_id');
        });
    }
};
