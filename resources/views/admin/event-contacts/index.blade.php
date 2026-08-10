@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Contactos de eventos</h1>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..." class="input input-bordered w-full sm:max-w-xs">
    <select name="status" class="select select-bordered">
        <option value="">Todos los estados</option>
        @foreach (\App\Models\EventContact::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
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
                <th>Nombre</th>
                <th>Evento</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha aprox.</th>
                <th>Estado</th>
                <th>Recibido</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr class="hover cursor-pointer" onclick="window.location='{{ route('admin.event-contacts.show', $contact) }}'">
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->event?->title }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->phone ?: '—' }}</td>
                    <td>{{ optional($contact->fecha_aproximada)->format('d/m/Y') ?: '—' }}</td>
                    <td><span class="badge badge-sm">{{ \App\Models\EventContact::STATUSES[$contact->status] ?? $contact->status }}</span></td>
                    <td class="text-xs opacity-70">{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right" onclick="event.stopPropagation()">
                        <form action="{{ route('admin.event-contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('¿Eliminar este contacto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error btn-outline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center opacity-60 py-6">No hay contactos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="lg:hidden flex flex-col gap-3">
    @forelse ($contacts as $contact)
        <a href="{{ route('admin.event-contacts.show', $contact) }}" class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <p class="font-semibold">{{ $contact->name }}</p>
                <p class="text-xs opacity-70">{{ $contact->event?->title }} · {{ $contact->email }}</p>
                <span class="badge badge-sm mt-1">{{ \App\Models\EventContact::STATUSES[$contact->status] ?? $contact->status }}</span>
            </div>
        </a>
    @empty
        <p class="text-center opacity-60 py-6">No hay contactos registrados.</p>
    @endforelse
</div>

<div class="mt-6">{{ $contacts->links() }}</div>
@endsection