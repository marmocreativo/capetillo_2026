@extends('layouts.admin')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Usuarios</h1>
    <a href="{{ route('admin.users.create') }}" class="btn" style="background-color:#DCA54A; color:#1A1A1A; border:none;">
        Nuevo usuario
    </a>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4">{{ session('status') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error mb-4">{{ session('error') }}</div>
@endif

<form method="GET" class="mb-4 max-w-sm">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Buscar por nombre o email..."
        class="input input-bordered w-full"
    >
</form>

<div class="overflow-x-auto bg-base-100 rounded-box border border-base-300">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-ghost">Editar</a>

                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar a {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost text-error">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-base-content/50">No hay usuarios.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection