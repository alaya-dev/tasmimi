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
            $table->dropColumn('generated_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_templates', function (Blueprint $table) {
            $table->longText('generated_image_url')->nullable()->after('thumbnail');
        });
    }
};
