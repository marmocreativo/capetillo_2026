@extends('layouts.public')

@section('title', 'Golden Party | ' . config('app.name'))
@section('meta_description', 'Golden Party: decoración y ambientación, banquetes, tecnología y fiestas temáticas para bodas, XV años, eventos corporativos y más.')

@section('public-content')

<div class="hero min-h-[45vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('{{ asset('images/fondo_concierto.png') }}');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-center w-full">

            <div class="hidden lg:flex lg:col-span-2 items-center justify-center">
                <img src="https://capetilloproducciones.mx/wp-content/uploads/2024/11/Golden-Party-2-1024x768.png"
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-14">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h3 class="card-title text-primary">Servicios</h3>
                <ul class="mt-2 space-y-1 text-sm">
                    <li>• Decoración y ambientación</li>
                    <li>• Renta de mobiliario, loza y cristalería</li>
                    <li>• Banquetes (alimentos y bebidas)</li>
                    <li>• Personal de servicio</li>
                    <li>• Tecnología</li>
                    <li>• Fiestas temáticas</li>
                </ul>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h3 class="card-title text-primary">Tipo de eventos</h3>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach (['Bodas', 'XV años', 'Graduaciones', 'Open House', 'Birthday party', 'Gender reveal', 'Fiestas corporativas y empresariales', 'Convenciones', 'Lanzamientos de marcas y productos', 'Inauguraciones'] as $eventType)
                        <span class="badge badge-outline badge-primary">{{ $eventType }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @php
        $galleries = [
            'Decoración y Ambientación' => [
                'subtitle' => 'Decoración en globos, Candy Bar, Decoración con flor, telaje y escenografías',
                'images' => [
                    'deco_1.png','deco_2.png','deco_3.png','deco_4.png',
                    'deco_5.png','deco_6.png','deco_7.png','deco_8.png',
                ],
            ],
            'Banquetes' => [
                'subtitle' => null,
                'images' => [
                    'banquete_1.png','banquete_2.png','banquete_3.png','banquete_4.png',
                    'banquete_5.png','banquete_6.png','banquete_7.jpeg','banquete_8.jpeg',
                    'banquete_9.jpeg','banquete_10.png','banquete_11.jpeg',
                ],
            ],
            'Tecnología' => [
                'subtitle' => 'Pantallas de video de alta calidad, Equipos de audio e iluminación, Efectos especiales, Escenarios, templetes y tarimas.',
                'images' => [
                    'tecno_1.png','tecno_2.png','tecno_3.png','tecno_4.png',
                    'tecno_5.png','tecno_6.png','tecno_7.png','tecno_8.png',
                    'tecno_9.png','tecno_10.png','tecno_11.png','tecno_12.png'
                ],
            ],
            'Fiestas Temáticas' => [
                'subtitle' => 'Experiencias inmersivas, Photo opportunity, Shows y animación, Dj y talento artístico, Celebridades.',
                'images' => [
                    'tematica_1.png','tematica_2.png','tematica_3.png','tematica_4.jpeg',
                    'tematica_5.jpeg','tematica_6.jpeg','tematica_7.jpeg','tematica_8.jpeg',
                ],
            ],
        ];
        $baseUploadUrl = 'http://localhost:8000/images/goldenparty/';
    @endphp

    @foreach ($galleries as $title => $gallery)
        <div class="mb-14">
            <h2 class="text-2xl font-bold mb-1">{{ $title }}</h2>
            @if ($gallery['subtitle'])
                <p class="opacity-70 mb-4">{{ $gallery['subtitle'] }}</p>
            @endif
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($gallery['images'] as $image)
                    <div class="rounded-box overflow-hidden bg-base-200 aspect-square">
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

@endsection