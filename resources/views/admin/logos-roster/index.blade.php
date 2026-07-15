@extends('layouts.admin')

@section('admin-content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold">Logos roster</h1>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4 text-sm">{{ session('status') }}</div>
@endif

<div class="card bg-base-100 border border-base-300 mb-6">
    <div class="card-body">
        <h3 class="font-semibold mb-3">Subir logo</h3>

        <form method="POST" action="{{ route('admin.logos-roster.store') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            @csrf
            <input type="file" name="image" accept="image/*" class="file-input file-input-bordered w-full sm:max-w-xs" required>
            <button type="submit" class="btn btn-primary">Subir</button>
        </form>
        @error('image') <p class="text-error text-xs mt-2">{{ $message }}</p> @enderror
        <p class="text-xs opacity-60 mt-2">Las imágenes se convierten automáticamente a WebP con un ancho máximo de 600px.</p>
    </div>
</div>

@if ($logosRoster->isEmpty())
    <p class="text-sm opacity-60">Aún no se han subido logos.</p>
@else
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach ($logosRoster as $logo)
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body p-3 items-center">
                    <img src="{{ Storage::url($logo->image) }}" class="w-full aspect-square object-contain">
                    <form method="POST" action="{{ route('admin.logos-roster.destroy', $logo) }}" class="w-full mt-2" onsubmit="return confirm('¿Eliminar este logo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-outline btn-xs w-full">Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $logosRoster->links() }}
    </div>
@endif
@endsection