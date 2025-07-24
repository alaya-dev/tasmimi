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
        Schema::table('payments', function (Blueprint $table) {
            // Change payment_method from enum to string to support more payment methods
            $table->string('payment_method', 50)->default('creditcard')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('payment_method', ['card', 'bank_transfer', 'wallet'])->default('card')->change();
        });
    }
};
