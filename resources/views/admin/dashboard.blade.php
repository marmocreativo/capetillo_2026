@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-1">Dashboard</h1>
<p class="opacity-60 mb-6">Bienvenido, {{ auth()->user()->name }}.</p>

{{-- Tarjetas de métricas principales --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-4">
            <p class="text-xs opacity-60">Talentos totales</p>
            <p class="text-3xl font-bold">{{ $totalTalents }}</p>
            <p class="text-xs opacity-60 mt-1">
                <span class="text-success">{{ $activeTalents }} activos</span>
                ·
                <span class="text-error">{{ $inactiveTalents }} inactivos</span>
            </p>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-4">
            <p class="text-xs opacity-60">Categorías</p>
            <p class="text-3xl font-bold">{{ $totalCategories }}</p>
            <p class="text-xs opacity-60 mt-1">{{ $activeCategories }} activas</p>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-4">
            <p class="text-xs opacity-60">Sin biografía</p>
            <p class="text-3xl font-bold {{ $talentsWithoutContent > 0 ? 'text-warning' : '' }}">{{ $talentsWithoutContent }}</p>
            <a href="{{ route('admin.talents.index') }}" class="text-xs link link-hover mt-1 inline-block">Ver talentos</a>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-4">
            <p class="text-xs opacity-60">Sin galería de imágenes</p>
            <p class="text-3xl font-bold {{ $talentsWithoutImages > 0 ? 'text-warning' : '' }}">{{ $talentsWithoutImages }}</p>
            <a href="{{ route('admin.talents.index') }}" class="text-xs link link-hover mt-1 inline-block">Ver talentos</a>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Categorías con más talentos --}}
    <div class="card bg-base-100 border border-base-300 lg:col-span-1">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Categorías con más talentos</h3>

            @forelse ($categoriesWithCounts as $category)
                <div class="flex items-center justify-between py-1.5 border-b border-base-300 last:border-0">
                    <a href="{{ route('admin.talents.index', ['category' => $category->slug]) }}" class="link link-hover text-sm">
                        {{ $category->name }}
                    </a>
                    <span class="badge badge-sm">{{ $category->talents_count }}</span>
                </div>
            @empty
                <p class="text-sm opacity-60">Aún no hay categorías.</p>
            @endforelse
        </div>
    </div>

    {{-- Talentos recientes --}}
    <div class="card bg-base-100 border border-base-300 lg:col-span-2">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Talentos agregados recientemente</h3>

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categorías</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTalents as $talent)
                            <tr>
                                <td>{{ $talent->name }}</td>
                                <td>
                                    @foreach ($talent->categories->take(2) as $category)
                                        <span class="badge badge-xs badge-outline">{{ $category->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($talent->is_active)
                                        <span class="badge badge-success badge-sm">Activo</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.talents.edit', $talent) }}" class="btn btn-xs">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 opacity-60">Aún no hay talentos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection