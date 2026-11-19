<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\HiringStatsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalMessages = ContactMessage::count();
        $monthMessages = ContactMessage::where('created_at', '>=', now()->startOfMonth())->count();
        $dailyAverage = round($monthMessages / max(1, now()->day), 1);

        // Periodo anterior: mismo tramo del mes pasado (para mensajes) y mes pasado completo (para el promedio)
        $prevMonthStart = now()->startOfMonth()->subMonthNoOverflow();
        $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();
        $prevSameSpanEnd = $prevMonthStart->copy()->addDays(now()->day - 1)->endOfDay()->min($prevMonthEnd);

        $prevMonthMessages = ContactMessage::whereBetween('created_at', [$prevMonthStart, $prevSameSpanEnd])->count();
        $prevMonthTotal = ContactMessage::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();
        $prevDailyAverage = round($prevMonthTotal / max(1, $prevMonthStart->daysInMonth), 1);

        $monthComparison = $this->compare($monthMessages, $prevMonthMessages, 'vs ' . $prevMonthMessages . ' al día ' . now()->day . ' del mes pasado');
        $averageComparison = $this->compare($dailyAverage, $prevDailyAverage, 'vs ' . $prevDailyAverage . ' promedio del mes pasado');
        // Nuevos mensajes (en espera de cotización), con scroll en la vista
        $recentInitialMessages = ContactMessage::where('status', 'en_espera_cotizacion')
            ->latest()
            ->take(30)
            ->get();

        // Mensajes por día (últimos 60: 30 actuales + 30 del periodo anterior; la vista filtra a 30/15/7)
        $messagesPerDay = ContactMessage::where('created_at', '>=', now()->subDays(59)->startOfDay())
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $dailyMessages = collect(range(59, 0))->map(function ($daysAgo) use ($messagesPerDay) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'date' => $date->format('d/m/Y'),
                'total' => (int) ($messagesPerDay[$date->format('Y-m-d')] ?? 0),
            ];
        });

        // Solicitudes de contratación (base de artistas y estados)
        $hirings = ContactMessage::where('type', 'contratacion')
            ->get(['talent_id', 'talent_name', 'estado_republica', 'status', 'cotizacion_final', 'created_at']);

        // Artistas más populares, con estadísticas para el modal
        $popularTalents = ContactMessage::select('talent_id', DB::raw('count(*) as total_mensajes'))
            ->where('type', 'contratacion')
            ->whereNotNull('talent_id')
            ->groupBy('talent_id')
            ->orderByDesc('total_mensajes')
            ->with('talent')
            ->limit(8)
            ->get()
            ->filter(fn ($row) => $row->talent !== null)
            ->values();

        $talentCards = $popularTalents->map(function ($row) use ($hirings) {
            $rows = $hirings->where('talent_id', $row->talent_id);

            return [
                'id' => $row->talent_id,
                'name' => $row->talent->name,
                'photo' => $row->talent->cover_image
                    ? Storage::url($row->talent->cover_image)
                    : asset('images/avatar-placeholder.png'),
                'total' => (int) $row->total_mensajes,
                'stats' => app(HiringStatsService::class)->summarize($rows, 'estado_republica'),
                'edit_url' => route('admin.talents.edit', $row->talent),
                'insights_url' => route('admin.talents.insights', $row->talent),
            ];
        });

        // Mapa: contactos por estado + estadísticas para el modal
        $svgIds = config('estados_mexico_svg_ids');
        $byState = $hirings->whereNotNull('estado_republica')->groupBy('estado_republica');

        $mexicoMapData = [];
        foreach ($svgIds as $estado => $svgId) {
            $rows = $byState->get($estado, collect());
            $top = $rows->whereNotNull('talent_name')->groupBy('talent_name')
                ->map->count()->sortDesc()->keys()->first();

            $mexicoMapData[$svgId] = [
                'name' => $estado,
                'count' => $rows->count(),
                'topTalent' => $top ?? 'Sin datos',
                'stats' => app(HiringStatsService::class)->summarize($rows, 'talent_name'),
            ];
        }

        return view('admin.dashboard', [
            'totalMessages' => $totalMessages,
            'monthMessages' => $monthMessages,
            'dailyAverage' => $dailyAverage,
            'monthComparison' => $monthComparison,
            'averageComparison' => $averageComparison,
            'recentInitialMessages' => $recentInitialMessages,
            'dailyMessages' => $dailyMessages,
            'talentCards' => $talentCards,
            'mexicoMapData' => $mexicoMapData,
        ]);
    }

    /** Compara un valor con su periodo anterior. */
    private function compare(float|int $current, float|int $previous, string $hint): array
    {
        $pct = $previous > 0 ? round(($current - $previous) / $previous * 100) : null;

        return [
            'pct' => $pct,
            'direction' => $current > $previous ? 'up' : ($current < $previous ? 'down' : 'flat'),
            'hint' => $hint,
        ];
    }
}
