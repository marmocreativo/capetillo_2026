<?php

namespace App\Http\Controllers;

use App\Models\Roster;

class PublicRosterController extends Controller
{
    public function show(Roster $roster)
    {
        $roster->load(['rosterTalents' => function ($query) {
            $query->with('talent.categories')->orderBy('orden');
        }]);

        $talents = $roster->rosterTalents->map(function ($entry) {
            $category = $entry->talent?->categories->sortBy('name')->first();

            $url = null;
            if ($entry->talent && $category) {
                $url = route('talents.show', [
                    'category' => $category->slug,
                    'talent' => $entry->talent->slug,
                ]);
            }

            return [
                'nombre' => $entry->nombre ?? $entry->talent?->name,
                'resumen' => $entry->resumen_corto,
                'honorarios_formatted' => '$' . number_format((float) ($entry->honorarios ?? 0), 2) . ' MXN',
                'imagen' => $entry->talent?->cover_image ? asset('storage/' . $entry->talent->cover_image) : null,
                'categoria' => $category?->name ?? 'Sin categoría',
                'url' => $url,
            ];
        })->values();

        $payload = [
            'name' => $roster->name,
            'intro_text' => $roster->intro_text,
            'outro_text' => $roster->outro_text,
            'mostrar_honorarios' => $roster->mostrar_honorarios,
            'separar_por_categoria' => $roster->separar_por_categoria,
            'default_mode' => $roster->talentos_por_pagina,
            'logos' => collect($roster->logos ?? [])->map(fn ($logo) => asset('storage/' . $logo))->values(),
            'contacto' => $roster->datos_contacto ?? [],
            'talents' => $talents,
        ];

        return view('public.rosters.show', [
            'roster' => $roster,
            'payload' => $payload,
        ]);
    }
}