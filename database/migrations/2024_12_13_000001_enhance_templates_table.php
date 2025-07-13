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
        Schema::table('templates', function (Blueprint $table) {
            // Ajouter des champs pour le nouveau système de design
            $table->string('background_image')->nullable()->after('thumbnail');
            $table->json('editable_elements')->nullable()->after('design_data');
            $table->string('canvas_size')->default('800x600')->after('editable_elements');
            $table->boolean('has_watermark')->default(true)->after('canvas_size');
            $table->text('design_notes')->nullable()->after('has_watermark');
            $table->integer('version')->default(1)->after('design_notes');
            $table->timestamp('last_edited_at')->nullable()->after('version');
            
            // Index pour améliorer les performances
            $table->index(['category_id', 'is_active']);
            $table->index(['created_by', 'is_active']);
            $table->index('last_edited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['created_by', 'is_active']);
            $table->dropIndex(['last_edited_at']);
            
            $table->dropColumn([
                'background_image',
                'editable_elements',
                'canvas_size',
                'has_watermark',
                'design_notes',
                'version',
                'last_edited_at'
            ]);
        });
    }
};
