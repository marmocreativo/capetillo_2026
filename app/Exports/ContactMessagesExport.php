<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContactMessagesExport implements WithMultipleSheets
{
    public const STATUS = [
        'contacto_inicial'     => 'Contacto inicial',
        'en_espera_cotizacion' => 'En espera de cotización',
        'procesando'           => 'Procesando',
        'venta_no_concluida'   => 'Venta no concluida',
        'cotizacion_completa'  => 'Cotización completa',
        'contrato_cerrado'     => 'Contrato cerrado',
        'contrato_pagado'      => 'Contrato pagado',
    ];

    public function __construct(protected array $filters = [])
    {
    }

    public function sheets(): array
    {
        return [
            new ContactMessagesSheet($this->filters),
            new ContactMessageTalentsSheet($this->filters),
        ];
    }
}