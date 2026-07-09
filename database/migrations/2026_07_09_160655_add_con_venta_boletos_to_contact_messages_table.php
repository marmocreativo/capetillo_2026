<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->boolean('con_venta_boletos')->default(false)->after('tipo_evento');
        });

        // Migra los registros que tenían tipo_evento = 'con_venta_boletos' antes de eliminarlo del enum/valores permitidos.
        DB::table('contact_messages')
            ->where('tipo_evento', 'con_venta_boletos')
            ->update(['con_venta_boletos' => true, 'tipo_evento' => null]);
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('con_venta_boletos');
        });
    }
};