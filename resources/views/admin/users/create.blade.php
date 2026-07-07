@extends('layouts.admin')

@section('admin-content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold mb-6">Nuevo usuario</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="flex flex-col gap-4">
        @csrf

        <div class="form-control">
            <label class="label"><span class="label-text">Nombre</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required>
            @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Email</span></label>
            <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
            @error('email') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Contraseña</span></label>
            <input type="password" name="password" class="input input-bordered w-full" required>
            @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Confirmar contraseña</span></label>
            <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn" style="background-color:#DCA54A; color:#1A1A1A; border:none;">
                Crear usuario
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection