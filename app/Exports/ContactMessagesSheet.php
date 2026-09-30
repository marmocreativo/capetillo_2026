<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactMessagesSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStrictNullComparison
{
    public function __construct(protected array $filters = [])
    {
    }

    public function title(): string
    {
        return 'Contactos';
    }

    public function query()
    {
        return DB::table('contact_messages')
            ->when($this->filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($this->filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('talent_name', 'like', "%{$s}%");
                });
            })
            ->when($this->filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($this->filters['desde'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($this->filters['hasta'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('id'); // orden único: evita filas duplicadas al hacer chunk
    }

    public function headings(): array
    {
        return [
            'ID', 'Fecha de registro', 'Tipo', 'Estatus', 'Talento', 'Nombre', 'Empresa',
            'Puesto', 'Correo', 'Teléfono', 'Mensaje', 'Estado', 'Ciudad', 'Aforo',
            'Venue', 'Fecha del evento', 'Hora de acceso', 'Hora de presentación',
            'Tipo de evento', 'Venta de boletos', 'Formato de contratación',
            'Tiene presupuesto', 'Presupuesto aprox.', 'Detalle de actividad',
            'Req. de operación', 'Req. técnicos', 'Notas', 'Vigencia',
            'Cotización final', 'Enviar cotización', 'Datos completados el',
        ];
    }

    public function map($row): array
    {
        $fecha = fn ($v, $fmt = 'd/m/Y') => $v ? Carbon::parse($v)->format($fmt) : '';
        $bool  = fn ($v) => $v === null ? '' : ($v ? 'Sí' : 'No');

        return [
            $row->id,
            $fecha($row->created_at, 'd/m/Y H:i'),
            $row->type === 'contratacion' ? 'Contratación' : 'General',
            ContactMessagesExport::STATUS[$row->status] ?? $row->status,
            $row->talent_name,
            $row->name,
            $row->empresa,
            $row->puesto_contacto,
            $row->email,
            $row->phone,
            $row->message,
            $row->estado_republica,
            $row->ciudad,
            $row->aforo_esperado,
            $row->venue,
            $fecha($row->fecha_evento),
            $row->hora_acceso ? substr($row->hora_acceso, 0, 5) : '',
            $row->hora_presentacion ? substr($row->hora_presentacion, 0, 5) : '',
            $row->tipo_evento,
            $bool($row->con_venta_boletos),
            $row->formato_contratacion,
            $bool($row->tiene_presupuesto),
            $row->presupuesto_aproximado,
            $row->detalle_actividad,
            $row->requerimientos_operacion,
            $row->requerimientos_tecnicos,
            $row->notas,
            $fecha($row->fecha_vigencia),
            $row->cotizacion_final,
            $bool($row->enviar_cotizacion),
            $fecha($row->extra_info_completed_at, 'd/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J'  => NumberFormat::FORMAT_TEXT,      // teléfono como texto
            'W'  => '#,##0.00',                     // presupuesto aprox.
            'AC' => '#,##0.00',                     // cotización final
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}