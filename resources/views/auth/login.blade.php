@extends('layouts.guest')

@section('guest-content')
<div class="relative min-h-screen flex items-center justify-center overflow-hidden">

    {{-- Fondo --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/fondo_hero.jpg') }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-base-100 via-black/40 to-transparent"></div>
    </div>

    {{-- Contenido --}}
    <div class="relative z-10 w-full max-w-md px-6 py-10">

        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/logo_capetillo_blanco.svg') }}" alt="Capetillo Producciones" class="h-16 w-auto">
        </div>

        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl shadow-2xl p-8">

            <h2 class="text-xl font-semibold text-white mb-1">Iniciar sesión</h2>
            <p class="text-sm text-white/50 mb-6">Accede al panel de administración</p>

            @if ($errors->any())
                <div class="alert alert-error mb-4 py-2 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text text-white/70">Correo electrónico</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="input input-bordered w-full bg-white/5 border-white/15 text-white placeholder:text-white/30 focus:border-primary focus:bg-white/10"
                        placeholder="tucorreo@capetilloproducciones.mx"
                        required
                        autofocus
                    >
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text text-white/70">Contraseña</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="input input-bordered w-full bg-white/5 border-white/15 text-white placeholder:text-white/30 focus:border-primary focus:bg-white/10"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <label class="label cursor-pointer justify-start gap-2 py-0">
                    <input type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary">
                    <span class="label-text text-white/60 text-sm">Recordarme</span>
                </label>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    Entrar
                </button>

                <div class="flex justify-center text-sm mt-1">
                    <a href="{{ route('password.request') }}" class="link link-hover text-white/50 hover:text-white">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-white/30 mt-6">
            &copy; {{ date('Y') }} Capetillo Producciones. Todos los derechos reservados.
        </p>
    </div>
</div>
@endsection