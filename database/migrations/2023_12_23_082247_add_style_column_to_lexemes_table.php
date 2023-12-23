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
        Schema::table('lexemes', static function (Blueprint $table) {
            $table->string('style')->nullable()->after('article')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lexemes', static function (Blueprint $table) {
            $table->dropColumn('style');
        });
    }
};
