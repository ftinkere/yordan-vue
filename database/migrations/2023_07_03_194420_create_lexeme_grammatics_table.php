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
        Schema::create('lexeme_grammatics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lexeme_id');
            $table->unsignedBigInteger('grammatic_value_id');
            $table->boolean('is_variable')->default(false);
            $table->timestamps();

            $table->foreign('lexeme_id')->references('id')->on('lexemes')->cascadeOnDelete();
            $table->foreign('grammatic_value_id')->references('id')->on('grammatic_values')->cascadeOnDelete();
            $table->unique(['lexeme_id', 'grammatic_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lexeme_grammatics');
    }
};
