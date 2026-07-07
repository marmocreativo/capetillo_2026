<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Talent;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalTalents = Talent::count();
        $activeTalents = Talent::where('is_active', true)->count();
        $inactiveTalents = $totalTalents - $activeTalents;

        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        $talentsWithoutContent = Talent::where(function ($q) {
            $q->whereNull('content')->orWhere('content', '');
        })->count();

        $talentsWithoutSummary = Talent::where(function ($q) {
            $q->whereNull('summary')->orWhere('summary', '');
        })->count();

        $talentsWithoutImages = Talent::doesntHave('images')->count();

        $categoriesWithCounts = Category::withCount('talents')
            ->orderByDesc('talents_count')
            ->take(6)
            ->get();

        $recentTalents = Talent::with('categories')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

            // Artistas más populares (por solicitudes de contratación recibidas)
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

        // Ventas de los últimos 3 meses (mensajes marcados como "contrato_pagado")
        $salesByMonth = collect(range(2, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            $total = ContactMessage::where('status', 'contrato_pagado')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('cotizacion_final');

            $count = ContactMessage::where('status', 'contrato_pagado')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();

            return [
                'label' => $date->translatedFormat('M Y'),
                'total' => (float) $total,
                'count' => $count,
            ];
        });

        // Contactos de contratación por estado
        $contactsByState = ContactMessage::select('estado_republica', DB::raw('count(*) as total'))
            ->where('type', 'contratacion')
            ->whereNotNull('estado_republica')
            ->groupBy('estado_republica')
            ->pluck('total', 'estado_republica');

        // Talento más popular por estado
        $topTalentByState = ContactMessage::select('estado_republica', 'talent_name', DB::raw('count(*) as total'))
            ->where('type', 'contratacion')
            ->whereNotNull('estado_republica')
            ->whereNotNull('talent_name')
            ->groupBy('estado_republica', 'talent_name')
            ->orderByDesc('total')
            ->get()
            ->groupBy('estado_republica')
            ->map(fn ($rows) => $rows->first()->talent_name);
        
        // Datos para el mapa SVG real de México
        $svgIds = config('estados_mexico_svg_ids');
        $mexicoMapData = [];
        foreach ($svgIds as $estado => $svgId) {
            $mexicoMapData[$svgId] = [
                'name' => $estado,
                'count' => $contactsByState[$estado] ?? 0,
                'topTalent' => $topTalentByState[$estado] ?? 'Sin datos',
            ];
        }

        return view('admin.dashboard', compact(
            'totalTalents',
            'activeTalents',
            'inactiveTalents',
            'totalCategories',
            'activeCategories',
            'talentsWithoutContent',
            'talentsWithoutSummary',
            'talentsWithoutImages',
            'categoriesWithCounts',
            'recentTalents',
            'popularTalents',
            'salesByMonth',
            'contactsByState',
            'topTalentByState',
            'mexicoMapData'
        ));
    }
}