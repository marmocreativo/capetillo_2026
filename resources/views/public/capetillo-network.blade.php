@extends('layouts.public')

@section('title', 'Capetillo Network | ' . config('app.name'))
@section('meta_description', 'Capetillo Network: marketing de influencers y visibilidad de marca. Campañas publicitarias, activaciones y estrategia digital integral.')

@section('public-content')

<div class="hero min-h-[45vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('{{ asset('images/fondo_concierto.webp') }}');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto px-6 pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-center w-full">

            <div class="flex lg:col-span-2 items-center justify-center">
                <img src="{{ asset('images/capetillo_network.png') }}"
                     alt="Capetillo Network" class="max-w-full max-h-[35vh] object-contain">
            </div>

            <div class="text-center lg:text-left lg:col-span-3">
                <h1 class="text-3xl md:text-5xl font-bold text-white">CAPETILLO NETWORK</h1>
                <p class="py-3 text-lg text-white/90">Conectamos marcas con el talento artístico correcto para crear campañas que sí se sienten auténticas</p>
            </div>

        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-2">
    <div class="max-w-3xl mx-auto text-center mb-12">
        <h2 class="text-2xl font-bold mb-3">¿Qué es Capetillo Network?</h2>
        <p class="opacity-80">
            Somos la vertical de <strong>marketing con influencers</strong> de Capetillo Producciones.
            Aprovechamos más de una década relacionando marcas con artistas, comediantes, músicos y
            creadores de contenido para diseñar campañas donde el talento no solo presta su imagen,
            sino que conecta genuinamente con la audiencia.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-14">
        <div class="card bg-base-100 shadow-lg relative overflow-hidden">
            <hero-icon-outline name="megaphone" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary">Servicios</h3>
                <ul class="mt-2 space-y-1 text-sm">
                    <li>• Matchmaking entre marcas y artistas o influencers alineados a su identidad</li>
                    <li>• Campañas publicitarias en medios tradicionales y digitales</li>
                    <li>• Activaciones de marca con talento en vivo</li>
                    <li>• Estrategia digital integral</li>
                    <li>• Creación de contenido: streams, videos, reels</li>
                    <li>• Amplificación y viralización de campañas</li>
                </ul>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg relative overflow-hidden">
            <hero-icon-outline name="sparkles" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary">Ejemplos de trabajo</h3>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach (['Selección de spokespersons e influencers para marcas', 'Alianzas marca-artista a la medida', 'Generación de contenido orgánico', 'Contenido patrocinado', 'Participación en eventos y activaciones de marca'] as $exampleType)
                        <span class="text-sm md:text-md badge badge-outline badge-primary h-auto whitespace-normal text-center py-1.5 leading-snug">{{ $exampleType }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mb-14">
        <h2 class="text-2xl font-bold mb-1">Cómo trabajamos</h2>
        <p class="opacity-70 mb-6">Del match marca-talento a la campaña en vivo, de principio a fin</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body items-center text-center">
                    <hero-icon-outline name="magnifying-glass" class="h-10 w-10 text-primary mb-2"></hero-icon-outline>
                    <h3 class="font-bold">Match marca-talento</h3>
                    <p class="text-sm opacity-70">Del roster de Capetillo, identificamos al artista o influencer cuya audiencia y personalidad conectan de verdad con tu marca.</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body items-center text-center">
                    <hero-icon-outline name="pencil-square" class="h-10 w-10 text-primary mb-2"></hero-icon-outline>
                    <h3 class="font-bold">Estrategia de contenido</h3>
                    <p class="text-sm opacity-70">Diseñamos la narrativa digital: streams, videos, reels y piezas para cada plataforma.</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body items-center text-center">
                    <hero-icon-outline name="rocket-launch" class="h-10 w-10 text-primary mb-2"></hero-icon-outline>
                    <h3 class="font-bold">Ejecución y viralización</h3>
                    <p class="text-sm opacity-70">Producimos, publicamos y potenciamos el alcance de cada campaña junto al talento.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="hero bg-base-200 rounded-box p-10 my-12">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">

            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¿Listo para conectar tu marca con el talento ideal?</h2>
                <p class="opacity-80">Cuéntanos qué estás planeando y te ayudamos a encontrar al artista o influencer perfecto para tu campaña.</p>
            </div>

            <div class="flex items-center justify-center">
                <a href="{{ route('contact.page') }}" class="btn btn-primary btn-lg">Contáctanos</a>
            </div>

        </div>
    </div>
</div>

@endsection