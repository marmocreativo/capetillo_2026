@extends('layouts.admin')

@section('admin-content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold">Categorías</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Nueva categoría</a>
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

    <select name="status" class="select select-bordered">
        <option value="">Todos los estados</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivas</option>
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>

    @if (request('search') || request('status'))
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">Limpiar</a>
    @endif
</form>

{{-- Tabla — solo desktop --}}
<div class="overflow-x-auto bg-base-100 rounded-box shadow hidden lg:block">
    <table class="table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Talentos</th>
                <th>Activa</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>
                        @if ($category->cover_image)
                            <img src="{{ Storage::url($category->cover_image) }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-base-200 rounded"></div>
                        @endif
                    </td>
                    <td>{{ $category->name }}</td>
                    <td class="text-sm opacity-70">/{{ $category->slug }}</td>
                    <td>
                        <a href="{{ route('admin.talents.index', ['category' => $category->slug]) }}" class="link link-hover">
                            {{ $category->talents()->count() }}
                        </a>
                    </td>
                    <td>
                        @if ($category->is_active)
                            <span class="badge badge-success badge-sm">Sí</span>
                        @else
                            <span class="badge badge-ghost badge-sm">No</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-xs">Editar</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta categoría?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-6 opacity-60">Aún no hay categorías.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Cards — solo móvil --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:hidden">
    @forelse ($categories as $category)
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4 gap-2">
                <div class="flex gap-3">
                    @if ($category->cover_image)
                        <img src="{{ Storage::url($category->cover_image) }}" class="w-16 h-16 object-cover rounded shrink-0">
                    @else
                        <div class="w-16 h-16 bg-base-200 rounded shrink-0"></div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="font-medium truncate">{{ $category->name }}</p>
                        <p class="text-xs opacity-60 truncate">/{{ $category->slug }}</p>
                        <a href="{{ route('admin.talents.index', ['category' => $category->slug]) }}" class="link link-hover text-sm">
                            {{ $category->talents()->count() }} talento(s)
                        </a>
                    </div>
                </div>

                <div>
                    @if ($category->is_active)
                        <span class="badge badge-success badge-sm">Activa</span>
                    @else
                        <span class="badge badge-ghost badge-sm">Inactiva</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-xs flex-1">Editar</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-error btn-outline">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center py-6 opacity-60 col-span-full">Aún no hay categorías.</p>
    @endforelse
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection