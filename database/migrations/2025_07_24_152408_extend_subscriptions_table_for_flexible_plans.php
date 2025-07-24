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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Ajouter des colonnes pour la flexibilité des abonnements
            $table->integer('duration_days')->after('type')->nullable(); // Durée en jours (remplace le type fixe)
            $table->string('stripe_price_id')->after('price')->nullable(); // ID du prix Stripe
            $table->string('stripe_product_id')->after('stripe_price_id')->nullable(); // ID du produit Stripe
            $table->json('features')->after('description')->nullable(); // Fonctionnalités incluses
            $table->integer('sort_order')->after('is_active')->default(0); // Ordre d'affichage
            $table->boolean('is_popular')->after('sort_order')->default(false); // Plan populaire
            $table->string('color')->after('is_popular')->nullable(); // Couleur du plan

            // Index pour améliorer les performances
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'duration_days',
                'stripe_price_id',
                'stripe_product_id',
                'features',
                'sort_order',
                'is_popular',
                'color'
            ]);
        });
    }
};
