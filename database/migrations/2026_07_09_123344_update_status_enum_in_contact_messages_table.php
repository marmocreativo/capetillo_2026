<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero ampliamos el enum para incluir los valores viejos y nuevos a la vez,
        // así el UPDATE de abajo no falla por truncamiento.
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM(
            'contacto_inicial',
            'seguimiento',
            'procesando',
            'venta_no_concluida',
            'cotizacion_completa',
            'contrato_cerrado',
            'contrato_pagado'
        ) NOT NULL DEFAULT 'contacto_inicial'");

        // Migra registros con el valor viejo 'seguimiento' al más equivalente: 'procesando'.
        DB::table('contact_messages')
            ->where('status', 'seguimiento')
            ->update(['status' => 'procesando']);

        // Ahora sí, dejamos el enum solo con los valores finales.
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM(
            'contacto_inicial',
            'procesando',
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
            'seguimiento',
            'contrato_cerrado',
            'contrato_pagado'
        ) NOT NULL DEFAULT 'contacto_inicial'");
    }
};