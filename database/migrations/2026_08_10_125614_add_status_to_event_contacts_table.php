<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_contacts', function (Blueprint $table) {
            $table->enum('status', [
                'contacto_inicial',
                'procesando',
                'en_espera_cotizacion',
                'venta_no_concluida',
                'cotizacion_completa',
                'contrato_cerrado',
                'contrato_pagado',
            ])->default('contacto_inicial')->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('event_contacts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};