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
        Schema::create('orthographeme_pronunciations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orthographeme_id');
            $table->unsignedBigInteger('pronunciation');
            $table->timestamps();

            $table->foreign('orthographeme_id')->references('id')->on('orthographemes')->cascadeOnDelete();
            $table->foreign('pronunciation')->references('id')->on('language_sounds')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orthographeme_pronunciations');
    }
};
