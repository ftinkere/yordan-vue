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
        Schema::table('table_cells', static function (Blueprint $table) {
            $table->unsignedBigInteger('merged_in')->nullable()->default(null)->after('colspan');

            $table->foreign('merged_in')->references('id')->on('table_cells')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_cells', static function (Blueprint $table) {
            $table->dropColumn('merged_in');
        });
    }
};
