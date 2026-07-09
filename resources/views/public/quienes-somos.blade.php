@extends('layouts.public')

@section('title', 'Quiénes Somos | ' . config('app.name'))

@section('public-content')

<div class="hero min-h-[45vh] overflow-hidden relative mb-12"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background-image: url('https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1600&q=80');
            background-size: cover; background-position: center;">
    <div class="hero-overlay bg-black/70"></div>

    <div class="hero-content w-full max-w-7xl mx-auto flex-col text-center px-6 pt-32">
        <span class="badge badge-primary font-semibold mb-3">+27 años de trayectoria</span>
        <img src="{{ asset('images/logo_capetillo_blanco.svg') }}" class="max-w-sm w-full object-cover my-8" alt="Capetillo Producciones" />
        <h1 class="text-3xl md:text-5xl font-bold uppercase leading-tight">Quiénes Somos</h1>
        <p class="py-4 opacity-80">
            Empresa fundada hace más de 27 años, considerada líder en entretenimiento en
            México, USA y LATAM. Con dos pilares fundamentales en sus inicios:
            <span class="text-primary font-semibold">Representación Artística</span> y
            <span class="text-primary font-semibold">Organización de Eventos</span>.
        </p>
    </div>
</div>
<div class="max-w-7xl mx-auto p-2">
<div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>

<div class="stats stats-vertical md:stats-horizontal shadow w-full my-6 bg-base-100">
    <div class="stat place-items-center">
        <div class="stat-title">Eventos creados</div>
        <div class="stat-value text-primary">+12 MIL</div>
        <div class="stat-desc">Producidos y coordinados por Capetillo</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Empresas</div>
        <div class="stat-value text-primary">+750</div>
        <div class="stat-desc">Que han confiado en nosotros</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Espectadores</div>
        <div class="stat-value text-primary">+10 M</div>
        <div class="stat-desc">Disfrutando eventos de alta calidad</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Experiencia</div>
        <div class="stat-value text-primary">27+</div>
        <div class="stat-desc">Años conectando talento y eventos</div>
    </div>
</div>

{{-- EVENTOS DESTACADOS --}}
<div class="my-16">
    <h2 class="text-2xl md:text-3xl font-bold  mb-2 uppercase">Eventos Destacados</h2>
    <p class=" opacity-70 mb-10">Una muestra de nuestra trayectoria en el escenario nacional e internacional</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="card bg-base-100 shadow relative overflow-hidden">
            <hero-icon-outline name="location-marker" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary text-lg ">Ferias</h3>
                <ul class="text-sm opacity-80 list-disc list-inside space-y-1">
                    <li>Tultitlán</li>
                    <li>Cuernavaca</li>
                    <li>Salamanca</li>
                    <li>Atempan</li>
                    <li>Acapulco</li>
                    <li>Metepec</li>
                    <li>Teziutlán</li>
                </ul>
            </div>
        </div>
        <div class="card bg-base-100 shadow relative overflow-hidden">
            <hero-icon-outline name="microphone" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary text-lg ">Giras</h3>
                <ul class="text-sm opacity-80 list-disc list-inside space-y-1">
                    <li><b>Two´r amigos Emmanuel y Mijares</b><br>— 5 ciudades</li>
                    <li><b>Yuri</b><br> — 12 ciudades</li>
                    <li><b>Ángeles Azules</b><br> — 9 ciudades</li>
                    <li><b>Platanito Show</b><br> — 150 ciudades (México, USA y Centroamérica)</li>
                    <li><b>Puro Loco (TV Azteca)</b><br> — 150 ciudades</li>
                </ul>
            </div>
        </div>
        <div class="card bg-base-100 shadow relative overflow-hidden">
            <hero-icon-outline name="sparkles" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary text-lg ">Eventos Privados</h3>
                <ul class="text-sm opacity-80 list-disc list-inside space-y-1">
                    <li><b>Mijares</b><br> — Hotel Mandarín Oriental, Miami (Cumpleaños)</li>
                    <li><b>Emmanuel en Cancún</b><br> (Convención)</li>
                    <li><b>Omar Chaparro en Guadalajara </b><br>(Conferencia)</li>
                </ul>
            </div>
        </div>
        <div class="card bg-base-100 shadow relative overflow-hidden">
            <hero-icon-outline name="ticket" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary text-lg ">Venta de Boletos</h3>
                <ul class="text-sm opacity-80 list-disc list-inside space-y-1">
                    <li><b>Pitbull</b><br> — Plaza de Toros Querétaro</li>
                    <li><b>Ely Guerra</b><br> — Teatro Diana, Guadalajara</li>
                    <li><b>Ricardo Arjona</b><br> — Nave de las Estrellas, Texcoco</li>
                    <li><b>Adal Ramones</b><br> — República Dominicana</li>
                </ul>
            </div>
        </div>
        <div class="card bg-base-100 shadow lg:col-span-2 relative overflow-hidden">
            <hero-icon-outline name="video-camera" class="absolute -right-4 -bottom-4 h-32 w-32 text-primary/10 rotate-12 pointer-events-none"></hero-icon-outline>
            <div class="card-body relative">
                <h3 class="card-title text-primary text-lg ">Programas y Contenido Audiovisual</h3>
                <ul class="text-sm opacity-80 list-disc list-inside space-y-1">
                    <li><b>Cañaveral</b><br> — Canción original para "El Canal Olímpico" de Azteca Deportes</li>
                    <li><b>El Costeño</b><br> — Cobertura para "El Canal del Mundial" de Azteca Deportes</li>
                    <li><b>Chuponcito</b><br> — Streaming en México, USA y Centroamérica</li>
                    <li><b>Noches con Platanito</b><br> — Programa en USA, canal Estrella TV</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- DIFERENCIADORES --}}
