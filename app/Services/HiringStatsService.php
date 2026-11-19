<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\EventContact;
use App\Models\Talent;
use Illuminate\Support\Collection;

class HiringStatsService
{
    /** Estadísticas de las solicitudes de contratación de un talento. */
    public function forTalent(Talent $talent): array
    {
        $rows = ContactMessage::where('type', 'contratacion')
            ->where('talent_id', $talent->id)
            ->get(['talent_id', 'talent_name', 'estado_republica', 'status', 'cotizacion_final', 'created_at']);

        return $this->summarize($rows, 'estado_republica');
    }

    /**
     * Estadísticas resumidas de un conjunto de solicitudes.
     * $topBy: campo para armar el "top" (estado para artistas, talento para estados).
     */
    public function summarize(Collection $rows, string $topBy): array
    {
        $labels = EventContact::STATUSES;

        return [
            'total' => $rows->count(),
            'last30' => $rows->where('created_at', '>=', now()->subDays(30))->count(),
            'closed' => $rows->whereIn('status', ['contrato_cerrado', 'contrato_pagado'])->count(),
            'paid' => (float) $rows->where('status', 'contrato_pagado')->sum('cotizacion_final'),
            'lastAt' => $rows->max('created_at')?->format('d/m/Y'),
            'statuses' => $rows->groupBy('status')
                ->map(fn ($g, $status) => ['label' => $labels[$status] ?? $status, 'count' => $g->count()])
                ->sortByDesc('count')->values()->all(),
            'tops' => $rows->whereNotNull($topBy)->groupBy($topBy)
                ->map(fn ($g, $name) => ['name' => $name, 'count' => $g->count()])
                ->sortByDesc('count')->take(5)->values()->all(),
        ];
    }
}
