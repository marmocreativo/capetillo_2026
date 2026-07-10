@extends('layouts.admin')

@section('admin-content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold">Rosters</h1>
    <a href="{{ route('admin.rosters.create') }}" class="btn btn-primary">Nuevo roster</a>
</div>

@if (session('status'))
    <div class="alert alert-success mb-6">{{ session('status') }}</div>
@endif

{{-- Tabla — solo desktop --}}
<div class="overflow-x-auto bg-base-100 rounded-box shadow hidden lg:block">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Talentos por página</th>
                <th>Separado por categoría</th>
                <th>No. de talentos</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rosters as $roster)
                <tr>
                    <td>{{ $roster->name }}</td>
                    <td>{{ $roster->talentos_por_pagina === 0 ? 'Lista' : $roster->talentos_por_pagina }}</td>
                    <td>
                        <span class="badge {{ $roster->separar_por_categoria ? 'badge-success' : 'badge-ghost' }}">
                            {{ $roster->separar_por_categoria ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $roster->roster_talents_count }}</td>
                    <td class="text-right">
                        <button
                            type="button"
                            class="btn btn-xs bg-[#262626] border-[#DCA54A] text-[#DCA54A]"
                            onclick="navigator.clipboard.writeText('{{ route('roster.public', $roster->public_token) }}'); this.innerText='¡Copiado!'; setTimeout(() => this.innerText='Copiar link', 1500)"
                        >
                            Copiar link
                        </button>
                        <a href="{{ route('admin.rosters.export', $roster) }}" class="btn btn-xs btn-secondary">Exportar PPTX</a>
                        <a href="{{ route('admin.rosters.edit', $roster) }}" class="btn btn-xs">Editar</a>
                        <form action="{{ route('admin.rosters.destroy', $roster) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este roster?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 opacity-60">Aún no hay rosters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Cards — solo móvil --}}
<div class="flex flex-col gap-3 lg:hidden">
    @forelse ($rosters as $roster)
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4 gap-2">
                <p class="font-medium truncate">{{ $roster->name }}</p>

                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="badge badge-sm badge-outline">
                        {{ $roster->talentos_por_pagina === 0 ? 'Lista' : $roster->talentos_por_pagina . ' por página' }}
                    </span>
                    <span class="badge badge-sm {{ $roster->separar_por_categoria ? 'badge-success' : 'badge-ghost' }}">
                        {{ $roster->separar_por_categoria ? 'Separado por categoría' : 'Sin separar' }}
                    </span>
                    <span class="opacity-60">{{ $roster->roster_talents_count }} talento(s)</span>
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.rosters.edit', $roster) }}" class="btn btn-xs flex-1">Editar</a>
                        <a href="{{ route('admin.rosters.export', $roster) }}" class="btn btn-xs btn-secondary flex-1">Exportar PPTX</a>
                        <form action="{{ route('admin.rosters.destroy', $roster) }}" method="POST" onsubmit="return confirm('¿Eliminar este roster?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                        </form>
                    </div>

                    <button
                        type="button"
                        class="btn btn-xs w-full bg-[#262626] border-[#DCA54A] text-[#DCA54A]"
                        onclick="navigator.clipboard.writeText('{{ route('roster.public', $roster->public_token) }}'); this.innerText='¡Copiado!'; setTimeout(() => this.innerText='Copiar link', 1500)"
                    >
                        Copiar link
                    </button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center py-6 opacity-60">Aún no hay rosters.</p>
    @endforelse
</div>

<div class="mt-4">
    {{ $rosters->links() }}
</div>
@endsection