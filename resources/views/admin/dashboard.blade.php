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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">

    {{-- Artistas más populares --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Artistas más populares</h3>
            <p class="text-xs opacity-60 mb-3">Con más solicitudes de contratación recibidas</p>

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Talento</th>
                            <th class="text-right">Solicitudes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($popularTalents as $index => $row)
                            <tr>
                                <td class="opacity-60">{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.talents.edit', $row->talent) }}" class="link link-hover">
                                        {{ $row->talent->name }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <span class="badge badge-primary badge-sm">{{ $row->total_mensajes }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center opacity-60 py-4">Aún no hay datos suficientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ventas de los últimos 3 meses --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Ventas de los últimos 3 meses</h3>
            <p class="text-xs opacity-60 mb-3">Basado en mensajes marcados como "Contrato pagado"</p>
            <canvas id="salesChart" height="140"></canvas>
        </div>
    </div>

</div>

{{-- Mapa de contactos por estado (SVG real, simplemaps.com) --}}
<div class="card bg-base-100 border border-base-300 mt-4">
    <div class="card-body">
        <h3 class="font-semibold mb-1">Contactos de contratación por estado</h3>
        <p class="text-xs opacity-60 mb-3">El color indica el número de contactos recibidos. Pasa el mouse sobre un estado para ver el detalle y el artista más popular.</p>

        @include('admin.partials.mexico-map')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesByMonth->pluck('label')) !!},
            datasets: [{
                label: 'Ventas (MXN)',
                data: {!! json_encode($salesByMonth->pluck('total')) !!},
                backgroundColor: '#DCA54A',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { ticks: { color: '#a3a3a3' }, grid: { color: '#333333' } },
                x: { ticks: { color: '#a3a3a3' }, grid: { display: false } }
            }
        }
    });
</script>
@endsection