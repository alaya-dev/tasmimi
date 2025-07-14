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
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename'); // Nom original du fichier
            $table->string('stored_filename'); // Nom du fichier stocké (avec hash)
            $table->string('path'); // Chemin relatif du fichier
            $table->string('mime_type'); // Type MIME (image/jpeg, image/png, etc.)
            $table->string('extension'); // Extension du fichier
            $table->bigInteger('size'); // Taille en bytes
            $table->string('folder')->default('general'); // Dossier d'organisation
            $table->json('metadata')->nullable(); // Métadonnées (dimensions, etc.)
            $table->string('source')->default('upload'); // Source: upload, pexels, etc.
            $table->string('source_id')->nullable(); // ID de la source externe
            $table->boolean('is_public')->default(false); // Accessible publiquement
            $table->text('description')->nullable(); // Description du fichier
            $table->json('tags')->nullable(); // Tags pour recherche
            $table->timestamp('last_used_at')->nullable(); // Dernière utilisation
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['user_id', 'folder']);
            $table->index(['user_id', 'mime_type']);
            $table->index(['user_id', 'source']);
            $table->index('last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
