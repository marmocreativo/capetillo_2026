@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center gap-3 mb-1">
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
    <h1 class="text-2xl font-bold">Nueva cotización</h1>
</div>

<form method="POST" action="{{ route('admin.contact-messages.store') }}" class="mt-6">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <div class="lg:col-span-2 flex flex-col gap-4">

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Datos del cliente</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Tipo</span></label>
                        <select name="type" class="select select-bordered w-full" id="type-select">
                            <option value="contratacion" @selected(old('type', 'contratacion') === 'contratacion')>Contratación</option>
                            <option value="general" @selected(old('type') === 'general')>General</option>
                        </select>
                        @error('type') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-control" id="talent-select-wrapper">
                        <label class="label"><span class="label-text">Talento</span></label>
                        <select name="talent_id" class="select select-bordered w-full">
                            <option value="">Sin talento específico</option>
                            @foreach ($talents as $talent)
                                <option value="{{ $talent->id }}" @selected(old('talent_id') == $talent->id)>{{ $talent->name }}</option>
                            @endforeach
                        </select>
                        @error('talent_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre del contacto</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required>
                        @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
                        @error('email') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="input input-bordered w-full">
                        @error('phone') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-control mt-3">
                    <label class="label"><span class="label-text">Mensaje</span></label>
                    <textarea name="message" rows="3" class="textarea textarea-bordered w-full" required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Detalles del evento</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Estado de la república</span></label>
                        <select name="estado_republica" class="select select-bordered w-full">
                            <option value="">Sin especificar</option>
                            @foreach (config('estados_mexico') as $estado)
                                <option value="{{ $estado }}" @selected(old('estado_republica') === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Ciudad</span></label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}" class="input input-bordered w-full">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Aforo esperado</span></label>
                        <input type="number" min="1" name="aforo_esperado" value="{{ old('aforo_esperado') }}" class="input input-bordered w-full">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Venue / Lugar</span></label>
                        <input type="text" name="venue" value="{{ old('venue') }}" class="input input-bordered w-full">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Tipo de evento</span></label>
                        <select name="tipo_evento" class="select select-bordered w-full">
                            <option value="">Sin especificar</option>
                            <option value="privado" @selected(old('tipo_evento') === 'privado')>Privado</option>
                            <option value="corporativo" @selected(old('tipo_evento') === 'corporativo')>Corporativo</option>
                            <option value="publico_masivo" @selected(old('tipo_evento') === 'publico_masivo')>Público / Masivo</option>
                            <option value="social" @selected(old('tipo_evento') === 'social')>Social (boda / XV)</option>
                            <option value="gubernamental" @selected(old('tipo_evento') === 'gubernamental')>Gubernamental</option>
                        </select>
                    </div>
                    <div class="form-control justify-end">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="con_venta_boletos" value="1" class="checkbox" @checked(old('con_venta_boletos'))>
                            <span class="label-text">¿Con venta de boletos?</span>
                        </label>
                    </div>
                </div>

                <div class="divider my-2"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="tiene_presupuesto" value="1" class="checkbox" @checked(old('tiene_presupuesto'))>
                            <span class="label-text">¿Tiene presupuesto?</span>
                        </label>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Presupuesto aproximado (MXN)</span></label>
                        <input type="number" step="0.01" min="0" name="presupuesto_aproximado" value="{{ old('presupuesto_aproximado') }}" class="input input-bordered w-full">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="lg:col-span-1">
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Estatus inicial</h3>

                <div class="form-control">
                    <select name="status" class="select select-bordered w-full">
                        <option value="contacto_inicial" @selected(old('status', 'contacto_inicial') === 'contacto_inicial')>Contacto inicial</option>
                        <option value="procesando" @selected(old('status') === 'procesando')>Procesando</option>
                        <option value="en_espera_cotizacion" @selected(old('status') === 'en_espera_cotizacion')>En espera de cotización</option>
                        <option value="venta_no_concluida" @selected(old('status') === 'venta_no_concluida')>Venta no concluida</option>
                        <option value="cotizacion_completa" @selected(old('status') === 'cotizacion_completa')>Cotización completa</option>
                        <option value="contrato_cerrado" @selected(old('status') === 'contrato_cerrado')>Contrato cerrado</option>
                        <option value="contrato_pagado" @selected(old('status') === 'contrato_pagado')>Contrato pagado</option>
                    </select>
                    @error('status') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="form-control mt-3">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="enviar_cotizacion" value="1" class="checkbox" @checked(old('enviar_cotizacion'))>
                        <span class="label-text">¿Enviar cotización al cliente?</span>
                    </label>
                    @error('enviar_cotizacion') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <p class="text-xs opacity-60 mt-3">
                    Después de crear la cotización podrás agregar talentos, precios, requerimientos y notas desde la pantalla de edición.
                </p>

                <button type="submit" class="btn btn-primary w-full mt-4">Crear cotización</button>
            </div>
        </div>
    </div>

</div>
</form>

<script>
    const typeSelect = document.getElementById('type-select');
    const talentWrapper = document.getElementById('talent-select-wrapper');

    function toggleTalentField() {
        talentWrapper.style.display = typeSelect.value === 'contratacion' ? 'block' : 'none';
    }
    typeSelect.addEventListener('change', toggleTalentField);
    toggleTalentField();
</script>

@endsection