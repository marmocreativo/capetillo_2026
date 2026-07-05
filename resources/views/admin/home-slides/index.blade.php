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
                <th>Imagen</th>
                <th>Título</th>
                <th>Orden</th>
                <th>Activo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($homeSlides as $slide)
                <tr>
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
                    <td colspan="5" class="text-center opacity-60">No hay slides registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $homeSlides->links() }}
</div>
@endsection