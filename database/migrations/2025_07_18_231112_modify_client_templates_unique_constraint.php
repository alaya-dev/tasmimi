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
        Schema::table('client_templates', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['user_id', 'template_id', 'name']);

            // Add a new unique constraint that allows multiple designs per template per user
            // but ensures unique names per user per template
            $table->unique(['user_id', 'template_id'], 'unique_user_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_templates', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('unique_user_template');

            // Restore the original constraint
            $table->unique(['user_id', 'template_id', 'name']);
        });
    }
};
