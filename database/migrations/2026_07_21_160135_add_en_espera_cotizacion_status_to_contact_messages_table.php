<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM(
            'contacto_inicial',
            'procesando',
            'en_espera_cotizacion',
            'venta_no_concluida',
            'cotizacion_completa',
            'contrato_cerrado',
            'contrato_pagado'
        ) NOT NULL DEFAULT 'contacto_inicial'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM(
            'contacto_inicial',
            'procesando',
            'venta_no_concluida',
            'cotizacion_completa',
            'contrato_cerrado',
            'contrato_pagado'
        ) NOT NULL DEFAULT 'contacto_inicial'");
    }
};