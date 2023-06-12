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
        Schema::dropIfExists('consenses');
        Schema::create('consenses', function (Blueprint $table) {
            $table->integerIncrements('consense_id');
            $table->integer('user_id')->nullable()->default(null);
            $table->integer('host_id')->nullable()->default(null);
            $table->integer('category_id')->nullable()->default(null);
            $table->unique(['user_id', 'host_id', 'category_id'], 'unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consenses');
    }
};
