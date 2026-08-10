<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventContact extends Model
{
    use HasFactory;

    protected $table = 'event_contacts';

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'fecha_aproximada',
        'message',
        'status',
    ];

    protected $casts = [
        'fecha_aproximada' => 'date',
    ];

    public const STATUSES = [
        'contacto_inicial' => 'Contacto inicial',
        'procesando' => 'Procesando',
        'en_espera_cotizacion' => 'En espera de cotización',
        'venta_no_concluida' => 'Venta no concluida',
        'cotizacion_completa' => 'Cotización completa',
        'contrato_cerrado' => 'Contrato cerrado',
        'contrato_pagado' => 'Contrato pagado',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}