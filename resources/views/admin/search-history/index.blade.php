@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Historial de búsquedas</h1>

<div class="card bg-base-100 shadow p-6 mb-6">
    <h2 class="font-bold mb-4">Búsquedas más repetidas ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})</h2>

    <div class="hidden lg:block overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Búsqueda</th>
                    <th>Veces buscada</th>
                    <th>Promedio de resultados</th>
                    <th>Veces sin resultados</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topSearches as $row)
                    <tr>
                        <td>{{ $row->ejemplo }}</td>
                        <td>{{ $row->total }}</td>
                        <td>{{ number_format($row->promedio_resultados, 1) }}</td>
                        <td>
                            @if ($row->sin_resultados > 0)
                                <span class="badge badge-warning">{{ $row->sin_resultados }}</span>
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center opacity-60 py-4">Sin datos en este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden flex flex-col gap-2">
        @forelse ($topSearches as $row)
            <div class="flex justify-between border-b border-base-300 py-2 text-sm">
                <span>{{ $row->ejemplo }}</span>
                <span class="font-semibold">{{ $row->total }}×</span>
            </div>
        @empty
            <p class="text-center opacity-60 py-4">Sin datos en este periodo.</p>
        @endforelse
    </div>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar término..." class="input input-bordered w-full sm:max-w-xs">
    <input type="date" name="from" value="{{ request('from') }}" class="input input-bordered">
    <input type="date" name="to" value="{{ request('to') }}" class="input input-bordered">
    <button type="submit" class="btn btn-neutral">Filtrar</button>
</form>

@if (session('status'))
    <div class="alert alert-success mb-4">{{ session('status') }}</div>
@endif

<form action="{{ route('admin.search-history.bulk-destroy') }}" method="POST" onsubmit="return confirm('¿Eliminar los registros seleccionados?');">
    @csrf
    @method('DELETE')

    <div class="hidden lg:block overflow-x-auto bg-base-100 rounded-box shadow">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="checkbox" onclick="document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked)"></th>
                    <th>Búsqueda</th>
                    <th>Resultados</th>
                    <th>IP</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($searches as $item)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="checkbox row-check"></td>
                        <td>{{ $item->query }}</td>
                        <td>
                            @if ($item->results_count === 0)
                                <span class="badge badge-warning">0</span>
                            @else
                                {{ $item->results_count }}
                            @endif
                        </td>
                        <td class="text-xs opacity-70">{{ $item->ip_address }}</td>
                        <td class="text-xs opacity-70">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center opacity-60 py-6">No hay búsquedas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden flex flex-col gap-3">
        @forelse ($searches as $item)
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 flex-row items-center gap-3">
                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="checkbox row-check">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold truncate">{{ $item->query }}</p>
                        <p class="text-xs opacity-70">{{ $item->results_count }} resultado(s) · {{ $item->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center opacity-60 py-6">No hay búsquedas registradas.</p>
        @endforelse
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-error btn-outline">Eliminar seleccionados</button>
    </div>
</form>

<div class="mt-6">
    {{ $searches->links() }}
</div>
@endsection