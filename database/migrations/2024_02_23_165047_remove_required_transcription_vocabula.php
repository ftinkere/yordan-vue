<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('vocables', static function (Blueprint $table) {
            $table->string('transcription')->nullable()->change();
        });
        \App\Models\Vocabula::where('transcription', ' ')->update(['transcription' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        \App\Models\Vocabula::whereNull('transcription')->update(['transcription' => '']);

        Schema::table('vocables', static function (Blueprint $table) {
            $table->string('transcription')->nullable(false)->change();
        });
    }
};
