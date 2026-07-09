@extends('layouts.public')

@section('title', 'Live Media | ' . config('app.name'))
@section('meta_description', 'Capetillo Live Media: producción audiovisual, tecnología y experiencias en vivo para conciertos, eventos corporativos y espectáculos.')

@section('public-content')

<div class="hero min-h-[40vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('{{ asset('images/fondo_concierto.webp') }}');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto px-6 pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-center w-full">

            <div class="flex lg:col-span-2 items-center justify-center">
                <img src="{{ asset('images/live media blanco.png') }}"
                     alt="Live Media" class="max-w-full max-h-[35vh] object-contain">
            </div>

            <div class="text-center lg:text-left lg:col-span-3">
                <h1 class="text-3xl md:text-5xl font-bold text-white">LIVE MEDIA</h1>
                <p class="py-3 text-lg text-white/90">Nos complace presentar una nueva empresa de tecnología</p>
            </div>

        </div>
    </div>
</div>
<div class="max-w-7xl mx-auto p-2">
<div class="max-w-3xl mx-auto mb-12">
    <h2 class="text-2xl font-bold mb-3 text-center">¿Qué es Capetillo Live Media?</h2>
    <p class="opacity-80 mb-4">
        Artistas, producciones, empresas y proyectos que consideren esencial proyectar a través de imágenes,
        experiencias y emociones un mensaje, una idea, un complemento o hasta una explosión a los sentidos.
        Capetillo Live Media fue creada para producir esa idea que será clave en una presentación, concierto,
        evento social, deportivo o político, llevada a la realidad a través de la tecnología más actual.
    </p>
    <p class="opacity-80">
        De la mano de Hugo Rosete, productor en audio, video y luces con más de 38 años en la industria del
        entretenimiento, apoyados en la creatividad y la tecnología innovadora, con el objetivo de crear
        experiencias únicas a la medida de cada proyecto.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-14">
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h3 class="card-title text-primary">Ha producido para artistas como</h3>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach (['Ricardo Arjona', 'Marc Anthony', 'Chayanne', 'Manuel Mijares', 'Emmanuel', 'Lucero', 'Tour Amigos', 'Yuri'] as $artist)
                    <span class="badge badge-outline badge-primary">{{ $artist }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h3 class="card-title text-primary">Y para gobiernos y corporativos como</h3>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach (['SEDENA', 'Banorte', 'Cemento Cruz Azul', 'Nissan', 'Volaris', 'Televisa', 'TV Azteca', 'Grupo Imagen'] as $client)
                    <span class="badge badge-outline badge-primary">{{ $client }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="mb-14">
    <h2 class="text-2xl font-bold text-center mb-6">Producciones</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach (['pFF92kC_4tc', 'jvQYMpV9PDc', 'YBbfppyHOHw', 'uhf3BOYH1-g', 'KdURjruKjps', '9Z15TyHyXVc', 'n7UDQOVzOx4'] as $videoId)
            <div class="aspect-video rounded-box overflow-hidden">
                <iframe class="w-full h-full"
                    src="https://www.youtube.com/embed/{{ $videoId }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @endforeach
    </div>
</div>
</div>
<div class="hero bg-base-200 rounded-box p-10 my-12">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">

            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¿Tienes un proyecto en mente?</h2>
                <p class="opacity-80">Hablemos de cómo podemos llevar tu idea a la realidad.</p>
            </div>

            <div class="flex items-center justify-center">
                <a href="{{ route('contact.page') }}" class="btn btn-primary btn-lg">Contáctanos</a>
            </div>

        </div>
    </div>
</div>

@endsection