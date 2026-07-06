<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            $table->boolean('mostrar_honorarios')->default(false)->after('separar_por_categoria');
        });
    }

    public function down(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            $table->dropColumn('mostrar_honorarios');
        });
    }
};