<div class="my-16 bg-base-200/50 py-14 px-6">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-10 uppercase">Nuestros Diferenciadores</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
        <div class="text-center">
            <hero-icon-outline name="fire" class="h-10 w-10 mx-auto mb-3 text-primary"></hero-icon-outline>
            <p class="font-semibold">Más de 27 años de experiencia comprobada</p>
        </div>
        <div class="text-center">
            <hero-icon-outline name="users" class="h-10 w-10 mx-auto mb-3 text-primary"></hero-icon-outline>
            <p class="font-semibold">Booking directo con talento artístico</p>
        </div>
        <div class="text-center">
            <hero-icon-outline name="film" class="h-10 w-10 mx-auto mb-3 text-primary"></hero-icon-outline>
            <p class="font-semibold">Producción integral <br>in-house</p>
        </div>
        <div class="text-center">
            <hero-icon-outline name="light-bulb" class="h-10 w-10 mx-auto mb-3 text-primary"></hero-icon-outline>
            <p class="font-semibold">Adaptabilidad, creatividad y tecnología</p>
        </div>
    </div>
</div>

{{-- VERTICALES --}}
<div class="my-16">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-2 uppercase">Nuestro Alcance</h2>
    <p class="text-center opacity-70 mb-10 max-w-2xl mx-auto">
        Con nuestra experiencia, relación directa con talento artístico y socios comerciales,
        nos hemos expandido a nuevas verticales especializadas con el sello y profesionalismo
        que nos caracteriza.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <img src="{{ asset('images/logo_grande_blanco.png') }}"
                     alt="Live Media" class="max-w-full max-h-[10vh] object-contain mb-4">
                <h3 class="card-title text-primary uppercase">Capetillo Producciones</h3>
                
                <p class="text-xs uppercase font-semibold opacity-60 mb-2">Management, booking y representación artística</p>
                <p class="text-sm font-semibold mt-2">Servicios:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Management y booking de talento</li>
                    <li>Representación artística</li>
                    <li>Diseño de espectáculos</li>
                </ul>
                <p class="text-sm font-semibold mt-2">Ejemplos:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Giras nacionales e internacionales</li>
                    <li>Eventos corporativos</li>
                    <li>Eventos privados y con venta de boletos</li>
                    <li>Ferias y shows masivos</li>
                    <li>Eventos con gobierno</li>
                </ul>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <img src="{{ asset('images/live media blanco.png') }}"
                     alt="Live Media" class="max-w-full max-h-[10vh] object-contain mb-4">
                <h3 class="card-title text-primary uppercase">Capetillo Live Media</h3>
                <p class="text-xs uppercase font-semibold opacity-60 mb-2">Producción audiovisual y técnica para espectáculos</p>
                <p class="text-sm font-semibold mt-2">Servicios:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Producción de video, audio y fotografía</li>
                    <li>Pantallas y escenografías</li>
                    <li>Producción y dirección de proyectos</li>
                    <li>Venta y renta de equipo de audio e iluminación</li>
                </ul>
                <a href="{{ route('live-media') }}" class="btn btn-sm btn-outline btn-primary mt-4 self-start">Conocer más</a>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <img src="{{ asset('images/capetillo_network.png') }}"
                     alt="Live Media" class="max-w-full max-h-[10vh] object-contain mb-4">
                <h3 class="card-title text-primary uppercase">Capetillo Network</h3>
                <p class="text-xs uppercase font-semibold opacity-60 mb-2">Marketing de influencers y visibilidad de marca</p>
                <p class="text-sm font-semibold mt-2">Servicios:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Campañas con influencers y talento artístico</li>
                    <li>Activaciones de marca</li>
                    <li>Estrategia y contenido digital: streams, videos, reels</li>
                </ul>
                <p class="text-sm font-semibold mt-2">Ejemplos:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Selección de spokespersons e influencers</li>
                    <li>Contenido orgánico / patrocinado</li>
                    <li>Participación en eventos o activaciones de marca</li>
                </ul>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <img src="{{ asset('images/Golden Party Dorado.png') }}"
                     alt="Live Media" class="max-w-full max-h-[20vh] object-contain mb-4">
                <h3 class="card-title text-primary uppercase">Capetillo Golden Party</h3>
                <p class="text-xs uppercase font-semibold opacity-60 mb-2">Fiestas, reuniones, eventos y convenciones icónicas</p>
                <p class="text-sm font-semibold mt-2">Servicios:</p>
                <ul class="text-sm opacity-80 list-disc list-inside">
                    <li>Conceptualización de eventos y experiencias</li>
                    <li>Tecnología de punta y producción creativa</li>
                    <li>Producción integral: artista, audio, iluminación, A&B, decoración y coordinación</li>
                </ul>
                <a href="{{ route('golden-party') }}" class="btn btn-sm btn-outline btn-primary mt-4 self-start">Conocer más</a>
            </div>
        </div>

    </div>
