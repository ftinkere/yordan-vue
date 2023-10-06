<?php

use App\Models\OrthographemePronunciation;
use App\Models\PronunciationContext;
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
        Schema::table('orthographeme_pronunciations', static function (Blueprint $table) {
            $table->text('context')->after('pronunciation')->nullable();
        });

        $data = PronunciationContext::all();

        foreach ($data->unique('pronunciation_id')->pluck('pronunciation_id') as $pronunciation_id) {
            $contexts = $data->where('pronunciation_id', $pronunciation_id);
            $context_result = implode("\n", $contexts->pluck('context')->toArray());

            $pronunciation = OrthographemePronunciation::find($pronunciation_id);
            $pronunciation->context = $context_result;
            $pronunciation->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orthographeme_pronunciations', static function (Blueprint $table) {
            $table->dropColumn('context');
        });
    }
};
