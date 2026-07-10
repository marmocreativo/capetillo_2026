@extends('layouts.admin')

@section('admin-content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold">Slider del home</h1>
    <a href="{{ route('admin.home-slides.create') }}" class="btn btn-primary">Nuevo slide</a>
</div>

@if(session('status'))
    <div class="alert alert-success mb-6">{{ session('status') }}</div>
@endif

<p class="text-xs opacity-60 mb-2">El orden por arrastre solo reordena los slides visibles en esta página.</p>

{{-- Tabla — solo desktop --}}
<div class="overflow-x-auto bg-base-100 rounded-box shadow hidden lg:block">
    <table class="table">
        <thead>
            <tr>
                <th class="w-8"></th>
                <th>Imagen</th>
                <th>Título</th>
                <th>Orden</th>
                <th>Activo</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="sortable-body">
            @forelse($homeSlides as $slide)
                <tr data-id="{{ $slide->id }}" class="cursor-move">
                    <td class="drag-handle text-center opacity-50">⠿</td>
                    <td>
                        @if($slide->image)
                            <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}" class="h-16 rounded bg-base-300 object-contain">
                        @endif
                    </td>
                    <td>{{ $slide->title }}</td>
                    <td>{{ $slide->order }}</td>
                    <td>
                        <span class="badge {{ $slide->is_active ? 'badge-success' : 'badge-ghost' }}">
                            {{ $slide->is_active ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.home-slides.edit', $slide) }}" class="btn btn-xs">Editar</a>
                        <form action="{{ route('admin.home-slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('¿Eliminar este slide?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center opacity-60">No hay slides registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Cards — solo móvil (también reordenables por arrastre) --}}
<div id="sortable-cards" class="flex flex-col gap-3 lg:hidden">
    @forelse($homeSlides as $slide)
        <div data-id="{{ $slide->id }}" class="card bg-base-100 border border-base-300">
            <div class="card-body p-4 gap-2">
                <div class="flex gap-3">
                    <span class="drag-handle-card text-xl opacity-50 cursor-move self-center">⠿</span>

                    @if($slide->image)
                        <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}" class="h-16 w-16 rounded bg-base-300 object-contain shrink-0">
                    @else
                        <div class="h-16 w-16 rounded bg-base-300 shrink-0"></div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="font-medium truncate">{{ $slide->title }}</p>
                        <p class="text-xs opacity-60">Orden: {{ $slide->order }}</p>
                    </div>
                </div>

                <div>
                    <span class="badge badge-sm {{ $slide->is_active ? 'badge-success' : 'badge-ghost' }}">
                        {{ $slide->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('admin.home-slides.edit', $slide) }}" class="btn btn-xs flex-1">Editar</a>
                    <form action="{{ route('admin.home-slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('¿Eliminar este slide?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center py-6 opacity-60">No hay slides registrados.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $homeSlides->links() }}
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const csrfToken = document.querySelector('input[name="_token"]')?.value
    || document.querySelector('meta[name="csrf-token"]')?.content;

async function saveOrder(ids) {
    try {
        await fetch('{{ route("admin.home-slides.reorder") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order: ids }),
        });
    } catch (e) {
        alert('No se pudo guardar el nuevo orden.');
    }
}

const sortableBody = document.getElementById('sortable-body');
if (sortableBody) {
    new Sortable(sortableBody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: () => {
            const order = Array.from(sortableBody.querySelectorAll('tr[data-id]')).map(tr => tr.dataset.id);
            saveOrder(order);
        },
    });
}

const sortableCards = document.getElementById('sortable-cards');
if (sortableCards) {
    new Sortable(sortableCards, {
        handle: '.drag-handle-card',
        animation: 150,
        onEnd: () => {
            const order = Array.from(sortableCards.querySelectorAll('[data-id]')).map(el => el.dataset.id);
            saveOrder(order);
        },
    });
}
</script>
@endsection