@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center gap-3 mb-1">
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
    <h1 class="text-2xl font-bold">Detalle del mensaje</h1>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4 mt-4 text-sm">{{ session('status') }}</div>
@endif

<div id="talent-ajax-alert" class="hidden alert mb-4 mt-4 text-sm"></div>

<form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}">
@csrf
@method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">

    <div class="lg:col-span-2">

        {{-- TABS --}}
        <div role="tablist" class="tabs tabs-lifted">
            <a role="tab" id="tab-btn-general" class="tab tab-active" onclick="switchTab('general')">Datos generales</a>
            <a role="tab" id="tab-btn-cotizacion" class="tab" onclick="switchTab('cotizacion')">Cotización</a>
        </div>

        <div class="bg-base-100 border border-base-300 border-t-0 rounded-b-box p-4">

            {{-- TAB 1: DATOS GENERALES --}}
            <div id="tab-general" class="flex flex-col gap-4">

                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold mb-3">Datos del cliente (mensaje original)</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Tipo</span></label>
                                <select name="type" class="select select-bordered w-full">
                                    <option value="contratacion" @selected($contactMessage->type === 'contratacion')>Contratación</option>
                                    <option value="general" @selected($contactMessage->type === 'general')>General</option>
                                </select>
                                @error('type') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Talento de interés</span></label>
                                <select name="talent_id" class="select select-bordered w-full">
                                    <option value="">Sin talento específico</option>
                                    @foreach ($talents as $talent)
                                        <option value="{{ $talent->id }}" @selected(old('talent_id', $contactMessage->talent_id) == $talent->id)>{{ $talent->name }}</option>
                                    @endforeach
                                </select>
                                @error('talent_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Nombre del contacto</span></label>
                                <input type="text" name="name" value="{{ old('name', $contactMessage->name) }}" class="input input-bordered w-full">
                                @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Correo</span></label>
                                <input type="email" name="email" value="{{ old('email', $contactMessage->email) }}" class="input input-bordered w-full">
                                @error('email') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Teléfono</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $contactMessage->phone) }}" class="input input-bordered w-full">
                                @error('phone') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Estado de la república</span></label>
                                <select name="estado_republica" class="select select-bordered w-full">
                                    <option value="">Sin especificar</option>
                                    @foreach (config('estados_mexico') as $estado)
                                        <option value="{{ $estado }}" @selected(old('estado_republica', $contactMessage->estado_republica) === $estado)>{{ $estado }}</option>
                                    @endforeach
                                </select>
                                @error('estado_republica') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Aforo esperado</span></label>
                                <input type="number" min="1" name="aforo_esperado" value="{{ old('aforo_esperado', $contactMessage->aforo_esperado) }}" class="input input-bordered w-full">
                                @error('aforo_esperado') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Venue / Lugar</span></label>
                                <input type="text" name="venue" value="{{ old('venue', $contactMessage->venue) }}" class="input input-bordered w-full">
                                @error('venue') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="form-control mt-3">
                            <label class="label"><span class="label-text">Mensaje</span></label>
                            <textarea name="message" rows="3" class="textarea textarea-bordered w-full">{{ old('message', $contactMessage->message) }}</textarea>
                            @error('message') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <p class="text-xs opacity-50 mt-3">Recibido el {{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold mb-3">Datos del cliente y del evento</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Empresa</span></label>
                                <input type="text" name="empresa" value="{{ old('empresa', $contactMessage->empresa) }}" class="input input-bordered w-full">
                                @error('empresa') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Puesto / Cargo</span></label>
                                <input type="text" name="puesto_contacto" value="{{ old('puesto_contacto', $contactMessage->puesto_contacto) }}" class="input input-bordered w-full">
                                @error('puesto_contacto') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Ciudad</span></label>
                                <input type="text" name="ciudad" value="{{ old('ciudad', $contactMessage->ciudad) }}" class="input input-bordered w-full">
                                @error('ciudad') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Tipo de evento</span></label>
                                <select name="tipo_evento" class="select select-bordered w-full">
                                    <option value="" @selected(old('tipo_evento', $contactMessage->tipo_evento) === null)>Sin especificar</option>
                                    <option value="privado" @selected(old('tipo_evento', $contactMessage->tipo_evento) === 'privado')>Privado</option>
                                    <option value="corporativo" @selected(old('tipo_evento', $contactMessage->tipo_evento) === 'corporativo')>Corporativo</option>
                                    <option value="publico_masivo" @selected(old('tipo_evento', $contactMessage->tipo_evento) === 'publico_masivo')>Público / Masivo</option>
                                    <option value="social" @selected(old('tipo_evento', $contactMessage->tipo_evento) === 'social')>Social (boda / XV)</option>
                                    <option value="gubernamental" @selected(old('tipo_evento', $contactMessage->tipo_evento) === 'gubernamental')>Gubernamental</option>
                                </select>
                                @error('tipo_evento') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-3">
                                    <input type="checkbox" name="con_venta_boletos" value="1" class="checkbox"
                                        @checked(old('con_venta_boletos', $contactMessage->con_venta_boletos))>
                                    <span class="label-text">¿Con venta de boletos?</span>
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Fecha del evento</span></label>
                                <input type="date" name="fecha_evento" value="{{ old('fecha_evento', optional($contactMessage->fecha_evento)->format('Y-m-d')) }}" class="input input-bordered w-full">
                                @error('fecha_evento') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Formato de contratación</span></label>
                                <input type="text" name="formato_contratacion" value="{{ old('formato_contratacion', $contactMessage->formato_contratacion) }}" class="input input-bordered w-full">
                                @error('formato_contratacion') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Hora de acceso</span></label>
                                <input type="time" name="hora_acceso" value="{{ old('hora_acceso', $contactMessage->hora_acceso ? \Carbon\Carbon::parse($contactMessage->hora_acceso)->format('H:i') : '') }}" class="input input-bordered w-full">
                                @error('hora_acceso') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Hora de presentación</span></label>
                                <input type="time" name="hora_presentacion" value="{{ old('hora_presentacion', $contactMessage->hora_presentacion ? \Carbon\Carbon::parse($contactMessage->hora_presentacion)->format('H:i') : '') }}" class="input input-bordered w-full">
                                @error('hora_presentacion') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-3">
                                    <input type="checkbox" name="tiene_presupuesto" value="1" class="checkbox"
                                           @checked(old('tiene_presupuesto', $contactMessage->tiene_presupuesto))>
                                    <span class="label-text">¿Tiene presupuesto?</span>
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Presupuesto aproximado (MXN)</span></label>
                                <input type="number" step="0.01" min="0" name="presupuesto_aproximado"
                                       value="{{ old('presupuesto_aproximado', $contactMessage->presupuesto_aproximado) }}"
                                       class="input input-bordered w-full" placeholder="0.00">
                                @error('presupuesto_aproximado') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="form-control mt-3">
                            <label class="label"><span class="label-text">Detalle de la actividad</span></label>
                            <textarea name="detalle_actividad" rows="3" class="textarea textarea-bordered w-full">{{ old('detalle_actividad', $contactMessage->detalle_actividad) }}</textarea>
                            @error('detalle_actividad') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if ($contactMessage->extra_info_completed_at)
                            <div class="alert alert-info text-xs mt-3">
                                El cliente completó esta información el {{ $contactMessage->extra_info_completed_at->format('d/m/Y H:i') }}.
                                Puedes seguir editándola desde aquí si es necesario.
                            </div>
                        @else
                            <div class="alert alert-warning text-xs mt-3">
                                El cliente aún no ha llenado esta información desde el enlace público.
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- TAB 2: COTIZACIÓN --}}
            <div id="tab-cotizacion" class="flex flex-col gap-4 hidden">

                {{-- Placeholder: la card de Talentos se renderiza fuera del <form> principal, ver abajo --}}
                <div id="talents-card-slot"></div>

                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold mb-3">Requerimientos</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Requerimientos de operación</span></label>
                                <textarea name="requerimientos_operacion" rows="3" class="textarea textarea-bordered w-full">{{ old('requerimientos_operacion', $contactMessage->requerimientos_operacion) }}</textarea>
                                @error('requerimientos_operacion') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Requerimientos técnicos</span></label>
                                <textarea name="requerimientos_tecnicos" rows="3" class="textarea textarea-bordered w-full">{{ old('requerimientos_tecnicos', $contactMessage->requerimientos_tecnicos) }}</textarea>
                                @error('requerimientos_tecnicos') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold mb-3">Datos de la cotización</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Fecha de vigencia</span></label>
                                <input type="date" name="fecha_vigencia" value="{{ old('fecha_vigencia', optional($contactMessage->fecha_vigencia)->format('Y-m-d')) }}" class="input input-bordered w-full">
                                @error('fecha_vigencia') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Notas de la cotización</span></label>
                            <textarea name="notas" rows="3" class="textarea textarea-bordered w-full" placeholder="Ej. Cotización con vigencia de 10 días naturales, precios sujetos a disponibilidad...">{{ old('notas', $contactMessage->notas) }}</textarea>
                            @error('notas') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="divider my-1"></div>

                        <p class="text-sm font-medium mb-2">Datos de contacto (aparecen en el cierre del PPTX)</p>
                        <div id="contacto-repeater" class="flex flex-col gap-2">
                            @php $contactos = old('contacto_tipo') ? collect(old('contacto_tipo'))->map(fn($tipo, $i) => ['tipo' => $tipo, 'valor' => old('contacto_valor')[$i] ?? '']) : collect($contactMessage->datos_contacto ?? []); @endphp
                            @forelse ($contactos as $item)
                                <div class="flex gap-2 items-start contacto-row">
                                    <select name="contacto_tipo[]" class="select select-bordered select-sm w-32">
                                        <option value="email" @selected(($item['tipo'] ?? '') === 'email')>Email</option>
                                        <option value="telefono" @selected(($item['tipo'] ?? '') === 'telefono')>Teléfono</option>
                                        <option value="direccion" @selected(($item['tipo'] ?? '') === 'direccion')>Dirección</option>
                                    </select>
                                    <input type="text" name="contacto_valor[]" value="{{ $item['valor'] ?? '' }}" class="input input-bordered input-sm flex-1">
                                    <button type="button" class="btn btn-ghost btn-sm btn-square remove-contacto-row">✕</button>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" id="add-contacto-row" class="btn btn-ghost btn-xs mt-2">+ Agregar dato de contacto</button>

                        <template id="contacto-row-template">
                            <div class="flex gap-2 items-start contacto-row">
                                <select name="contacto_tipo[]" class="select select-bordered select-sm w-32">
                                    <option value="email">Email</option>
                                    <option value="telefono">Teléfono</option>
                                    <option value="direccion">Dirección</option>
                                </select>
                                <input type="text" name="contacto_valor[]" class="input input-bordered input-sm flex-1">
                                <button type="button" class="btn btn-ghost btn-sm btn-square remove-contacto-row">✕</button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Gestión: estatus y cotización --}}
    <div class="lg:col-span-1">
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Gestión del mensaje</h3>

                <div class="flex flex-col gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Estatus</span></label>
                        <select name="status" class="select select-bordered w-full">
                            <option value="contacto_inicial" @selected($contactMessage->status === 'contacto_inicial')>Contacto inicial</option>
                            <option value="procesando" @selected($contactMessage->status === 'procesando')>Procesando</option>
                            <option value="venta_no_concluida" @selected($contactMessage->status === 'venta_no_concluida')>Venta no concluida</option>
                            <option value="cotizacion_completa" @selected($contactMessage->status === 'cotizacion_completa')>Cotización completa</option>
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
                        <p class="text-xs opacity-60 mt-1">Este es el pago neto que recibe Capetillo Producciones por esta contratación (no necesariamente el precio mostrado al cliente).</p>
                        @error('cotizacion_final')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="divider my-1"></div>

                    @if ($contactMessage->cotizacionTalents->isNotEmpty())
                        <a href="{{ route('admin.contact-messages.export', $contactMessage) }}" class="btn btn-outline w-full">
                            Exportar PPTX
                        </a>
                    @endif

                    <button type="submit" class="btn btn-primary w-full">Guardar</button>
                </div>

                <div class="divider"></div>

                <p class="text-xs opacity-60 mb-2">Enlace público de seguimiento:</p>
                <a href="{{ route('contact-messages.public.show', $contactMessage->public_token) }}" target="_blank" class="link link-primary text-xs break-all">
                    {{ route('contact-messages.public.show', $contactMessage->public_token) }}
                </a>
            </div>
        </div>
    </div>

