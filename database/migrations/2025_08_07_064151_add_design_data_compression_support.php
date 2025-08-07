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
        // Change design_data column type to LONGTEXT for better compression support
        Schema::table('templates', function (Blueprint $table) {
            $table->longText('design_data')->nullable()->change();
        });

        Schema::table('client_templates', function (Blueprint $table) {
            $table->longText('design_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to TEXT
        Schema::table('templates', function (Blueprint $table) {
            $table->text('design_data')->nullable()->change();
        });

        Schema::table('client_templates', function (Blueprint $table) {
            $table->text('design_data')->nullable()->change();
        });
    }
};