</div>
</div>
{{-- CLIENTES --}}
<div class="my-16 text-center">
    <h2 class="text-2xl md:text-3xl font-bold mb-10 uppercase text-primary">Clientes que confían en nosotros</h2>

    @php
        $clientLogos = [
            'cliente_1.jpg', 'cliente_2.jpg', 'cliente_3.jpg', 'cliente_4.jpg', 'cliente_5.jpg',
            'cliente_6.jpg', 'cliente_7.jpg', 'cliente_8.jpg', 'cliente_9.jpg',
            'cliente_10.jpg', 'cliente_11.jpg', 'cliente_12.jpg', 'cliente_13.jpg', 'cliente_14.jpg',
            'cliente_15.jpg', 'cliente_16.jpg', 'cliente_17.jpg', 'cliente_18.jpg', 'cliente_19.jpg',
        ];
    @endphp

    <div class="clients-marquee bg-base-100 shadow py-8 relative overflow-hidden">
        <div class="clients-marquee-track">
            @foreach ($clientLogos as $logo)
                <img src="{{ asset('images/clientes/' . $logo) }}" alt="Logo cliente" class="clients-marquee-logo">
            @endforeach
            @foreach ($clientLogos as $logo)
                <img src="{{ asset('images/clientes/' . $logo) }}" alt="Logo cliente" class="clients-marquee-logo" aria-hidden="true">
            @endforeach
        </div>
    </div>
</div>

<style>
.clients-marquee {
    -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
}

.clients-marquee-track {
    display: flex;
    align-items: center;
    width: max-content;
    gap: 3.5rem;
    animation: clients-marquee-scroll 35s linear infinite;
}

.clients-marquee:hover .clients-marquee-track {
    animation-play-state: paused;
}

.clients-marquee-logo {
    height: 5rem;
    width: auto;
    object-fit: contain;
    opacity: 0.9;
    flex-shrink: 0;
}

@keyframes clients-marquee-scroll {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}
</style>

{{-- CTA / HASHTAG --}}
<div class="hero bg-base-200/50 p-2 md:p-10 my-16 text-center">
    <div class="hero-content flex-col max-w-2xl">
        <p class="text-primary font-bold text-sm md:text-xl uppercase">#SoloEnCapetilloProducciones</p>
        <p class="opacity-80 my-4">
            Agradecemos a nuestros clientes, talento artístico y amigos por darnos la oportunidad
            de hacer más que eventos: experiencias inolvidables.
        </p>
        <a href="{{ route('contact.page') }}" class="btn btn-primary btn-lg">Contáctanos</a>
    </div>
</div>

@endsection