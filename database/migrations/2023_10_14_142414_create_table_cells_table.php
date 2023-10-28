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
        Schema::create('table_cells', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('row_id');
            $table->text('content')->default('');
            $table->boolean('is_header')->default(false);
            $table->unsignedInteger('rowspan')->default(1);
            $table->unsignedInteger('colspan')->default(1);
            $table->timestamps();

            $table->foreign('row_id')->references('id')->on('table_rows')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_cells');
    }
};
