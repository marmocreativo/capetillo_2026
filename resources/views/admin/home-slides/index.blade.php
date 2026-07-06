@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Slider del home</h1>
    <a href="{{ route('admin.home-slides.create') }}" class="btn btn-primary">Nuevo slide</a>
</div>

@if(session('status'))
    <div class="alert alert-success mb-6">{{ session('status') }}</div>
@endif

<div class="overflow-x-auto bg-base-100 rounded-box shadow">
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

<div class="mt-6">
    {{ $homeSlides->links() }}
</div>

<p class="text-xs opacity-60 mt-2">El orden por arrastre solo reordena los slides visibles en esta página.</p>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const csrfToken = document.querySelector('input[name="_token"]')?.value
    || document.querySelector('meta[name="csrf-token"]')?.content;

const sortableBody = document.getElementById('sortable-body');
if (sortableBody) {
    new Sortable(sortableBody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: async () => {
            const order = Array.from(sortableBody.querySelectorAll('tr[data-id]')).map(tr => tr.dataset.id);
            try {
                await fetch('{{ route("admin.home-slides.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order }),
                });
            } catch (e) {
                alert('No se pudo guardar el nuevo orden.');
            }
        },
    });
}
</script>
@endsection