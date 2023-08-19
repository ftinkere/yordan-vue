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
        Schema::create('language_sounds', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('sound_id');
            $table->unsignedBigInteger('allophone_of')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
            $table->foreign('sound_id')->references('id')->on('sounds')->cascadeOnDelete();
            $table->foreign('allophone_of')->references('id')->on('language_sounds')->nullOnDelete();

            $table->unique(['language_id', 'sound_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_sounds');
    }
};
