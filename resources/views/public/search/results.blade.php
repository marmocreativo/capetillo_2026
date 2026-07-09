@extends('layouts.public')

@section('title', 'Resultados de búsqueda' . ($query ? ': ' . $query : '') . ' | ' . config('app.name'))
@section('meta_description', 'Encuentra talento artístico en Capetillo Producciones.')

@section('public-content')

<div class="hero min-h-[60vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1600&q=80');
            background-size: cover; background-position: center;"> 
    <div class="hero-overlay bg-black/70"></div>
    <div class="hero-content flex-col md:flex-row text-start w-full pt-32">
        <div class="max-w-2xl w-full">
            <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight">
                Resultados de búsqueda
            </h1>
            <p class="py-4 text-lg text-white/90">
                @if($query !== '')
                    Mostrando resultados para "<span class="font-semibold">{{ $query }}</span>"
                @else
                    Escribe algo en el buscador para encontrar talento.
                @endif
            </p>
        </div>
        <div class="shadow w-full mb-10 bg-base-100/50 backdrop-lg">
            <div class="stat place-items-center">
                <div class="stat-title">Resultados encontrados</div>
                <div class="stat-value text-primary">{{ $talents->count() }}</div>
                <div class="stat-desc">Para "{{ $query }}"</div>
            </div>
        </div>
    </div>
</div>


<div class="max-w-7xl mx-auto p-2">
    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse ($talents as $talent)
                @php $category = $talent->categories->first(); @endphp
                <a href="{{ $category ? route('talents.show', [$category, $talent]) : '#' }}"
                class="relative aspect-[4/5] overflow-hidden shadow hover:shadow-lg transition-shadow group">
                    @if ($talent->cover_image)
                        <img src="{{ Storage::url($talent->cover_image) }}" alt="{{ $talent->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-base-300 flex items-center justify-center text-4xl opacity-30">🎤</div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                    <div class="absolute inset-0 flex items-end justify-center pb-4 text-center px-2">
                        <h2 class="text-primary font-bold text-sm leading-tight">{{ $talent->name }}</h2>
                    </div>
                </a>
            @empty
                <p class="opacity-60 col-span-full">No encontramos talento que coincida con tu búsqueda.</p>
            @endforelse
        </div>
    </div>

<div class="hero bg-base-200/50 backdrop-lg p-10 my-6">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">

            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¡Estamos a un mensaje de distancia!</h2>
                <p class="opacity-80">
                    Cuéntanos qué tipo de evento estás planeando y te ayudamos a encontrar y contratar
                    al talento ideal, con la logística resuelta de principio a fin.
                </p>
            </div>

            <div class="flex items-center justify-center">
                <a href="{{ route('contact.page') }}" class="btn btn-primary btn-lg">
                    Contrata ahora
                </a>
            </div>

        </div>
    </div>
</div>

@endsection