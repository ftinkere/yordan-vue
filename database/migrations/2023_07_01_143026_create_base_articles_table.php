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
        Schema::create('base_articles', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id');
            $table->text('about')->nullable();
            $table->text('phonetic')->nullable();
            $table->text('grammatic')->nullable();
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('base_articles');
    }
};
