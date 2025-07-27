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
            $table->timestamps();
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
