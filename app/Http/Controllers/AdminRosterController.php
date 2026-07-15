<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRosterRequest;
use App\Models\Category;
use App\Models\Roster;
use App\Models\RosterTalent;
use App\Models\Talent;
use App\Models\LogoRoster;

class AdminRosterController extends Controller
{
    public function __construct(protected \App\Services\ImageUploadService $imageUploadService)
    {
    }

    public function index()
    {
        $rosters = Roster::withCount('rosterTalents')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.rosters.index', compact('rosters'));
    }

    public function create()
    {
        $talents = Talent::with('categories')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $logosRoster = LogoRoster::orderByDesc('id')->get();

        return view('admin.rosters.create', compact('talents', 'categories', 'logosRoster'));
    }

    public function store(StoreRosterRequest $request)
    {
        $data = $request->validated();
        $data['separar_por_categoria'] = $request->boolean('separar_por_categoria');
        $data['mostrar_honorarios'] = $request->boolean('mostrar_honorarios');
        $data['datos_contacto'] = $this->buildContacto($request);
        $data['logos'] = $request->input('selected_logos', []);

        $roster = Roster::create(collect($data)->except(['selected_logos', 'talents', 'contacto_tipo', 'contacto_valor'])->toArray());

        $this->syncTalents($request, $roster);

        return redirect()->route('admin.rosters.index')->with('status', 'Roster creado correctamente.');
    }

    public function edit(Roster $roster)
    {
        $talents = Talent::with('categories')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $logosRoster = LogoRoster::orderByDesc('id')->get();
        $roster->load('rosterTalents.talent');

        return view('admin.rosters.edit', compact('roster', 'talents', 'categories', 'logosRoster'));
    }

    public function update(StoreRosterRequest $request, Roster $roster)
    {
        $data = $request->validated();
        $data['separar_por_categoria'] = $request->boolean('separar_por_categoria');
        $data['mostrar_honorarios'] = $request->boolean('mostrar_honorarios');
        $data['datos_contacto'] = $this->buildContacto($request);
        $data['logos'] = $request->input('selected_logos', []);

        $roster->update(collect($data)->except(['selected_logos', 'talents', 'contacto_tipo', 'contacto_valor'])->toArray());

        $this->syncTalents($request, $roster);

        return redirect()->route('admin.rosters.index')->with('status', 'Roster actualizado correctamente.');
    }

    public function destroy(Roster $roster)
    {
        $roster->delete();

        return redirect()->route('admin.rosters.index')->with('status', 'Roster eliminado correctamente.');
    }


    protected function buildContacto(\Illuminate\Http\Request $request): array
    {
        $tipos = $request->input('contacto_tipo', []);
        $valores = $request->input('contacto_valor', []);

        $contacto = [];
        foreach ($tipos as $index => $tipo) {
            if (! empty($valores[$index])) {
                $contacto[] = ['tipo' => $tipo, 'valor' => $valores[$index]];
            }
        }

        return $contacto;
    }

    protected function syncTalents(\Illuminate\Http\Request $request, Roster $roster): void
    {
        $selectedIds = $request->input('talents', []);

        // Elimina los que ya no están seleccionados
        $roster->rosterTalents()->whereNotIn('talent_id', $selectedIds)->delete();

        // Agrega los nuevos (los existentes no se tocan, para no perder ediciones de nombre/resumen/honorarios)
        $existingIds = $roster->rosterTalents()->pluck('talent_id')->toArray();
        $newIds = array_diff($selectedIds, $existingIds);

        $order = $roster->rosterTalents()->max('orden') ?? 0;

        foreach ($newIds as $talentId) {
            $talent = Talent::find($talentId);
            if (! $talent) {
                continue;
            }
            $order++;
            RosterTalent::create([
                'roster_id' => $roster->id,
                'talent_id' => $talent->id,
                'nombre' => $talent->name,
                'resumen_corto' => $talent->summary,
                'honorarios' => $talent->honorarios_default,
                'orden' => $order,
            ]);
        }
    }
}