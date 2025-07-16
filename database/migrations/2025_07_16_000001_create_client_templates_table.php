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
        Schema::create('client_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('templates')->onDelete('cascade');
            $table->string('name'); // Nom personnalisé donné par le client
            $table->longText('design_data'); // Données JSON du design personnalisé
            $table->json('editable_elements')->nullable(); // Éléments modifiés par le client
            $table->string('canvas_size')->default('800x600'); // Taille du canvas
            $table->string('thumbnail')->nullable(); // Miniature générée
            $table->text('notes')->nullable(); // Notes personnelles du client
            $table->integer('version')->default(1); // Version du design
            $table->timestamp('last_edited_at')->nullable(); // Dernière modification
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'template_id']);
            $table->index('last_edited_at');
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['user_id', 'template_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_templates');
    }
};