</div>
</form>

{{-- TALENTOS EN LA COTIZACIÓN (fuera del <form> principal a propósito: HTML no permite <form> anidados) --}}
<div id="talents-card" class="card bg-base-100 border border-base-300">
    <div class="card-body">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Talentos en la cotización</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('talent-picker-modal').showModal()">
                + Agregar talento
            </button>
        </div>

        <div id="talents-list" class="flex flex-col gap-4">
            @foreach ($contactMessage->cotizacionTalents as $entry)
                @include('admin.contact-messages.partials.talent-entry', ['entry' => $entry])
            @endforeach
        </div>

        <p id="talents-empty-msg" class="text-sm opacity-60 {{ $contactMessage->cotizacionTalents->isNotEmpty() ? 'hidden' : '' }}">
            Aún no se han agregado talentos a esta cotización.
        </p>
    </div>
</div>

<script>
    // Mueve la card de talentos (que vive fuera del <form> por seguridad de HTML) al slot visual dentro del tab.
    document.getElementById('talents-card-slot')?.replaceWith(document.getElementById('talents-card'));
</script>

{{-- MODAL: selector de talentos --}}
<dialog id="talent-picker-modal" class="modal">
    <div class="modal-box max-w-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="font-bold text-lg mb-3">Agregar talentos a la cotización</h3>

        <div class="flex items-center gap-3 mb-3">
            <select id="category-filter" class="select select-bordered select-sm">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <label class="label cursor-pointer gap-2">
                <input type="checkbox" id="select-all-talents" class="checkbox checkbox-sm">
                <span class="label-text text-sm">Seleccionar todo</span>
            </label>
        </div>

        <div class="max-h-80 overflow-y-auto border border-base-300 rounded-box p-3 flex flex-col gap-1">
            @foreach ($talents as $talent)
                <label class="label cursor-pointer justify-start gap-2 talent-row" data-categories="{{ $talent->categories->pluck('id')->implode(',') }}">
                    <input type="checkbox" value="{{ $talent->id }}" class="checkbox checkbox-sm talent-checkbox">
                    <span class="label-text text-sm">{{ $talent->name }}</span>
                    <span class="text-xs opacity-50 ml-auto">{{ $talent->categories->pluck('name')->join(', ') }}</span>
                </label>
            @endforeach
        </div>

        <div class="modal-action">
            <button type="button" id="add-talents-btn" class="btn btn-primary">Agregar a la cotización</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const addTalentsUrl = '{{ route('admin.contact-messages.talents.store', $contactMessage) }}';
