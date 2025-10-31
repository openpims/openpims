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
        Schema::dropIfExists('cookies');
        Schema::create('cookies', function (Blueprint $table) {
            $table->integerIncrements('cookie_id');
            $table->string('cookie');
            $table->unsignedInteger('site_id');
            $table->unique(['cookie', 'site_id'], 'unique');
            $table->boolean('necessary')->default(0);
            $table->string('category', 50)->default('functional'); // functional, personalization, analytics, marketing

            // Provider information
            $table->string('provider', 255)->nullable(); // Singular (was: providers)

            // Open Cookie Database fields - Domain & Third-Party Detection
            $table->string('domain', 255)->nullable(); // e.g., ".google.com" or ".example.com"
            $table->boolean('is_third_party')->default(false); // true if domain != site domain

            // Open Cookie Database fields - Data Controller (GDPR Art. 13)
            $table->string('data_controller', 255)->nullable(); // e.g., "Google LLC, USA"
            $table->string('controller_country', 2)->nullable(); // ISO 3166-1 alpha-2, e.g., "US", "DE"
            $table->boolean('is_third_country')->default(false); // true if country outside EU/EEA (Schrems II)

            // Open Cookie Database fields - Wildcard matching
            $table->boolean('is_wildcard')->default(false); // true for patterns like "_gac_*"
            $table->string('pattern', 255)->nullable(); // Regex pattern for wildcard cookies

            // Open Cookie Database fields - Privacy information
            $table->string('privacy_policy_url', 500)->nullable(); // Link to provider's privacy policy

            // Existing fields
            $table->text('data_stored')->nullable();
            $table->text('purposes')->nullable();
            $table->text('retention_periods')->nullable();
            $table->text('revocation_info')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['site_id', 'category']);
            $table->index('domain');
            $table->index('is_third_party');
            $table->index('data_controller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookies');
    }
};
