@if ($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedTalents = old('talents', isset($roster) ? $roster->rosterTalents->pluck('talent_id')->toArray() : []);
    $existingContacto = old('contacto_tipo') ? null : ($roster->datos_contacto ?? []);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 flex flex-col gap-4">

        <div class="form-control">
            <label class="label"><span class="label-text">Nombre del roster</span></label>
            <input type="text" name="name" value="{{ old('name', $roster->name ?? '') }}" class="input input-bordered w-full" required>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Texto de presentación</span></label>
            <textarea name="intro_text" class="textarea textarea-bordered w-full" rows="4">{{ old('intro_text', $roster->intro_text ?? '') }}</textarea>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Texto de salida</span></label>
            <textarea name="outro_text" class="textarea textarea-bordered w-full" rows="4">{{ old('outro_text', $roster->outro_text ?? '') }}</textarea>
        </div>

        <div class="divider my-0">Datos de contacto</div>

        <div class="form-control">
            <div id="contacto-repeater" class="flex flex-col gap-2">
                @forelse ($existingContacto ?: [] as $index => $item)
                    <div class="flex gap-2">
                        <select name="contacto_tipo[]" class="select select-bordered select-sm w-40">
                            <option value="email" {{ ($item['tipo'] ?? '') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="telefono" {{ ($item['tipo'] ?? '') === 'telefono' ? 'selected' : '' }}>Teléfono</option>
                            <option value="direccion" {{ ($item['tipo'] ?? '') === 'direccion' ? 'selected' : '' }}>Dirección</option>
                        </select>
                        <input type="text" name="contacto_valor[]" value="{{ $item['valor'] ?? '' }}" class="input input-bordered input-sm w-full">
                        <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                    </div>
                @empty
                    <div class="flex gap-2">
                        <select name="contacto_tipo[]" class="select select-bordered select-sm w-40">
                            <option value="email">Email</option>
                            <option value="telefono">Teléfono</option>
                            <option value="direccion">Dirección</option>
                        </select>
                        <input type="text" name="contacto_valor[]" value="" class="input input-bordered input-sm w-full">
                        <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                    </div>
                @endforelse
            </div>
            <button type="button" id="add-contacto" class="btn btn-xs btn-outline mt-2">+ Agregar dato de contacto</button>
        </div>

        <div class="divider my-0">Talentos asignados</div>

        <div class="form-control">
            <div class="flex flex-col sm:flex-row gap-2 mb-2">
                <input type="text" id="talent-filter-name" placeholder="Filtrar por nombre..." class="input input-bordered input-sm w-full sm:max-w-xs">
                <select id="talent-filter-category" class="select select-bordered select-sm">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="label cursor-pointer gap-2 self-center">
                    <input type="checkbox" id="talent-select-all-visible" class="checkbox checkbox-sm">
                    <span class="label-text text-sm">Seleccionar todo lo visible</span>
                </label>

                <span class="text-sm opacity-60 self-center">Seleccionados: <span id="talent-selected-count">{{ count($selectedTalents) }}</span></span>
            </div>

            <div class="flex flex-col gap-1 p-3 border border-base-300 rounded-box max-h-96 overflow-y-auto">
                @foreach ($talents as $talent)
                    <label class="label cursor-pointer gap-2 justify-start py-1 talent-row"
                        data-name="{{ Str::lower($talent->name) }}"
                        data-categories="{{ $talent->categories->pluck('id')->implode(',') }}">
                        <input type="checkbox" name="talents[]" value="{{ $talent->id }}" class="checkbox checkbox-sm talent-checkbox"
                            {{ in_array($talent->id, $selectedTalents) ? 'checked' : '' }}>
                        <span class="label-text">{{ $talent->name }}</span>
                        <span class="text-xs opacity-50">({{ $talent->categories->pluck('name')->implode(', ') }})</span>
                    </label>
                @endforeach
            </div>
        </div>

    </div>

    <div class="lg:col-span-1">
        <div class="card bg-base-100 border border-base-300 lg:sticky lg:top-6">
            <div class="card-body gap-4">

                <div class="form-control">
                    <label class="label"><span class="label-text">Talentos por página</span></label>
                    <select name="talentos_por_pagina" class="select select-bordered w-full">
                        @php $current = old('talentos_por_pagina', $roster->talentos_por_pagina ?? 4); @endphp
                        <option value="1" {{ (int) $current === 1 ? 'selected' : '' }}>1</option>
                        <option value="2" {{ (int) $current === 2 ? 'selected' : '' }}>2</option>
                        <option value="4" {{ (int) $current === 4 ? 'selected' : '' }}>4</option>
                        <option value="0" {{ (int) $current === 0 ? 'selected' : '' }}>0 (lista)</option>
                    </select>
                </div>

                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="separar_por_categoria" value="1" class="checkbox" {{ old('separar_por_categoria', $roster->separar_por_categoria ?? false) ? 'checked' : '' }}>
                    <span class="label-text">Separar por categoría</span>
                </label>

                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="mostrar_honorarios" value="1" class="checkbox" {{ old('mostrar_honorarios', $roster->mostrar_honorarios ?? false) ? 'checked' : '' }}>
                    <span class="label-text">Mostrar honorarios (aunque sean 0)</span>
                </label>

                <div class="divider my-0"></div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Logos (PNG)</span></label>

                    @if (isset($roster) && ! empty($roster->logos))
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            @foreach ($roster->logos as $logo)
                                <div class="relative">
                                    <img src="{{ Storage::url($logo) }}" class="w-full aspect-square object-contain bg-base-200 rounded">
                                    <label class="absolute top-1 right-1 bg-base-100/90 rounded px-1 text-xs flex items-center gap-1 cursor-pointer">
                                        <input type="checkbox" name="delete_logos[]" value="{{ $logo }}" class="checkbox checkbox-xs checkbox-error">
                                        Eliminar
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="file" name="new_logos[]" accept="image/png" multiple class="file-input file-input-bordered w-full">
                    <p class="text-xs opacity-60 mt-1">Solo PNG. Se conserva el tamaño original.</p>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="mt-6 flex gap-2">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.rosters.index') }}" class="btn btn-ghost">Cancelar</a>
</div>

<script>
document.getElementById('add-contacto')?.addEventListener('click', () => {
    const container = document.getElementById('contacto-repeater');
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.innerHTML = `
        <select name="contacto_tipo[]" class="select select-bordered select-sm w-40">
            <option value="email">Email</option>
            <option value="telefono">Teléfono</option>
            <option value="direccion">Dirección</option>
        </select>
        <input type="text" name="contacto_valor[]" value="" class="input input-bordered input-sm w-full">
        <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
    `;
    container.appendChild(row);
});

document.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.flex.gap-2').remove();
    }
});