const talentAjaxAlert = document.getElementById('talent-ajax-alert');

function switchTab(tab) {
    document.getElementById('tab-general').classList.toggle('hidden', tab !== 'general');
    document.getElementById('tab-cotizacion').classList.toggle('hidden', tab !== 'cotizacion');
    document.getElementById('tab-btn-general').classList.toggle('tab-active', tab === 'general');
    document.getElementById('tab-btn-cotizacion').classList.toggle('tab-active', tab === 'cotizacion');
}

function showTalentAlert(message, isError = false) {
    talentAjaxAlert.textContent = message;
    talentAjaxAlert.classList.remove('hidden', 'alert-success', 'alert-error');
    talentAjaxAlert.classList.add(isError ? 'alert-error' : 'alert-success');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => talentAjaxAlert.classList.add('hidden'), 4000);
}

(function () {
    // Repeater de datos de contacto
    const container = document.getElementById('contacto-repeater');
    const template = document.getElementById('contacto-row-template');

    document.getElementById('add-contacto-row')?.addEventListener('click', () => {
        container.appendChild(template.content.cloneNode(true));
    });

    container?.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-contacto-row')) {
            e.target.closest('.contacto-row').remove();
        }
    });

    // Filtro por categoría + seleccionar todo en el picker de talentos
    const categoryFilter = document.getElementById('category-filter');
    const selectAll = document.getElementById('select-all-talents');
    const talentRows = document.querySelectorAll('.talent-row');

    categoryFilter?.addEventListener('change', () => {
        const categoryId = categoryFilter.value;
        talentRows.forEach(row => {
            const categories = row.dataset.categories.split(',');
            row.style.display = (! categoryId || categories.includes(categoryId)) ? 'flex' : 'none';
        });
    });

    selectAll?.addEventListener('change', () => {
        talentRows.forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('.talent-checkbox').checked = selectAll.checked;
            }
        });
    });

    // Agregar talentos vía AJAX (sin reload — inyecta HTML devuelto por el servidor)
    document.getElementById('add-talents-btn')?.addEventListener('click', async () => {
        const ids = Array.from(document.querySelectorAll('.talent-checkbox:checked')).map(cb => cb.value);

        if (ids.length === 0) {
            showTalentAlert('Selecciona al menos un talento.', true);
            return;
        }

        try {
            const response = await fetch(addTalentsUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ talent_ids: ids }),
            });

            const data = await response.json();
            if (! response.ok) throw new Error(data.message || 'Error al agregar talentos.');

            document.getElementById('talents-list').innerHTML = data.html;
            document.getElementById('talents-empty-msg').classList.add('hidden');
            bindTalentFormEvents();

            document.getElementById('talent-picker-modal').close();
            document.querySelectorAll('.talent-checkbox:checked').forEach(cb => cb.checked = false);
            showTalentAlert(data.message);
        } catch (err) {
            showTalentAlert(err.message, true);
        }
    });
})();

// Delegación de eventos para forms de talento (funciona con contenido inyectado dinámicamente)
function bindTalentFormEvents() {
    document.querySelectorAll('.talent-update-form:not([data-bound])').forEach(form => {
        form.setAttribute('data-bound', '1');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(form.action, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                if (! response.ok) throw new Error(data.message || 'Error al guardar.');

                showTalentAlert(data.message);
            } catch (err) {
                showTalentAlert(err.message, true);
            }
        });
    });

    document.querySelectorAll('.talent-remove-btn:not([data-bound])').forEach(btn => {
        btn.setAttribute('data-bound', '1');
        btn.addEventListener('click', async () => {
            if (! confirm('¿Quitar este talento de la cotización?')) return;

            try {
                const response = await fetch(btn.dataset.url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();
                if (! response.ok) throw new Error(data.message || 'Error al eliminar.');

                btn.closest('.talent-entry-row').remove();
                showTalentAlert(data.message);

                if (document.querySelectorAll('.talent-entry-row').length === 0) {
                    document.getElementById('talents-empty-msg').classList.remove('hidden');
                }
            } catch (err) {
                showTalentAlert(err.message, true);
            }
        });
    });
}

bindTalentFormEvents();
</script>

@endsection