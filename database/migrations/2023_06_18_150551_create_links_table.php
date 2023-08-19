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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('from_lexeme_id');
            $table->unsignedBigInteger('to_lexeme_id');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('from_lexeme_id')->references('id')->on('lexemes')->cascadeOnDelete();
            $table->foreign('to_lexeme_id')->references('id')->on('lexemes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
