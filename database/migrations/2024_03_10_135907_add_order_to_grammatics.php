<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('grammatics', function (Blueprint $table) {
            $table->integer('order')->after('article');
        });

        foreach (\App\Models\Language::all() as $language) {
            $order = 1;
            foreach ($language->grammatics as $grammatic) {
                $grammatic->order = $order;
                $grammatic->save();
                $order += 1;
            }
        }

        Schema::table('grammatic_values', function (Blueprint $table) {
            $table->integer('order')->after('short');
        });

        foreach (\App\Models\Grammatic::all() as $grammatic) {
            $order = 1;
            foreach ($grammatic->grammatic_values as $value) {
                $value->order = $order;
                $value->save();
                $order += 1;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('grammatics', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('grammatic_values', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