const nameFilter = document.getElementById('talent-filter-name');
const categoryFilter = document.getElementById('talent-filter-category');
const rows = document.querySelectorAll('.talent-row');
const selectedCountEl = document.getElementById('talent-selected-count');

const selectAllVisible = document.getElementById('talent-select-all-visible');

function getVisibleCheckboxes() {
    return Array.from(rows)
        .filter(row => !row.classList.contains('hidden'))
        .map(row => row.querySelector('.talent-checkbox'));
}

function updateSelectAllVisibleState() {
    const visibleCheckboxes = getVisibleCheckboxes();
    if (visibleCheckboxes.length === 0) {
        selectAllVisible.checked = false;
        selectAllVisible.indeterminate = false;
        return;
    }
    const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
    selectAllVisible.checked = checkedCount === visibleCheckboxes.length;
    selectAllVisible.indeterminate = checkedCount > 0 && checkedCount < visibleCheckboxes.length;
}

function applyTalentFilter() {
    const name = nameFilter.value.toLowerCase();
    const category = categoryFilter.value;

    rows.forEach(row => {
        const matchesName = row.dataset.name.includes(name);
        const matchesCategory = category === '' || row.dataset.categories.split(',').includes(category);
        row.classList.toggle('hidden', !(matchesName && matchesCategory));
    });

    updateSelectAllVisibleState();
}

nameFilter?.addEventListener('input', applyTalentFilter);
categoryFilter?.addEventListener('change', applyTalentFilter);

selectAllVisible?.addEventListener('change', () => {
    const shouldCheck = selectAllVisible.checked;
    getVisibleCheckboxes().forEach(cb => {
        cb.checked = shouldCheck;
    });
    selectedCountEl.textContent = document.querySelectorAll('.talent-checkbox:checked').length;
    updateSelectAllVisibleState();
});

document.querySelectorAll('.talent-checkbox').forEach(cb => {
    cb.addEventListener('change', () => {
        selectedCountEl.textContent = document.querySelectorAll('.talent-checkbox:checked').length;
        updateSelectAllVisibleState();
    });
});
</script>