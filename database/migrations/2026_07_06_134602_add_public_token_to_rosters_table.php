<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            $table->string('public_token', 40)->nullable()->unique()->after('id');
        });

        // Backfill para rosters que ya existan.
        DB::table('rosters')->whereNull('public_token')->get()->each(function ($roster) {
            DB::table('rosters')->where('id', $roster->id)->update([
                'public_token' => Str::random(32),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            $table->dropColumn('public_token');
        });
    }
};