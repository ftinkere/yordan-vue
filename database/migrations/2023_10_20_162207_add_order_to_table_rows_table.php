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
        Schema::table('table_rows', static function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('table_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_rows', static function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
