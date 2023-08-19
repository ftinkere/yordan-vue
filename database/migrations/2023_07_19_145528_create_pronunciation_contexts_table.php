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
        Schema::create('pronunciation_contexts', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pronunciation_id');
            $table->string('context');
            $table->timestamps();

            $table->foreign('pronunciation_id')->references('id')->on('orthographeme_pronunciations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciation_contexts');
    }
};
