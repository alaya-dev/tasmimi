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
        // Ensure design_data can handle very large data
        DB::statement('ALTER TABLE templates MODIFY design_data LONGTEXT');
        
        // Set MySQL session variables for large data handling
        DB::statement('SET SESSION max_allowed_packet = 67108864'); // 64MB
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this optimization
    }
};
