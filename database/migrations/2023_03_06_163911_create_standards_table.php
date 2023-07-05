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
        Schema::dropIfExists('standards');
        Schema::create('standards', function (Blueprint $table) {
            $table->integerIncrements('standard_id');
            $table->string('standard');
            $table->integer('user_id')->nullable()->default(null);
            $table->boolean('checked')->default(0);
            $table->boolean('disabled')->default(0);
            $table->unique(['standard', 'user_id'], 'unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};
