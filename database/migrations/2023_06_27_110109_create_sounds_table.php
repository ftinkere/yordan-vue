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
        Schema::create('sounds', function (Blueprint $table) {
            $table->id();
            $table->string('sound');
            $table->string('table');
            $table->string('row');
            $table->string('column');
            $table->string('sub_column')->nullable()->default(null);
            $table->unsignedBigInteger('language_id')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sounds');
    }
};
