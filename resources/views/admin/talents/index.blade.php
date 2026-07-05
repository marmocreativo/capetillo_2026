@extends('layouts.admin')

@section('admin-content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Talentos</h1>
    <div class="flex gap-2">
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-outline btn-sm">
                Más opciones
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </label>
            <div tabindex="0" class="dropdown-content z-10 menu p-4 shadow-lg bg-base-100 rounded-box w-80 gap-3 border border-base-300">

                <div>
                    <p class="text-xs font-semibold opacity-60 mb-2">Contenido (biografía + SEO)</p>
                    <div class="flex flex-col gap-1">
                        <button type="button" id="batch-selected-btn" class="btn btn-sm btn-secondary justify-start" disabled>
                            ✨ Generar (seleccionados: <span id="selected-count">0</span>)
                        </button>
                        <button type="button" id="batch-all-btn" class="btn btn-sm btn-outline btn-secondary justify-start">
                            ✨ Generar (solo vacíos)
                        </button>
                    </div>
                    <div id="batch-progress" class="hidden text-xs mt-2">
                        Procesando <span id="batch-current">0</span> / <span id="batch-total">0</span>
                        — <span id="batch-status" class="opacity-70"></span>
                    </div>
                </div>

                <div class="divider my-0"></div>

                <div>
                    <p class="text-xs font-semibold opacity-60 mb-2">Contenido extra (resumen, logros, Spotify, videos)</p>
                    <div class="flex flex-col gap-1">
                        <button type="button" id="extra-selected-btn" class="btn btn-sm btn-secondary justify-start" disabled>
                            🔍 Buscar (seleccionados: <span id="extra-selected-count">0</span>)
                        </button>
                        <button type="button" id="extra-all-btn" class="btn btn-sm btn-outline btn-secondary justify-start">
                            🔍 Buscar (solo vacíos)
                        </button>
                    </div>
                    <div id="extra-progress" class="hidden text-xs mt-2">
                        Procesando <span id="extra-current">0</span> / <span id="extra-total">0</span>
                        — <span id="extra-status" class="opacity-70"></span>
                    </div>
                </div>

            </div>
        </div>

        <a href="{{ route('admin.talents.create') }}" class="btn btn-primary btn-sm">Nuevo talento</a>
    </div>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4 text-sm">{{ session('status') }}</div>
@endif

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-4">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Buscar por nombre o slug..."
        class="input input-bordered w-full sm:max-w-xs"
    >

    <select name="category" class="select select-bordered">
        <option value="">Todas las categorías</option>
        @foreach ($categories as $category)
            <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="status" class="select select-bordered">
        <option value="">Todos los estados</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>

    @if (request('search') || request('category') || request('status'))
        <a href="{{ route('admin.talents.index') }}" class="btn btn-ghost">Limpiar</a>
    @endif
</form>

<div class="overflow-x-auto bg-base-100 rounded-box shadow">
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all" class="checkbox checkbox-sm"></th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Categorías</th>
                <th>Activo</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($talents as $talent)
                <tr>
                    <td><input type="checkbox" class="checkbox checkbox-sm talent-checkbox" value="{{ $talent->slug }}"></td>
                    <td>
                        @if ($talent->cover_image)
                            <img src="{{ Storage::url($talent->cover_image) }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-base-200 rounded"></div>
                        @endif
                    </td>
                    <td>{{ $talent->name }}</td>
                    <td class="text-sm opacity-70">/{{ $talent->slug }}</td>
                    <td>
                        @foreach ($talent->categories as $category)
                            <span class="badge badge-sm badge-outline">{{ $category->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if ($talent->is_active)
                            <span class="badge badge-success badge-sm">Sí</span>
                        @else
                            <span class="badge badge-ghost badge-sm">No</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.talents.edit', $talent) }}" class="btn btn-xs">Editar</a>
                        <form action="{{ route('admin.talents.destroy', $talent) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este talento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                        </form>
                    </td>
                </tr>
           @empty
                <tr>
                    <td colspan="7" class="text-center py-6 opacity-60">Aún no hay talentos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $talents->links() }}
</div>

<script>
const csrfToken = document.querySelector('input[name="_token"]')?.value
    || document.querySelector('meta[name="csrf-token"]')?.content;

const checkboxes = document.querySelectorAll('.talent-checkbox');
const selectAll = document.getElementById('select-all');
const selectedBtn = document.getElementById('batch-selected-btn');
const selectedCount = document.getElementById('selected-count');

const extraSelectedBtn = document.getElementById('extra-selected-btn');
const extraSelectedCount = document.getElementById('extra-selected-count');

function updateSelectedCount() {
    const count = document.querySelectorAll('.talent-checkbox:checked').length;
    selectedCount.textContent = count;
    selectedBtn.disabled = count === 0;
    extraSelectedCount.textContent = count;
    extraSelectedBtn.disabled = count === 0;
}

selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
});

checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

async function generateForTalent(id) {
    const url = `{{ url('admin/talents') }}/${id}/generate-content?save=1`;
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        throw new Error(data.message || `Error en talento #${id}`);
    }
}

async function runBatch(ids) {
    const progressBox = document.getElementById('batch-progress');
    const currentEl = document.getElementById('batch-current');
    const totalEl = document.getElementById('batch-total');
    const statusEl = document.getElementById('batch-status');

    document.getElementById('batch-selected-btn').disabled = true;
    document.getElementById('batch-all-btn').disabled = true;
    progressBox.classList.remove('hidden');
    totalEl.textContent = ids.length;

    let done = 0;
    let errors = 0;

    for (const id of ids) {
        currentEl.textContent = done + 1;
        statusEl.textContent = `Talento #${id}...`;
        try {
            await generateForTalent(id);
        } catch (e) {
            errors++;
            console.error(e);
        }
        done++;
    }

    statusEl.textContent = errors > 0
        ? `Listo, con ${errors} error(es). Revisa la consola.`
        : 'Listo, todo generado correctamente.';

    setTimeout(() => window.location.reload(), 1500);
}

document.getElementById('batch-selected-btn').addEventListener('click', () => {
    const ids = Array.from(document.querySelectorAll('.talent-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    if (!confirm(`Se va a generar y SOBRESCRIBIR contenido de ${ids.length} talento(s). ¿Continuar?`)) return;
    runBatch(ids);
});

document.getElementById('batch-all-btn').addEventListener('click', async () => {
    const response = await fetch('{{ route("admin.talents.all-ids") }}?only_empty=1', {
        headers: { 'Accept': 'application/json' },
    });
    const ids = await response.json();
    if (ids.length === 0) {
        alert('No hay talentos sin contenido, todos ya tienen ficha generada.');
        return;
    }
    if (!confirm(`Se va a generar contenido para ${ids.length} talento(s) SIN contenido (no se sobrescribe nada existente). ¿Continuar?`)) return;
    runBatch(ids);
});

async function generateExtraForTalent(id) {
    const url = `{{ url('admin/talents') }}/${id}/generate-extra?save=1`;
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });

    if (response.status === 404) {
        throw new Error(`Talento #${id} ya no existe, se omite.`);
    }

    if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        throw new Error(data.message || `Error en talento #${id}`);
    }
}

async function runExtraBatch(ids) {
    const progressBox = document.getElementById('extra-progress');
    const currentEl = document.getElementById('extra-current');
    const totalEl = document.getElementById('extra-total');
    const statusEl = document.getElementById('extra-status');

    document.getElementById('extra-selected-btn').disabled = true;
    document.getElementById('extra-all-btn').disabled = true;
    progressBox.classList.remove('hidden');
    totalEl.textContent = ids.length;

    let done = 0;
    let errors = 0;

    for (const id of ids) {
        currentEl.textContent = done + 1;
        statusEl.textContent = `Talento #${id}...`;
        try {
            await generateExtraForTalent(id);
        } catch (e) {
            errors++;
            console.error(e);
        }
        done++;
    }

    statusEl.textContent = errors > 0
        ? `Listo, con ${errors} error(es). Revisa la consola.`
        : 'Listo, contenido extra actualizado.';

    setTimeout(() => window.location.reload(), 1500);
}

document.getElementById('extra-selected-btn').addEventListener('click', () => {
    const ids = Array.from(document.querySelectorAll('.talent-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    if (!confirm(`Se va a buscar y guardar contenido extra de ${ids.length} talento(s) seleccionado(s). ¿Continuar?`)) return;
    runExtraBatch(ids);
});

document.getElementById('extra-all-btn').addEventListener('click', async () => {
    const response = await fetch('{{ route("admin.talents.all-ids") }}?only_empty=1&field=summary', {
        headers: { 'Accept': 'application/json' },
    });
    const ids = await response.json();
    if (ids.length === 0) {
        alert('No hay talentos sin resumen, todos ya tienen contenido extra.');
        return;
    }
    if (!confirm(`Se va a buscar contenido extra para ${ids.length} talento(s) SIN resumen. ¿Continuar?`)) return;
    runExtraBatch(ids);
});
</script>
@endsection