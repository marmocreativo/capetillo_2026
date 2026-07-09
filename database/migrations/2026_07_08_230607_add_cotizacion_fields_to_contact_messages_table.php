<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->text('notas')->nullable()->after('requerimientos_tecnicos');
            $table->date('fecha_vigencia')->nullable()->after('notas');
            $table->json('datos_contacto')->nullable()->after('fecha_vigencia');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['notas', 'fecha_vigencia', 'datos_contacto']);
        });
    }
};