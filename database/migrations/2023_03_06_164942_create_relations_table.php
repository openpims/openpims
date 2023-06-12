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
        Schema::dropIfExists('relations');
        Schema::create('relations', function (Blueprint $table) {
            $table->integerIncrements('relation_id');
            $table->integer('host_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->unique(['host_id', 'category_id'], 'unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relations');
    }
};
