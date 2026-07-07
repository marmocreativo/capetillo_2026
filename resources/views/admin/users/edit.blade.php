@extends('layouts.admin')

@section('admin-content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold mb-6">Editar usuario</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-col gap-4">
        @csrf
        @method('PUT')

        <div class="form-control">
            <label class="label"><span class="label-text">Nombre</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input input-bordered w-full" required>
            @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Email</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input input-bordered w-full" required>
            @error('email') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="divider">Cambiar contraseña (opcional)</div>

        <div class="form-control">
            <label class="label"><span class="label-text">Nueva contraseña</span></label>
            <input type="password" name="password" class="input input-bordered w-full" placeholder="Dejar en blanco para no cambiar">
            @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Confirmar nueva contraseña</span></label>
            <input type="password" name="password_confirmation" class="input input-bordered w-full">
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn" style="background-color:#DCA54A; color:#1A1A1A; border:none;">
                Guardar cambios
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection