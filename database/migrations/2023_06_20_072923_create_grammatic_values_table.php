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
        Schema::create('grammatic_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grammatic_id');
            $table->string('value');
            $table->text('article')->nullable();
            $table->string('short');
            $table->timestamps();

            $table->foreign('grammatic_id')->references('id')->on('grammatics')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grammatic_values');
    }
};
