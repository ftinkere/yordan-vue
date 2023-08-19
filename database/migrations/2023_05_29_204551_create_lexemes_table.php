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
        Schema::create('lexemes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vocabula_id')->nullable(false);
            $table->integer('group_number')->default(1);
            $table->integer('lexeme_number')->default(1);
            $table->text('short')->nullable(false);
            $table->text('article')->nullable(false);
            $table->timestamps();

            $table->foreign('vocabula_id')->references('id')->on('vocables')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lexemes');
    }
};
