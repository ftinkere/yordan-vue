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
        Schema::create('orthographemes', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id');
            $table->string('lowercase');
            $table->string('uppercase')->nullable();
            $table->boolean('is_alphabet')->default(true);
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orthographemes');
    }
};
