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
            // Add payment_id column as nullable foreign key
            $table->foreignId('payment_id')->nullable()->after('template_id')->constrained('payments')->onDelete('set null');

            // Add index for payment_id
            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_purchases', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
    }
};
