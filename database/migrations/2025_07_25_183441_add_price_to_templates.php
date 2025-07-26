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
        // Check if price column doesn't exist before adding it
        if (!Schema::hasColumn('templates', 'price')) {
            Schema::table('templates', function (Blueprint $table) {
                $table->decimal('price', 8, 2)->default(0)->after('is_active')->comment('سعر القالب بالريال السعودي');
                $table->index('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'price')) {
                $table->dropIndex(['price']);
                $table->dropColumn('price');
            }
        });
    }
};
