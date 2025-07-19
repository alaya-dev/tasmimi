<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the column type to LONGTEXT to handle large base64 data
        DB::statement('ALTER TABLE client_templates MODIFY generated_image_url LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE client_templates MODIFY generated_image_url TEXT NULL');
    }
};
