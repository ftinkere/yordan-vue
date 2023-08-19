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
            $table->fullText(['short', 'article'], 'lexemes_short_article_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lexemes', static function (Blueprint $table) {
            $table->dropFullText('lexemes_short_article_fulltext');
        });
    }
};
