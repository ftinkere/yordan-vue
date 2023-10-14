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
        Schema::create('table_cell_styles', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cell_id');
            $table->string('style');
            $table->string('value');
            $table->timestamps();

            $table->foreign('cell_id')->references('id')->on('table_cells')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_cell_styles');
    }
};
