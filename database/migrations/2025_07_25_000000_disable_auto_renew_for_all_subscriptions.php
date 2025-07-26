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
        // Disable auto_renew for all existing user subscriptions
        DB::table('user_subscriptions')->update(['auto_renew' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-enable auto_renew for all user subscriptions (if needed for rollback)
        DB::table('user_subscriptions')->update(['auto_renew' => true]);
    }
};
