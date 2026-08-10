@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Contacto de {{ $eventContact->name }}</h1>
    <a href="{{ route('admin.event-contacts.index') }}" class="btn btn-ghost btn-sm">Volver</a>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card bg-base-100 shadow p-6">
        <h2 class="font-bold mb-4">Datos del contacto</h2>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="opacity-60">Evento</dt>
                <dd class="font-medium">{{ $eventContact->event?->title ?? '—' }}</dd>
            </div>
            <div>
                <dt class="opacity-60">Fecha aproximada del evento</dt>
                <dd class="font-medium">{{ optional($eventContact->fecha_aproximada)->format('d/m/Y') ?: 'Por confirmar' }}</dd>
            </div>
            <div>
                <dt class="opacity-60">Correo</dt>
                <dd class="font-medium">{{ $eventContact->email }}</dd>
            </div>
            <div>
                <dt class="opacity-60">Teléfono</dt>
                <dd class="font-medium">{{ $eventContact->phone ?: '—' }}</dd>
            </div>
            <div>
                <dt class="opacity-60">Recibido</dt>
                <dd class="font-medium">{{ $eventContact->created_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="divider"></div>

        <dt class="opacity-60 text-sm mb-1">Mensaje</dt>
        <p class="whitespace-pre-line">{{ $eventContact->message }}</p>
    </div>

    <div class="card bg-base-100 shadow p-6">
        <h2 class="font-bold mb-4">Seguimiento</h2>

        <form action="{{ route('admin.event-contacts.update', $eventContact) }}" method="POST">
            @csrf
            @method('PUT')

            <label class="label"><span class="label-text">Estado</span></label>
            <select name="status" class="select select-bordered w-full">
                @foreach (\App\Models\EventContact::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected($eventContact->status === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary btn-block mt-4">Actualizar estado</button>
        </form>
    </div>
</div>
@endsection