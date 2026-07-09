<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('public_token', 40)->nullable()->unique()->after('id');
            $table->string('empresa')->nullable()->after('talent_name');
            $table->string('puesto_contacto')->nullable()->after('empresa');
            $table->string('ciudad')->nullable()->after('estado_republica');
            $table->date('fecha_evento')->nullable()->after('venue');
            $table->time('hora_acceso')->nullable()->after('fecha_evento');
            $table->time('hora_presentacion')->nullable()->after('hora_acceso');
            $table->json('tipo_evento')->nullable()->after('hora_presentacion');
            $table->string('formato_contratacion')->nullable()->after('tipo_evento');
            $table->boolean('tiene_presupuesto')->nullable()->after('formato_contratacion');
            $table->decimal('presupuesto_aproximado', 10, 2)->nullable()->after('tiene_presupuesto');
            $table->text('detalle_actividad')->nullable()->after('presupuesto_aproximado');
            $table->text('requerimientos_operacion')->nullable()->after('detalle_actividad');
            $table->text('requerimientos_tecnicos')->nullable()->after('requerimientos_operacion');
            $table->timestamp('extra_info_completed_at')->nullable()->after('requerimientos_tecnicos');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn([
                'public_token', 'empresa', 'puesto_contacto', 'ciudad', 'fecha_evento',
                'hora_acceso', 'hora_presentacion', 'tipo_evento', 'formato_contratacion',
                'tiene_presupuesto', 'presupuesto_aproximado', 'detalle_actividad',
                'requerimientos_operacion', 'requerimientos_tecnicos', 'extra_info_completed_at',
            ]);
        });
    }
};