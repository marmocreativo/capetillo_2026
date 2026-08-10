@extends('layouts.admin')

@section('admin-content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">Eventos</h1>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Nuevo evento</a>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por título o slug..." class="input input-bordered w-full sm:max-w-xs">
    <select name="status" class="select select-bordered">
        <option value="">Todos los estados</option>
        <option value="active" @selected(request('status') === 'active')>Activos</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
    </select>
    <button type="submit" class="btn btn-neutral">Filtrar</button>
</form>

@if (session('status'))
    <div class="alert alert-success mb-4">{{ session('status') }}</div>
@endif

<div class="hidden lg:block overflow-x-auto bg-base-100 rounded-box shadow">
    <table class="table">
        <thead>
            <tr>
                <th>Portada</th>
                <th>Título</th>
                <th>Slug / URL</th>
                <th>Orden</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <td>
                        @if ($event->cover_image)
                            <img src="{{ Storage::url($event->cover_image) }}" class="w-14 h-14 object-cover rounded">
                        @else
                            <div class="w-14 h-14 bg-base-300 rounded"></div>
                        @endif
                    </td>
                    <td class="font-semibold">{{ $event->title }}</td>
                    <td class="text-xs opacity-70">/{{ $event->slug }}</td>
                    <td>{{ $event->orden }}</td>
                    <td>
                        <span class="badge {{ $event->is_active ? 'badge-success' : 'badge-ghost' }}">
                            {{ $event->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn btn-sm btn-ghost">Ver</a>
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-ghost">Editar</a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('¿Eliminar este evento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error btn-outline">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center opacity-60 py-6">No hay eventos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="lg:hidden flex flex-col gap-3">
    @forelse ($events as $event)
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    @if ($event->cover_image)
                        <img src="{{ Storage::url($event->cover_image) }}" class="w-16 h-16 object-cover rounded">
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $event->title }}</p>
                        <p class="text-xs opacity-70 truncate">/{{ $event->slug }}</p>
                        <span class="badge badge-sm {{ $event->is_active ? 'badge-success' : 'badge-ghost' }} mt-1">
                            {{ $event->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-ghost flex-1">Editar</a>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('¿Eliminar este evento?');" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-error btn-outline w-full">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center opacity-60 py-6">No hay eventos registrados.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection