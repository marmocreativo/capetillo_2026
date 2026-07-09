@extends('layouts.public')

@section('title', ($category->meta_title ?: $category->name) . ' | ' . config('app.name'))
@section('meta_description', $category->meta_description ?: 'Contrata talento de la categoría ' . $category->name . ' con Capetillo Producciones.')
@section('meta_keywords', $category->meta_keywords ?: '')

@section('public-content')

<div class="hero min-h-[70vh] overflow-hidden relative mb-10"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('{{ asset('images/fondo_hero.jpg') }}');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto px-6 pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-center w-full">

        <div class="hidden lg:block lg:col-span-2 overflow-hidden shadow-lg aspect-[4/5] max-h-[45vh] mx-auto bg-base-200">
                @if ($category->cover_image)
                    <img src="{{ Storage::url($category->cover_image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=800&q=80" alt="{{ $category->name }}" class="w-full h-full object-cover">
                @endif
            </div>

            <div class="text-center lg:text-left lg:col-span-3">
                <h1 class="text-2xl md:text-4xl font-bold text-white leading-tight mb-3">
                    {{ $category->name }}
                </h1>

                @if ($category->meta_description)
                    <p class="text-white/90 mb-6">{{ $category->meta_description }}</p>
                @endif

                <div class=" bg-base-100/50 backdrop-lg">
                    <div class="stat place-items-center">
                        <div class="stat-title">Talento disponible</div>
                        <div class="stat-value text-primary">{{ $talents->count() }}</div>
                        <div class="stat-desc">En {{ $category->name }}</div>
                    </div>
                    <div class="stat place-items-center">
                        <div class="stat-title">Disponibilidad</div>
                        <div class="stat-value text-primary text-2xl">Todo México</div>
                        <div class="stat-desc">Eventos privados y corporativos</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="max-w-7xl mx-auto p-2">
    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>
    

    @if ($featuredTalents->isNotEmpty())
        <h2 class="text-xl font-bold text-primary mb-3">Destacados</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach ($featuredTalents as $talent)
                <a href="{{ route('talents.show', [$category, $talent]) }}"
                class="relative aspect-[4/5] overflow-hidden shadow hover:shadow-lg transition-shadow group ring-2 ring-primary/60">
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
            @endforeach
        </div>
    @endif

    @if ($regularTalents->isNotEmpty())
        @if ($featuredTalents->isNotEmpty())
            <h2 class="text-xl font-bold mb-3">Todo el talento</h2>
        @endif
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($regularTalents as $talent)
                <a href="{{ route('talents.show', [$category, $talent]) }}"
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
            @endforeach
        </div>
    @endif

    @if ($featuredTalents->isEmpty() && $regularTalents->isEmpty())
        <p class="opacity-60">Aún no hay talento registrado en esta categoría.</p>
    @endif
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