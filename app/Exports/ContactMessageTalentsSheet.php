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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactMessageTalentsSheet implements
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
        return 'Talentos cotizados';
    }

    public function query()
    {
        return DB::table('contact_message_talent as cmt')
            ->join('contact_messages as cm', 'cm.id', '=', 'cmt.contact_message_id')
            ->select(
                'cmt.*',
                'cm.name as contacto',
                'cm.empresa',
                'cm.status',
                'cm.fecha_evento'
            )
            ->when($this->filters['status'] ?? null, fn ($q, $v) => $q->where('cm.status', $v))
            ->when($this->filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('cm.name', 'like', "%{$s}%")
                        ->orWhere('cm.email', 'like', "%{$s}%")
                        ->orWhere('cm.talent_name', 'like', "%{$s}%");
                });
            })
            ->when($this->filters['type'] ?? null, fn ($q, $v) => $q->where('cm.type', $v))
            ->when($this->filters['desde'] ?? null, fn ($q, $v) => $q->whereDate('cm.created_at', '>=', $v))
            ->when($this->filters['hasta'] ?? null, fn ($q, $v) => $q->whereDate('cm.created_at', '<=', $v))
            ->orderByDesc('cmt.contact_message_id')
            ->orderBy('cmt.orden')
            ->orderBy('cmt.id');
    }

    public function headings(): array
    {
        return [
            'ID solicitud', 'Contacto', 'Empresa', 'Estatus', 'Fecha del evento',
            'Talento', 'Honorarios', 'Incluye', 'Condiciones de pago', 'Orden',
        ];
    }

    public function map($row): array
    {
        return [
            $row->contact_message_id,
            $row->contacto,
            $row->empresa,
            ContactMessagesExport::STATUS[$row->status] ?? $row->status,
            $row->fecha_evento ? Carbon::parse($row->fecha_evento)->format('d/m/Y') : '',
            $row->nombre,
            $row->honorarios,
            $row->incluye,
            $row->condiciones_pago,
            $row->orden,
        ];
    }

    public function columnFormats(): array
    {
        return ['G' => '#,##0.00'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}