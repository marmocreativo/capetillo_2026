@extends('layouts.public')

@section('title', 'Talento | ' . config('app.name'))

@section('public-content')

<div class="hero min-h-[50vh] md:min-h-[40vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1600&q=80');
            background-size: cover; background-position: center;"> 
    <div class="hero-overlay bg-black/70"></div>
    <div class="hero-content text-center">
        <div class="max-w-2xl">
            <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight">
                Nuestro Talento
            </h1>
            <p class="py-4 md:text-lg text-white/90">
                Comediantes, cantantes, bandas, actores y conferencistas listos para hacer de tu evento
                una experiencia inolvidable. Explora nuestras categorías y contrata al talento perfecto
                para tu próxima celebración.
            </p>
        </div>
    </div>
</div>
<div class="max-w-7xl mx-auto p-2">
    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.png') }}');"></div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category) }}"
                class="relative aspect-[4/5]  overflow-hidden shadow hover:shadow-lg transition-shadow group">
                @if ($category->cover_image)
                    <img src="{{ Storage::url($category->cover_image) }}" alt="{{ $category->name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=800&q=80"
                        alt="{{ $category->name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                <div class="absolute inset-0 flex items-end justify-center pb-6 text-center px-4">
                    <h2 class="text-primary font-bold text-xl">{{ $category->name }}</h2>
                </div>
            </a>
        @endforeach
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
                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-lg">
                    Contrata ahora
                </a>
            </div>

        </div>
    </div>
</div>
@endsection