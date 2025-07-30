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
            // Remove the unique constraint that prevents users from having multiple designs
            // based on the same template
            $table->dropUnique('unique_user_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_templates', function (Blueprint $table) {
            // Restore the unique constraint (Note: this will fail if there are duplicates)
            $table->unique(['user_id', 'template_id'], 'unique_user_template');
        });
    }
};
