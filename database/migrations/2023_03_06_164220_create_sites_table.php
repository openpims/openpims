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
        Schema::dropIfExists('sites');
        Schema::create('sites', function (Blueprint $table) {
            $table->integerIncrements('site_id');
            $table->string('site', 64);
            $table->string('url');
            $table->unique(['site', 'url'], 'unique');
            //$table->boolean('not_loaded')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
