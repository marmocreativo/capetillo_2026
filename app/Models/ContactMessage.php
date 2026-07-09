<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'type',
        'talent_id',
        'talent_name',
        'empresa',
        'puesto_contacto',
        'name',
        'email',
        'phone',
        'message',
        'estado_republica',
        'ciudad',
        'aforo_esperado',
        'venue',
        'fecha_evento',
        'hora_acceso',
        'hora_presentacion',
        'tipo_evento',
        'con_venta_boletos',
        'formato_contratacion',
        'tiene_presupuesto',
        'presupuesto_aproximado',
        'detalle_actividad',
        'requerimientos_operacion',
        'requerimientos_tecnicos',
        'cotizacion_final',
        'status',
        'extra_info_completed_at',
        'notas',
        'fecha_vigencia',
        'datos_contacto',
    ];

    protected $casts = [
        'aforo_esperado' => 'integer',
        'cotizacion_final' => 'decimal:2',
        'presupuesto_aproximado' => 'decimal:2',
        'tiene_presupuesto' => 'boolean',
        'con_venta_boletos' => 'boolean',
        'tipo_evento' => 'array',
        'fecha_evento' => 'date',
        'hora_acceso' => 'datetime:H:i',
        'hora_presentacion' => 'datetime:H:i',
        'extra_info_completed_at' => 'datetime',
        'fecha_vigencia' => 'date',
        'datos_contacto' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContactMessage $contactMessage) {
            if (empty($contactMessage->public_token)) {
                $contactMessage->public_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function cotizacionTalents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContactMessageTalent::class)->orderBy('orden');
    }
}