@extends('layouts.guest')

@section('guest-content')
<h2 class="text-lg font-semibold mb-4">Crear cuenta</h2>

@if ($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-3">
    @csrf

    <div class="form-control">
        <label class="label"><span class="label-text">Nombre</span></label>
        <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required autofocus>
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Correo electrónico</span></label>
        <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Contraseña</span></label>
        <input type="password" name="password" class="input input-bordered w-full" required>
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Confirmar contraseña</span></label>
        <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
    </div>

    <button type="submit" class="btn btn-primary w-full">Registrarme</button>

    <div class="text-sm mt-2 text-center">
        <a href="{{ route('login') }}" class="link link-hover">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</form>
@endsection