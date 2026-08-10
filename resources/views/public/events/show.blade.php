@extends('layouts.public')

@section('title', ($event->meta_title ?: 'Organización de ' . $event->title . ' en la CDMX') . ' | ' . config('app.name'))
@section('meta_description', $event->meta_description ?: $event->summary)

@section('public-content')

{{-- =========================================================
     HERO: imagen de portada + columna info / columna formulario
========================================================== --}}
<div class="relative isolate overflow-hidden"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);
            @if($event->cover_image) background-image: url('{{ Storage::url($event->cover_image) }}'); @endif
            background-size: cover; background-position: center;">

    <div class="absolute inset-0 bg-black/75"></div>

    <div class="relative max-w-7xl mx-auto px-4 md:px-6 pt-36 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

            {{-- Columna izquierda: logo + título + numeralia --}}
            <div class="text-white">
                <img src="{{ asset('images/Golden Party Dorado.png') }}" alt="Golden Party" class="h-48 w-auto mb-3">

                <h1 class="text-3xl md:text-5xl font-bold leading-tight uppercase">
                    Organización de {{ $event->title }} en la CDMX
                </h1>

                @if ($event->summary)
                    <p class="mt-4 text-lg text-white/90 max-w-xl">{{ $event->summary }}</p>
                @endif

                <div class="stats stats-vertical sm:stats-horizontal shadow mt-10 bg-base-100/95 w-full">
                    <div class="stat place-items-center">
                        <div class="stat-title">Trayectoria</div>
                        <div class="stat-value text-primary text-2xl">27+</div>
                        <div class="stat-desc">años de experiencia</div>
                    </div>
                    <div class="stat place-items-center">
                        <div class="stat-title">Eventos</div>
                        <div class="stat-value text-primary text-2xl">+12 MIL</div>
                        <div class="stat-desc">producidos y coordinados</div>
                    </div>
                    <div class="stat place-items-center">
                        <div class="stat-title">Confianza</div>
                        <div class="stat-value text-primary text-2xl">+750</div>
                        <div class="stat-desc">empresas y familias</div>
                    </div>
                </div>

                <p class="mt-6 text-sm text-white/70 max-w-xl">
                    Más de 750 empresas han confiado en nosotros y más de 10 millones de espectadores han disfrutado
                    eventos de altísima calidad organizados por Capetillo Producciones.
                </p>
            </div>

            {{-- Columna derecha: formulario de contacto (fondo blanco) --}}
            <div id="contact-form" class="card bg-base-100 text-base-content shadow-xl scroll-mt-28">
                <div class="card-body p-6 md:p-8">
                    <h2 class="text-xl font-bold mb-1">Cotiza {{ $event->title }}</h2>
                    <p class="opacity-70 text-sm mb-6">Cuéntanos sobre tu evento y te contactamos a la brevedad.</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-4 text-sm">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('event-contacts.store') }}" method="POST" class="flex flex-col gap-3">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">

                        <div class="form-control">
                            <label class="label"><span class="label-text">Nombre</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Correo</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Teléfono</span></label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" class="input input-bordered w-full">
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Fecha aproximada del evento</span></label>
                            <input type="date" name="fecha_aproximada" value="{{ old('fecha_aproximada') }}" class="input input-bordered w-full">
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Mensaje</span></label>
                            <textarea name="message" rows="4" class="textarea textarea-bordered w-full" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">Enviar</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================
     CONTENIDO
========================================================== --}}
@if ($event->content)
    <div class="max-w-4xl mx-auto px-4">
        <div class="event-content my-16">
            {!! $event->content !!}
        </div>
    </div>
@endif

{{-- =========================================================
     SERVICIOS ESPECIALIZADOS
========================================================== --}}
@if (!empty($event->servicios_especializados))
    <div class="max-w-7xl mx-auto px-4 mb-20">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-2">Servicios especializados</h2>
        <p class="text-center opacity-70 mb-10">Lo que nos distingue en la organización de {{ $event->title }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($event->servicios_especializados as $servicio)
                <div class="card bg-base-100 shadow border-t-4 border-primary">
                    <div class="card-body p-6">
                        <h3 class="text-xl font-bold leading-snug">{{ $servicio['titulo_servicio'] ?? '' }}</h3>
                        <p class="text-sm opacity-80 mt-2">{{ $servicio['descripcion'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- =========================================================
     GALERÍA DE IMÁGENES (carrusel + lightbox)
========================================================== --}}
@if ($event->images->count())
    <div class="max-w-7xl mx-auto px-4 mb-16">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Galería</h2>

        <div id="gallery-carousel" class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory" style="scrollbar-width: thin;">
            @foreach ($event->images as $index => $image)
                <button type="button"
                        class="gallery-item shrink-0 w-64 sm:w-80 aspect-video rounded-box overflow-hidden shadow snap-start"
                        data-full="{{ Storage::url($image->path) }}"
                        data-index="{{ $index }}">
                    <img src="{{ Storage::url($image->path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </button>
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" class="fixed inset-0 z-[100] bg-black/90 hidden items-center justify-center p-4">
        <button type="button" id="lightbox-close" class="btn btn-circle btn-sm absolute top-4 right-4" aria-label="Cerrar">✕</button>
        <button type="button" id="lightbox-prev" class="btn btn-circle absolute left-4 top-1/2 -translate-y-1/2" aria-label="Anterior">❮</button>
        <img id="lightbox-image" src="" alt="" class="max-h-[85vh] max-w-full object-contain rounded">
        <button type="button" id="lightbox-next" class="btn btn-circle absolute right-4 top-1/2 -translate-y-1/2" aria-label="Siguiente">❯</button>
    </div>
@endif

{{-- =========================================================
     VIDEOS
========================================================== --}}
@if ($event->videos->count())
    <div class="max-w-7xl mx-auto px-4 mb-20">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Videos</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($event->videos as $video)
                <div class="aspect-video">
                    <iframe class="w-full h-full rounded-box shadow" src="https://www.youtube.com/embed/{{ $video->youtube_id }}" title="{{ $event->title }}" allowfullscreen></iframe>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- =========================================================
     PREGUNTAS FRECUENTES
========================================================== --}}
@if (!empty($event->preguntas_frecuentes))
    <div class="max-w-4xl mx-auto px-4 mb-20">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Preguntas frecuentes</h2>
        <div class="flex flex-col gap-2">
            @foreach ($event->preguntas_frecuentes as $index => $faq)
                <div class="collapse collapse-arrow bg-base-200/60">
                    <input type="radio" name="faq-accordion">
                    <div class="collapse-title font-semibold">
                        {{ $faq['pregunta'] ?? '' }}
                    </div>
                    <div class="collapse-content text-sm opacity-80">
                        <p>{{ $faq['respuesta'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- =========================================================
     TALENTOS DISPONIBLES (aleatorio, estilo home)
========================================================== --}}
@if ($randomTalents->isNotEmpty())
    <div class="my-16">
        <div class="fixed inset-0 -z-10 bg-cover bg-center"
             style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>

        <h2 class="text-2xl md:text-3xl font-bold text-center mb-2">Talento disponible para tu evento</h2>
        <p class="text-center opacity-70 mb-8 max-w-2xl mx-auto px-4">
            Nuestros talentos artísticos están disponibles para {{ mb_strtolower($event->title) }} — música en vivo,
            shows y presentaciones a la medida de tu celebración.
        </p>

        <div id="event-talent-carousel" class="flex gap-4 overflow-x-auto p-4 bg-base-100" style="scrollbar-width: none;">
            @foreach ($randomTalents->concat($randomTalents) as $talent)
                @php $category = $talent->categories->first(); @endphp
                <a href="{{ $category ? route('talents.show', [$category, $talent]) : '#' }}"
                   class="relative shrink-0 w-48 h-64 overflow-hidden shadow hover:shadow-lg transition-shadow group">
                    @if ($talent->cover_image)
                        <img src="{{ Storage::url($talent->cover_image) }}" alt="{{ $talent->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-base-300 flex items-center justify-center text-4xl opacity-30">🎤</div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/10 to-transparent"></div>

                    <div class="absolute bottom-0 left-0 right-0 p-3 text-center">
                        <h3 class="text-primary font-semibold text-sm leading-tight line-clamp-2">{{ $talent->name }}</h3>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('categories.index') }}" class="btn btn-outline btn-primary">
                Ver todo el catálogo
            </a>
        </div>
    </div>
@endif

{{-- =========================================================
     CALL TO ACTION FINAL (scroll al formulario)
========================================================== --}}
<div class="hero bg-base-200/50 backdrop-lg p-10 my-6">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">
            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¡Hagamos de tu evento algo inolvidable!</h2>
                <p class="opacity-80">Cuéntanos los detalles y te ayudamos con la organización completa, de principio a fin.</p>
            </div>
            <div class="flex items-center justify-center">
                <a href="#contact-form" class="btn btn-primary btn-lg cta-scroll-top">Contáctanos</a>
            </div>
        </div>
    </div>
</div>

<style>
    html {
        scroll-behavior: smooth;
    }

    .event-content {
        line-height: 1.75;
    }

    .event-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: #DCA54A;
        line-height: 1.3;
    }

    .event-content h2:first-child {
        margin-top: 0;
    }

    .event-content h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
    }

    .event-content p {
        margin-bottom: 1.25rem;
        opacity: 0.85;
    }

    .event-content ul,
    .event-content ol {
        margin: 1.25rem 0;
        padding-left: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .event-content ul {
        list-style: none;
        padding-left: 0;
    }

    .event-content ul li {
        position: relative;
        padding-left: 1.75rem;
        opacity: 0.85;
    }

    .event-content ul li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.5em;
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background-color: #DCA54A;
    }

    .event-content ol {
        list-style: decimal;
        padding-left: 1.75rem;
    }

    .event-content ol li {
        opacity: 0.85;
    }

    .event-content a {
        color: #DCA54A;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .event-content strong {
        font-weight: 700;
        opacity: 1;
    }

    .event-content blockquote {
        border-left: 3px solid #DCA54A;
        padding-left: 1.25rem;
        margin: 1.5rem 0;
        font-style: italic;
        opacity: 0.8;
    }
</style>

<script>
(function () {
    // Carrusel de talentos: auto-scroll continuo (igual que en home)
    const track = document.getElementById('event-talent-carousel');
    if (track) {
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
    }

    // Lightbox de galería
    const items = Array.from(document.querySelectorAll('.gallery-item'));
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const closeBtn = document.getElementById('lightbox-close');
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');

    if (items.length && lightbox) {
        let current = 0;

        function open(index) {
            current = index;
            lightboxImage.src = items[current].dataset.full;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function show(index) {
            current = (index + items.length) % items.length;
            lightboxImage.src = items[current].dataset.full;
        }

        items.forEach((item, index) => {
            item.addEventListener('click', () => open(index));
        });

        closeBtn?.addEventListener('click', close);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) close();
        });
        prevBtn?.addEventListener('click', () => show(current - 1));
        nextBtn?.addEventListener('click', () => show(current + 1));

        document.addEventListener('keydown', (e) => {
            if (lightbox.classList.contains('hidden')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') show(current - 1);
            if (e.key === 'ArrowRight') show(current + 1);
        });
    }
})();
</script>

@endsection