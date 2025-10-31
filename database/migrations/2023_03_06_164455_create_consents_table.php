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
        // Cookie-level consents (granular, for power users)
        Schema::dropIfExists('consents');
        Schema::create('consents', function (Blueprint $table) {
            $table->integerIncrements('consent_id');
            $table->integer('user_id')->unsigned();
            $table->integer('cookie_id')->unsigned();
            $table->unique(['user_id', 'cookie_id'], 'unique');
            $table->boolean('consent_status')->nullable()->default(null); // 1=accepted, 0=rejected, null=not_set (was: checked)
            $table->timestamp('consented_at')->nullable(); // GDPR Art. 7: When user gave/changed consent
            $table->timestamps();

            // Indexes
            $table->index('consented_at');
        });

        // Category-level consents (simple, TDDDG-compliant)
        Schema::dropIfExists('consent_categories');
        Schema::create('consent_categories', function (Blueprint $table) {
            $table->integerIncrements('consent_category_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('site_id');
            $table->string('category', 50); // functional, personalization, analytics, marketing
            $table->boolean('consent_status')->nullable()->default(null); // 1=accepted, 0=rejected, null=not_set (was: checked)
            $table->timestamp('consented_at')->nullable(); // GDPR Art. 7: When user gave/changed consent
            $table->timestamps();

            // Ensure one consent per user + site + category
            $table->unique(['user_id', 'site_id', 'category'], 'unique_user_site_category');

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('site_id');
            $table->index(['user_id', 'site_id']);
            $table->index('consented_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_categories');
        Schema::dropIfExists('consents');
    }
};
