<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->string('summary')->nullable()->after('name');
            $table->string('spotify_url')->nullable()->after('content');
            $table->json('highlights')->nullable()->after('spotify_url');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['summary', 'spotify_url', 'highlights']);
        });
    }
};
