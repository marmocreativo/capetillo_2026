@extends('layouts.public')

@section('title', 'Golden Party | ' . config('app.name'))
@section('meta_description', 'Golden Party: organización de eventos, decoración y ambientación, banquetes, tecnología y fiestas temáticas para bodas, XV años, eventos corporativos y más.')

@section('public-content')

<div class="hero min-h-[45vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('{{ asset('images/fondo_concierto.webp') }}');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto px-6 pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-center w-full">

            <div class="flex lg:col-span-2 items-center justify-center">
                <img src="{{ asset('images/Golden Party Dorado.png') }}"
                     alt="Golden Party" class="max-w-full max-h-[35vh] object-contain">
            </div>

            <div class="text-center lg:text-left lg:col-span-3">
                <h1 class="text-3xl md:text-5xl font-bold text-white">GOLDEN PARTY</h1>
                <p class="py-3 text-lg text-white/90">Decoración y ambientación, Banquetes, Tecnología, Temáticas</p>
            </div>

        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-2">

    <div class="max-w-3xl mx-auto text-center mb-12">
        <h2 class="text-2xl font-bold mb-3">¿Qué es Golden Party?</h2>
        <p class="opacity-80">
            Somos una empresa hermana de Capetillo Producciones especializada en <strong>eventos sociales</strong>
            con servicios específicos y de gran calidad para altos niveles sociales.
        </p>
    </div>

    {{-- =========================================================
         NUMERALIA
    ========================================================== --}}
    <div class="stats stats-vertical md:stats-horizontal shadow w-full mb-14 bg-base-100">
        <div class="stat place-items-center">
            <div class="stat-title">Eventos creados</div>
            <div class="stat-value text-primary">+12 MIL</div>
            <div class="stat-desc">Producidos y coordinados</div>
        </div>
        <div class="stat place-items-center">
            <div class="stat-title">Empresas y familias</div>
            <div class="stat-value text-primary">+750</div>
            <div class="stat-desc">Que han confiado en nosotros</div>
        </div>
        <div class="stat place-items-center">
            <div class="stat-title">Espectadores</div>
            <div class="stat-value text-primary">+10 M</div>
            <div class="stat-desc">Disfrutando eventos de alta calidad</div>
        </div>
        <div class="stat place-items-center">
            <div class="stat-title">Trayectoria</div>
            <div class="stat-value text-primary">27+</div>
            <div class="stat-desc">Años en la industria</div>
        </div>
    </div>

    {{-- =========================================================
         EVENTOS (grid dinámico)
    ========================================================== --}}
    @if ($events->isNotEmpty())
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-center mb-2">Organización de eventos</h2>
            <p class="text-center opacity-70 mb-8 max-w-2xl mx-auto">
                Cada tipo de evento tiene su propio equipo especializado, listo para hacer de tu celebración algo inolvidable.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}"
                       class="card bg-base-100 shadow-lg overflow-hidden group hover:shadow-xl transition-shadow">
                        <div class="aspect-[4/3] overflow-hidden bg-base-300">
                            @if ($event->cover_image)
                                <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center opacity-30">
                                    <hero-icon-outline name="sparkles" class="h-12 w-12"></hero-icon-outline>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-5">
                            <h3 class="card-title text-primary">{{ $event->title }}</h3>
                            @if ($event->summary)
                                <p class="text-sm opacity-70 line-clamp-2">{{ $event->summary }}</p>
                            @endif
                            <span class="link link-hover text-sm font-semibold mt-2">Conoce más →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- =========================================================
         POR QUÉ CONTRATARNOS (grid de cards con heroicons)
    ========================================================== --}}
    <div class="mb-14">
        <h2 class="text-2xl font-bold text-center mb-8">¿Por qué contratarnos?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ([
                ['icon' => 'building-storefront', 'title' => 'Empresa hermana de Capetillo Producciones', 'text' => 'Respaldo, experiencia y estructura de una de las agencias líderes de entretenimiento en México.'],
                ['icon' => 'microphone', 'title' => 'Acceso directo a talento artístico', 'text' => 'Integramos a nuestra exclusiva plantilla de artistas para dar un toque único a tu evento.'],
                ['icon' => 'clipboard-document-check', 'title' => 'Coordinación integral', 'text' => 'Un solo punto de contacto para toda la logística de tu evento, de principio a fin.'],
                ['icon' => 'star', 'title' => 'Proveedores de primer nivel', 'text' => 'Locaciones, mobiliario y servicios seleccionados con los más altos estándares de calidad.'],
                ['icon' => 'sparkles', 'title' => 'Experiencias a la medida', 'text' => 'Diseñamos cada detalle en función de tus gustos, tu estilo y las necesidades de tu evento.'],
                ['icon' => 'trophy', 'title' => '+27 años de trayectoria', 'text' => 'Más de dos décadas de experiencia organizando eventos sociales inolvidables.'],
            ] as $item)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body items-center text-center">
                        <div class="rounded-full bg-primary/10 p-4 mb-2">
                            <hero-icon-outline name="{{ $item['icon'] }}" class="h-8 w-8 text-primary"></hero-icon-outline>
                        </div>
                        <h3 class="card-title text-primary text-lg">{{ $item['title'] }}</h3>
                        <p class="text-sm opacity-70">{{ $item['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- =========================================================
         SERVICIOS (grid de píldoras)
    ========================================================== --}}
    <div class="mb-14">
        <h2 class="text-2xl font-bold text-center mb-8">Nuestros servicios</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach ([
                'Decoración y ambientación',
                'Renta de mobiliario, loza y cristalería',
                'Banquetes (alimentos y bebidas)',
                'Personal de servicio',
                'Tecnología',
                'Fiestas temáticas',
            ] as $servicio)
                <span class="badge badge-lg badge-outline badge-primary h-auto whitespace-normal text-center py-3 px-5 text-sm font-semibold">
                    {{ $servicio }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- =========================================================
         GALERÍAS (carruseles infinitos, contenido fijo)
    ========================================================== --}}
    @php
        $galleries = collect([
            'Decoración y Ambientación' => [
                'subtitle' => 'Decoración en globos, Candy Bar, Decoración con flor, telaje y escenografías',
                'images' => collect([
                    'deco_1.png','deco_2.png','deco_3.png','deco_4.png',
                    'deco_5.png','deco_6.png','deco_7.png','deco_8.png',
                ]),
            ],
            'Banquetes' => [
                'subtitle' => null,
                'images' => collect([
                    'banquete_1.png','banquete_2.png','banquete_3.png','banquete_4.png',
                    'banquete_5.png','banquete_6.png','banquete_7.jpeg','banquete_8.jpeg',
                    'banquete_9.jpeg','banquete_10.png','banquete_11.jpeg',
                ]),
            ],
            'Tecnología' => [
                'subtitle' => 'Pantallas de video de alta calidad, Equipos de audio e iluminación, Efectos especiales, Escenarios, templetes y tarimas.',
                'images' => collect([
                    'tecno_1.png','tecno_2.png','tecno_3.png','tecno_4.png',
                    'tecno_5.png','tecno_6.png','tecno_7.png','tecno_8.png',
                    'tecno_9.png','tecno_10.png','tecno_11.png','tecno_12.png'
                ]),
            ],
            'Fiestas Temáticas' => [
                'subtitle' => 'Experiencias inmersivas, Photo opportunity, Shows y animación, Dj y talento artístico, Celebridades.',
                'images' => collect([
                    'tematica_1.png','tematica_2.png','tematica_3.png','tematica_4.jpeg',
                    'tematica_5.jpeg','tematica_6.jpeg','tematica_7.jpeg','tematica_8.jpeg',
                ]),
            ],
        ]);
        $baseUploadUrl = 'https://capetilloproducciones.mx/images/goldenparty/';
    @endphp

    @foreach ($galleries as $title => $gallery)
        <div class="mb-14">
            <h2 class="text-2xl font-bold mb-1">{{ $title }}</h2>
            @if ($gallery['subtitle'])
                <p class="opacity-70 mb-4">{{ $gallery['subtitle'] }}</p>
            @endif

            @php $carouselId = 'gp-carousel-' . \Illuminate\Support\Str::slug($title); @endphp
            <div id="{{ $carouselId }}" class="gp-marquee-track flex gap-4 overflow-x-auto p-2 bg-base-100 rounded-box" style="scrollbar-width: none;">
                @foreach ($gallery['images']->concat($gallery['images']) as $image)
                    <div class="shrink-0 w-56 sm:w-64 aspect-square rounded-box overflow-hidden bg-base-200">
                        <img src="{{ $baseUploadUrl . $image }}" alt="{{ $title }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div class="hero bg-base-200 rounded-box p-10 my-12">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">

            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¿Listo para tu evento?</h2>
                <p class="opacity-80">Cuéntanos qué estás planeando y armamos la propuesta perfecta para ti.</p>
            </div>

            <div class="flex items-center justify-center">
                <a href="{{ route('contact.page') }}" class="btn btn-primary btn-lg">Contáctanos</a>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    document.querySelectorAll('.gp-marquee-track').forEach(function (track) {
        let paused = false;
        const speed = 0.6;

        track.addEventListener('mouseenter', () => paused = true);
        track.addEventListener('mouseleave', () => paused = false);

        function step() {
            if (!paused) {
                track.scrollLeft += speed;
                if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 1) {
                    track.scrollLeft = 0;
                }
            }
            requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    });
})();
</script>

@endsection