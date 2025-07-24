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
        Schema::table('payments', function (Blueprint $table) {
            // Renommer les colonnes Stripe vers des noms génériques
            $table->renameColumn('stripe_payment_intent_id', 'payment_gateway_id');
            $table->renameColumn('stripe_payment_method_id', 'payment_method_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Renommer la colonne Stripe dans user_subscriptions aussi
            $table->renameColumn('stripe_subscription_id', 'gateway_subscription_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Renommer les colonnes Stripe dans subscriptions
            $table->renameColumn('stripe_price_id', 'gateway_price_id');
            $table->renameColumn('stripe_product_id', 'gateway_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_gateway_id', 'stripe_payment_intent_id');
            $table->renameColumn('payment_method_id', 'stripe_payment_method_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->renameColumn('gateway_subscription_id', 'stripe_subscription_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('gateway_price_id', 'stripe_price_id');
            $table->renameColumn('gateway_product_id', 'stripe_product_id');
        });
    }
};
