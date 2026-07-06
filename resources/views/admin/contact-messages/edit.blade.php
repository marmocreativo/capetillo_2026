@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center gap-3 mb-1">
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
    <h1 class="text-2xl font-bold">Detalle del mensaje</h1>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4 mt-4 text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">

    {{-- Datos del contacto (solo lectura) --}}
    <div class="card bg-base-100 border border-base-300 lg:col-span-2">
        <div class="card-body">
            <h3 class="font-semibold mb-3">
                Datos recibidos
                @if ($contactMessage->type === 'contratacion')
                    <span class="badge badge-primary badge-sm ml-2">Contratación</span>
                @else
                    <span class="badge badge-ghost badge-sm ml-2">General</span>
                @endif
            </h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="opacity-60 text-xs">Nombre</dt>
                    <dd>{{ $contactMessage->name }}</dd>
                </div>
                <div>
                    <dt class="opacity-60 text-xs">Correo</dt>
                    <dd><a href="mailto:{{ $contactMessage->email }}" class="link link-hover">{{ $contactMessage->email }}</a></dd>
                </div>
                <div>
                    <dt class="opacity-60 text-xs">Teléfono</dt>
                    <dd>{{ $contactMessage->phone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="opacity-60 text-xs">Talento de interés</dt>
                    <dd>
                        @if ($contactMessage->talent)
                            <a href="{{ route('admin.talents.edit', $contactMessage->talent) }}" class="link link-hover">
                                {{ $contactMessage->talent_name }}
                            </a>
                        @else
                            {{ $contactMessage->talent_name ?: '—' }}
                        @endif
                    </dd>
                </div>

                @if ($contactMessage->type === 'contratacion')
                    <div>
                        <dt class="opacity-60 text-xs">Estado de la república</dt>
                        <dd>{{ $contactMessage->estado_republica ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="opacity-60 text-xs">Aforo esperado</dt>
                        <dd>{{ $contactMessage->aforo_esperado ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="opacity-60 text-xs">Venue / Lugar</dt>
                        <dd>{{ $contactMessage->venue ?: '—' }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="opacity-60 text-xs">Fecha de recepción</dt>
                    <dd>{{ $contactMessage->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            <div class="divider"></div>

            <dt class="opacity-60 text-xs mb-1">Mensaje</dt>
            <dd class="whitespace-pre-line text-sm">{{ $contactMessage->message }}</dd>
        </div>
    </div>

    {{-- Gestión: estatus y cotización --}}
    <div class="card bg-base-100 border border-base-300 lg:col-span-1">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Gestión del mensaje</h3>

            <form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}" class="flex flex-col gap-3">
                @csrf
                @method('PUT')

                <div class="form-control">
                    <label class="label"><span class="label-text">Estatus</span></label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="contacto_inicial" @selected($contactMessage->status === 'contacto_inicial')>Contacto inicial</option>
                        <option value="seguimiento" @selected($contactMessage->status === 'seguimiento')>Seguimiento</option>
                        <option value="contrato_cerrado" @selected($contactMessage->status === 'contrato_cerrado')>Contrato cerrado</option>
                        <option value="contrato_pagado" @selected($contactMessage->status === 'contrato_pagado')>Contrato pagado</option>
                    </select>
                    @error('status')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Cotización final (MXN)</span></label>
                    <input type="number" step="0.01" min="0" name="cotizacion_final"
                           value="{{ old('cotizacion_final', $contactMessage->cotizacion_final) }}"
                           class="input input-bordered w-full" placeholder="0.00">
                    @error('cotizacion_final')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-2">Guardar cambios</button>
            </form>

            <div class="divider"></div>

            <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}"
                  onsubmit="return confirm('¿Eliminar este mensaje? Esta acción no se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-outline btn-sm w-full">Eliminar mensaje</button>
            </form>
        </div>
    </div>

</div>
@endsection