<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->boolean('mostrar_network')->default(false)->after('destacado');
            $table->json('recomendaciones_network')->nullable()->after('mostrar_network');
            $table->boolean('mostrar_party')->default(false)->after('recomendaciones_network');
            $table->json('recomendaciones_party')->nullable()->after('mostrar_party');
        });
    }

    public function down(): void
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['mostrar_network', 'recomendaciones_network', 'mostrar_party', 'recomendaciones_party']);
        });
    }
};