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
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('content_blocks');
            $table->string('file_path')->nullable()->after('file_name');
            $table->string('file_type')->nullable()->after('file_path'); // word, pdf
            $table->integer('file_size')->nullable()->after('file_type');
            $table->text('extracted_content')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_path', 'file_type', 'file_size', 'extracted_content']);
        });
    }
};
