@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center justify-between mb-1">
    <h1 class="text-2xl font-bold">Mensajes de contacto</h1>
</div>
<p class="opacity-60 mb-6">Consultas generales y solicitudes de contratación recibidas desde el sitio público.</p>

@if (session('status'))
    <div class="alert alert-success mb-4 text-sm">{{ session('status') }}</div>
@endif

<div class="card bg-base-100 border border-base-300 mb-6">
    <div class="card-body p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="form-control flex-1 min-w-[200px]">
                <label class="label"><span class="label-text text-xs">Buscar</span></label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, correo o talento"
                       class="input input-bordered input-sm w-full">
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-xs">Tipo</span></label>
                <select name="type" class="select select-bordered select-sm">
                    <option value="">Todos</option>
                    <option value="general" @selected(request('type') === 'general')>General</option>
                    <option value="contratacion" @selected(request('type') === 'contratacion')>Contratación</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-xs">Estatus</span></label>
                <select name="status" class="select select-bordered select-sm">
                    <option value="">Todos</option>
                    <option value="contacto_inicial" @selected(request('status') === 'contacto_inicial')>Contacto inicial</option>
                    <option value="seguimiento" @selected(request('status') === 'seguimiento')>Seguimiento</option>
                    <option value="contrato_cerrado" @selected(request('status') === 'contrato_cerrado')>Contrato cerrado</option>
                    <option value="contrato_pagado" @selected(request('status') === 'contrato_pagado')>Contrato pagado</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-ghost btn-sm">Limpiar</a>
        </form>
    </div>
</div>

<div class="card bg-base-100 border border-base-300">
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Talento</th>
                        <th>Estatus</th>
                        <th>Cotización</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td class="text-xs whitespace-nowrap">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($message->type === 'contratacion')
                                    <span class="badge badge-primary badge-sm">Contratación</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">General</span>
                                @endif
                            </td>
                            <td>{{ $message->name }}</td>
                            <td class="text-xs">
                                <div>{{ $message->email }}</div>
                                @if ($message->phone)
                                    <div class="opacity-60">{{ $message->phone }}</div>
                                @endif
                            </td>
                            <td>{{ $message->talent_name ?: '—' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'contacto_inicial' => 'badge-info',
                                        'seguimiento' => 'badge-warning',
                                        'contrato_cerrado' => 'badge-success',
                                        'contrato_pagado' => 'badge-success',
                                    ];
                                    $statusLabels = [
                                        'contacto_inicial' => 'Contacto inicial',
                                        'seguimiento' => 'Seguimiento',
                                        'contrato_cerrado' => 'Contrato cerrado',
                                        'contrato_pagado' => 'Contrato pagado',
                                    ];
                                @endphp
                                <span class="badge badge-sm {{ $statusColors[$message->status] ?? 'badge-ghost' }}">
                                    {{ $statusLabels[$message->status] ?? $message->status }}
                                </span>
                            </td>
                            <td class="text-xs">
                                {{ $message->cotizacion_final ? '$' . number_format($message->cotizacion_final, 2) : '—' }}
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.contact-messages.edit', $message) }}" class="btn btn-xs">Ver / Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 opacity-60">Aún no hay mensajes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $messages->links() }}
</div>
@endsection