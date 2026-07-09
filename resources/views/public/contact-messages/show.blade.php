@extends('layouts.public')

@section('title', 'Seguimiento de solicitud | ' . config('app.name'))

@section('public-content')

@php
    $statusLabels = [
        'contacto_inicial' => 'Contacto inicial',
        'procesando' => 'Procesando',
        'venta_no_concluida' => 'Venta no concluida',
        'cotizacion_completa' => 'Cotización completa',
        'contrato_cerrado' => 'Contrato cerrado',
        'contrato_pagado' => 'Contrato pagado',
    ];

    $tiposEvento = [
        'privado' => 'Privado',
        'corporativo' => 'Corporativo',
        'publico_masivo' => 'Público / Masivo',
        'social' => 'Social (boda / XV)',
        'gubernamental' => 'Gubernamental',
    ];

    $showCotizacion = in_array($contactMessage->status, ['cotizacion_completa', 'contrato_cerrado', 'contrato_pagado'], true);
    $isVentaNoConcluida = $contactMessage->status === 'venta_no_concluida';
@endphp

<div class="max-w-4xl mx-auto p-2">

    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>

    <div class="my-8">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <p class="text-xs uppercase tracking-widest opacity-60 mb-1">Capetillo Producciones</p>
                <h1 class="text-2xl md:text-3xl font-bold">Solicitud de Cotización</h1>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-xs uppercase tracking-widest opacity-60 mb-1">No. de cotización</p>
                <p class="text-xl font-bold text-primary">#{{ str_pad($contactMessage->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-6 text-sm">{{ session('status') }}</div>
        @endif

        @error('empresa')
            <div class="alert alert-error mb-6 text-sm">Revisa los datos marcados abajo, hay campos con errores.</div>
        @enderror

        {{-- ESTADO DE SEGUIMIENTO --}}
        <div class="card bg-base-100/80 backdrop-lg shadow mb-6">
            <div class="card-body p-5">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest opacity-60 mb-1">Estado actual</p>
                        <span class="badge badge-primary badge-lg font-semibold">
                            {{ $statusLabels[$contactMessage->status] ?? $contactMessage->status }}
                        </span>
                    </div>
                    @if ($contactMessage->cotizacion_final && $showCotizacion)
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-widest opacity-60 mb-1">Precio cotizado</p>
                            <p class="text-2xl font-bold text-primary">${{ number_format((float) $contactMessage->cotizacion_final, 2) }} MXN</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($isVentaNoConcluida)

            {{-- VISTA MÍNIMA: venta no concluida --}}
            <div class="card bg-base-100/80 backdrop-lg shadow mb-4 overflow-hidden">
                <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-primary">Datos del contacto</h2>
                </div>
                <div class="card-body p-5">
                    <div class="mb-4">
                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Nombre del contacto</p>
                        <p class="font-medium">{{ $contactMessage->name ?: '—' }}</p>
                    </div>
                    <div class="divider my-1"></div>
                    <p class="text-xs uppercase tracking-wide opacity-50 mb-1">Mensaje</p>
                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ $contactMessage->message }}</p>
                </div>
            </div>

        @else

            {{-- TABS --}}
            <div x-data="{ tab: '{{ $showCotizacion ? 'cotizacion' : 'datos' }}' }">

                <div role="tablist" class="tabs tabs-lifted">
                    @if ($showCotizacion)
                        <a role="tab" class="tab" :class="tab === 'cotizacion' && 'tab-active'" @click="tab = 'cotizacion'">Cotización</a>
                    @endif
                    <a role="tab" class="tab" :class="tab === 'datos' && 'tab-active'" @click="tab = 'datos'">Mis datos</a>
                </div>

                <div class="bg-base-100/80 backdrop-lg border border-base-300 border-t-0 rounded-b-box p-5">

                    {{-- TAB: COTIZACIÓN (misma estructura que el PPTX: evento, talentos, requerimientos, cierre) --}}
                    @if ($showCotizacion)
                        <div x-show="tab === 'cotizacion'" x-cloak class="flex flex-col gap-4">

                            {{-- DATOS DEL EVENTO --}}
                            <div class="border border-base-content/10 rounded-box overflow-hidden">
                                <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                    <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Datos del evento</h3>
                                </div>
                                <div class="p-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Cliente</p>
                                            <p class="font-medium">{{ $contactMessage->name ?: '—' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Fecha</p>
                                            <p class="font-medium">{{ $contactMessage->fecha_evento?->format('d/m/Y') ?? 'Por confirmar' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Ciudad</p>
                                            <p class="font-medium">{{ $contactMessage->ciudad ?: $contactMessage->estado_republica ?: '—' }}</p>
                                        </div>
                                    </div>
                                    @if ($contactMessage->venue)
                                        <div class="divider my-3"></div>
                                        <p class="text-sm opacity-80">{{ $contactMessage->venue }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- TALENTOS / INVERSIÓN --}}
                            @if ($contactMessage->cotizacionTalents->isNotEmpty())
                                <div class="border border-base-content/10 rounded-box overflow-hidden">
                                    <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5 flex items-center justify-between">
                                        <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Inversión</h3>
                                        @if ($contactMessage->fecha_vigencia)
                                            <span class="text-xs opacity-70">Vigente hasta {{ $contactMessage->fecha_vigencia->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    <div class="p-5 flex flex-col gap-5">
                                        @foreach ($contactMessage->cotizacionTalents as $entry)
                                            <div class="border border-base-content/10 rounded-box overflow-hidden bg-base-100">
                                                <div class="grid grid-cols-1 sm:grid-cols-3">
                                                    @if ($entry->imagen)
                                                        <div class="sm:col-span-1 h-48 sm:h-full bg-base-300">
                                                            <img src="{{ Storage::url($entry->imagen) }}" alt="{{ $entry->nombre }}" class="w-full h-full object-cover">
                                                        </div>
                                                    @endif

                                                    <div class="{{ $entry->imagen ? 'sm:col-span-2' : 'sm:col-span-3' }} p-5">
                                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-4">
                                                            <p class="text-lg font-bold text-primary">{{ $entry->nombre }}</p>
                                                            @if ($entry->honorarios)
                                                                <div class="bg-base-300/60 rounded-box px-4 py-2 text-center sm:text-right shrink-0">
                                                                    <p class="text-[10px] uppercase tracking-widest opacity-60">Presentación en vivo</p>
                                                                    <p class="text-xl font-bold">${{ number_format((float) $entry->honorarios, 2) }} MXN</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if ($entry->incluye)
                                                            <div class="mb-3">
                                                                <p class="text-xs uppercase tracking-wide font-semibold text-primary mb-1">Incluye</p>
                                                                <p class="text-sm leading-relaxed whitespace-pre-line opacity-90">{{ $entry->incluye }}</p>
                                                            </div>
                                                        @endif

                                                        @if ($entry->condiciones_pago)
                                                            <div>
                                                                <p class="text-xs uppercase tracking-wide font-semibold text-primary mb-1">Condiciones de pago</p>
                                                                <p class="text-sm leading-relaxed whitespace-pre-line opacity-90">{{ $entry->condiciones_pago }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- REQUERIMIENTOS --}}
                            @if ($contactMessage->requerimientos_operacion || $contactMessage->requerimientos_tecnicos)
                                <div class="border border-base-content/10 rounded-box overflow-hidden">
                                    <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                        <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Requerimientos</h3>
                                    </div>
                                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        @if ($contactMessage->requerimientos_tecnicos)
                                            <div>
                                                <p class="text-xs uppercase tracking-wide font-semibold text-primary mb-1">Técnico</p>
                                                <p class="text-sm leading-relaxed whitespace-pre-line opacity-90">{{ $contactMessage->requerimientos_tecnicos }}</p>
                                            </div>
                                        @endif
                                        @if ($contactMessage->requerimientos_operacion)
                                            <div>
                                                <p class="text-xs uppercase tracking-wide font-semibold text-primary mb-1">Operación</p>
                                                <p class="text-sm leading-relaxed whitespace-pre-line opacity-90">{{ $contactMessage->requerimientos_operacion }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- CIERRE: notas, vigencia, datos de contacto --}}
                            @if ($contactMessage->notas || !empty($contactMessage->datos_contacto))
                                <div class="border border-base-content/10 rounded-box overflow-hidden">
                                    <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                        <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Notas</h3>
                                    </div>
                                    <div class="p-5">
                                        @if ($contactMessage->notas)
                                            <p class="text-sm leading-relaxed whitespace-pre-line opacity-90 mb-4">{{ $contactMessage->notas }}</p>
                                        @endif

                                        @if (!empty($contactMessage->datos_contacto))
                                            <div class="divider my-2"></div>
                                            <div class="flex flex-col gap-1">
                                                @foreach ($contactMessage->datos_contacto as $item)
                                                    <p class="text-sm">
                                                        <span class="font-semibold uppercase text-xs opacity-60">{{ $item['tipo'] ?? '' }}:</span>
                                                        {{ $item['valor'] ?? '' }}
                                                    </p>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <p class="text-xs opacity-50 text-center sm:text-left">
                                Precios sujetos a disponibilidad y confirmación de fecha del artista. Cotización sujeta a cambios sin previo aviso.
                            </p>

                        </div>
                    @endif

                    {{-- TAB: MIS DATOS (todo lo que el cliente llenó) --}}
                    <div x-show="tab === 'datos'" x-cloak class="flex flex-col gap-4" @if(! $showCotizacion) x-cloak="false" @endif>

                        {{-- 01 DATOS DEL CLIENTE --}}
                        <div class="border border-base-content/10 rounded-box overflow-hidden">
                            <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Datos del cliente</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Nombre del contacto</p>
                                        <p class="font-medium">{{ $contactMessage->name ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Empresa</p>
                                        <p class="font-medium">{{ $contactMessage->empresa ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Puesto / Cargo</p>
                                        <p class="font-medium">{{ $contactMessage->puesto_contacto ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Teléfono</p>
                                        <p class="font-medium">{{ $contactMessage->phone ?: '—' }}</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Correo electrónico</p>
                                        <p class="font-medium">{{ $contactMessage->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 02 DETALLES DEL EVENTO --}}
                        <div class="border border-base-content/10 rounded-box overflow-hidden">
                            <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Detalles del evento</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 mb-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Fecha del evento</p>
                                        <p class="font-medium">{{ $contactMessage->fecha_evento?->format('d/m/Y') ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Ciudad</p>
                                        <p class="font-medium">{{ $contactMessage->ciudad ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Aforo estimado</p>
                                        <p class="font-medium">{{ $contactMessage->aforo_esperado ? number_format($contactMessage->aforo_esperado) . ' personas' : '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Estado de la república</p>
                                        <p class="font-medium">{{ $contactMessage->estado_republica ?: '—' }}</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Venue / Lugar del evento</p>
                                        <p class="font-medium">{{ $contactMessage->venue ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Hora de acceso del público</p>
                                        <p class="font-medium">{{ $contactMessage->hora_acceso ? \Carbon\Carbon::parse($contactMessage->hora_acceso)->format('H:i') : '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Hora de presentación</p>
                                        <p class="font-medium">{{ $contactMessage->hora_presentacion ? \Carbon\Carbon::parse($contactMessage->hora_presentacion)->format('H:i') : '—' }}</p>
                                    </div>
                                </div>

                                <div class="divider my-1"></div>

                                <p class="text-xs uppercase tracking-wide opacity-50 mb-2">Tipo de evento</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge badge-outline badge-primary">
                                        {{ $tiposEvento[$contactMessage->tipo_evento] ?? 'No especificado' }}
                                    </span>
                                    @if ($contactMessage->con_venta_boletos)
                                        <span class="badge badge-outline badge-primary">Con venta de boletos</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 03 TALENTO SOLICITADO --}}
                        <div class="border border-base-content/10 rounded-box overflow-hidden">
                            <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Talento solicitado</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Talento / Artista</p>
                                        <p class="font-medium text-primary">{{ $contactMessage->talent_name ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Formato de contratación</p>
                                        <p class="font-medium">{{ $contactMessage->formato_contratacion ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">¿Tiene presupuesto?</p>
                                        <p class="font-medium">
                                            {{ is_null($contactMessage->tiene_presupuesto) ? '—' : ($contactMessage->tiene_presupuesto ? 'Sí tiene presupuesto' : 'No tiene presupuesto') }}
                                        </p>
                                    </div>
                                    @if ($contactMessage->tiene_presupuesto && $contactMessage->presupuesto_aproximado)
                                        <div>
                                            <p class="text-xs uppercase tracking-wide opacity-50 mb-0.5">Presupuesto aproximado</p>
                                            <p class="font-medium">${{ number_format((float) $contactMessage->presupuesto_aproximado, 2) }} MXN</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 04/05 DETALLE Y REQUERIMIENTOS (si ya se completó fase 2) --}}
                        @if ($contactMessage->extra_info_completed_at)
                            @if ($contactMessage->detalle_actividad)
                                <div class="border border-base-content/10 rounded-box overflow-hidden">
                                    <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                        <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Detalle de la actividad</h3>
                                    </div>
                                    <div class="p-5">
                                        <p class="text-sm leading-relaxed whitespace-pre-line">{{ $contactMessage->detalle_actividad }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($contactMessage->requerimientos_operacion || $contactMessage->requerimientos_tecnicos)
                                <div class="border border-base-content/10 rounded-box overflow-hidden">
                                    <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                        <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Requerimientos</h3>
                                    </div>
                                    <div class="p-5">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div>
                                                <p class="text-xs uppercase tracking-wide opacity-50 mb-1">Requerimientos de operación</p>
                                                <p class="text-sm leading-relaxed whitespace-pre-line">{{ $contactMessage->requerimientos_operacion ?: '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide opacity-50 mb-1">Requerimientos técnicos</p>
                                                <p class="text-sm leading-relaxed whitespace-pre-line">{{ $contactMessage->requerimientos_tecnicos ?: '—' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="alert bg-primary/10 border border-primary/30 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Ya registraste tu información adicional el {{ $contactMessage->extra_info_completed_at->format('d/m/Y H:i') }}. Nuestro equipo se pondrá en contacto contigo pronto.</span>
                            </div>

                        @else
                            {{-- FORMULARIO FASE 2 --}}
                            <div class="border border-base-content/10 rounded-box overflow-hidden">
                                <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5">
                                    <h3 class="text-xs font-bold uppercase tracking-widest text-primary">Completa tu solicitud</h3>
                                </div>
                                <div class="p-5">
                                    <p class="text-sm opacity-70 mb-5">
                                        Ayúdanos con estos datos adicionales para preparar tu cotización con mayor precisión.
                                        Podrás llenar esta información <strong>una sola vez</strong>.
                                    </p>

                                    <form method="POST" action="{{ route('contact-messages.public.update', $contactMessage->public_token) }}" class="flex flex-col gap-4">
                                        @csrf

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Empresa</span></label>
                                                <input type="text" name="empresa" value="{{ old('empresa') }}" class="input input-bordered w-full">
                                                @error('empresa') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Puesto / Cargo</span></label>
                                                <input type="text" name="puesto_contacto" value="{{ old('puesto_contacto') }}" class="input input-bordered w-full">
                                                @error('puesto_contacto') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Fecha del evento</span></label>
                                                <input type="date" name="fecha_evento" value="{{ old('fecha_evento') }}" class="input input-bordered w-full">
                                                @error('fecha_evento') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Hora de acceso</span></label>
                                                <input type="time" name="hora_acceso" value="{{ old('hora_acceso') }}" class="input input-bordered w-full">
                                                @error('hora_acceso') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Hora de presentación</span></label>
                                                <input type="time" name="hora_presentacion" value="{{ old('hora_presentacion') }}" class="input input-bordered w-full">
                                                @error('hora_presentacion') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="form-control">
                                            <label class="label"><span class="label-text">Formato de contratación</span></label>
                                            <input type="text" name="formato_contratacion" value="{{ old('formato_contratacion') }}" placeholder="Ej. Show completo, por hora, meet & greet..." class="input input-bordered w-full">
                                            @error('formato_contratacion') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-control">
                                            <label class="label"><span class="label-text">Detalle de la actividad</span></label>
                                            <textarea name="detalle_actividad" rows="3" class="textarea textarea-bordered w-full">{{ old('detalle_actividad') }}</textarea>
                                            @error('detalle_actividad') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Requerimientos de operación</span></label>
                                                <textarea name="requerimientos_operacion" rows="3" class="textarea textarea-bordered w-full">{{ old('requerimientos_operacion') }}</textarea>
                                                @error('requerimientos_operacion') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-control">
                                                <label class="label"><span class="label-text">Requerimientos técnicos</span></label>
                                                <textarea name="requerimientos_tecnicos" rows="3" class="textarea textarea-bordered w-full">{{ old('requerimientos_tecnicos') }}</textarea>
                                                @error('requerimientos_tecnicos') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="pt-2">
                                            <button type="submit" class="btn btn-primary w-full sm:w-auto">Guardar información</button>
                                            <p class="text-xs opacity-50 mt-2">Una vez guardada, esta información ya no podrá editarse.</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

        @endif

        <p class="text-center text-xs opacity-50 mt-8">
            ventas@capetilloproducciones.mx · www.capetilloproducciones.mx
        </p>

    </div>
</div>

@endsection