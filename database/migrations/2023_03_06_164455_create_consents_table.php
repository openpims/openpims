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
        Schema::dropIfExists('consents');
        Schema::create('consents', function (Blueprint $table) {
            $table->integerIncrements('consent_id');
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->unique(['user_id', 'category_id'], 'unique');
            $table->boolean('checked')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
