<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates consent_providers table for Tier 2 (Provider-Level) consents
     * in the 3-Tier consent model:
     * - Tier 1: Category-Level (consent_categories)
     * - Tier 2: Provider-Level (consent_providers) ← THIS TABLE
     * - Tier 3: Cookie-Level (consents)
     */
    public function up(): void
    {
        Schema::create('consent_providers', function (Blueprint $table) {
            $table->integerIncrements('consent_provider_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('site_id');
            $table->string('category', 50); // functional, personalization, analytics, marketing
            $table->string('provider', 255); // "Google Analytics", "Meta Platforms (Facebook)", etc.
            $table->boolean('consent_status')->nullable()->default(null); // 1=accepted, 0=rejected, null=not_set (was: checked)
            $table->timestamp('consented_at')->nullable(); // GDPR Art. 7: When user gave/changed consent
            $table->timestamps();

            // Unique constraint: one consent per user + site + category + provider
            $table->unique(['user_id', 'site_id', 'category', 'provider'], 'unique_user_site_cat_prov');

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('site_id');
            $table->index(['user_id', 'site_id']);
            $table->index(['user_id', 'site_id', 'category']);
            $table->index('consented_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_providers');
    }
};
