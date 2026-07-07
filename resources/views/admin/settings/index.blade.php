@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Configuraciones</h1>

@if (session('status'))
    <div class="alert alert-success mb-4">{{ session('status') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error mb-4">{{ session('error') }}</div>
@endif

<div class="bg-base-100 rounded-box border border-base-300 p-6 max-w-2xl">
    <h2 class="text-lg font-semibold mb-4">Comandos de mantenimiento</h2>
    <p class="text-sm text-base-content/60 mb-6">
        Estas acciones se ejecutan directamente en el servidor. Úsalas con cuidado, especialmente "Ejecutar migraciones".
    </p>

    <div class="flex flex-col gap-3">
        @foreach ($commands as $key => $definition)
            <form
                method="POST"
                action="{{ route('admin.settings.run-command') }}"
                onsubmit="return confirm('¿Seguro que quieres ejecutar: {{ $definition['label'] }}?');"
                class="flex items-center justify-between gap-4 border border-base-300 rounded-lg p-4"
            >
                @csrf
                <input type="hidden" name="command" value="{{ $key }}">
                <span class="text-sm">{{ $definition['label'] }}</span>
                <button type="submit" class="btn btn-sm" style="background-color:#DCA54A; color:#1A1A1A; border:none;">
                    Ejecutar
                </button>
            </form>
        @endforeach
    </div>
</div>

@if (session('command_output'))
    <div class="mt-6 max-w-2xl">
        <h3 class="font-semibold mb-2">Salida del comando</h3>
        <pre class="bg-base-300 text-xs p-4 rounded-lg overflow-x-auto whitespace-pre-wrap">{{ session('command_output') }}</pre>
    </div>
@endif
@endsection