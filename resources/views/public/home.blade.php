@extends('layouts.public')

@section('title', config('app.name') . ' | Agencia de Contratación de Talento Artístico')

@section('public-content')

<div id="hero-slider" class="relative isolate min-h-screen overflow-hidden"
     style="margin-top: -100px; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">

    <div class="hero-kenburns absolute inset-0 -z-10"
         style="background-image: url('{{ asset('images/fondo_hero.jpg') }}'); background-size: cover; background-position: center;"></div>
    @forelse ($homeSlides as $index => $slide)
        <div class="hero-slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
            <div class="absolute inset-x-0 bottom-0 h-[90%] flex items-end justify-center pointer-events-none">
                <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}"
                     class="h-full w-auto max-w-full object-contain">
            </div>

            <div class="relative h-full flex items-end justify-center text-center pb-10 px-6">
                <div class="max-w-xl p-10 bg-black/50">
                    @if ($slide->caption)
                        <span class="hero-anim badge badge-primary mb-3 font-semibold" style="transition-delay: 100ms;">{{ $slide->caption }}</span>
                    @endif
                    <h1 class="hero-anim text-2xl md:text-4xl font-bold text-white leading-tight uppercase" style="transition-delay: 220ms;">
                        {{ $slide->title }}
                    </h1>
                    @if ($slide->summary)
                        <p class="hero-anim py-4 text-sm md:text-base text-white/90" style="transition-delay: 340ms;">{{ $slide->summary }}</p>
                    @endif
                    @if ($slide->button_text && $slide->button_url)
                        <a href="{{ $slide->button_url }}" class="hero-anim btn btn-outline btn-primary rounded-none bg-transparent" style="transition-delay: 460ms;">
                            {{ $slide->button_text }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="hero-slide absolute inset-0 opacity-100 z-10">
            <div class="relative h-full flex items-center justify-center text-center px-6">
                <div class="max-w-xl">
                    <span class="badge badge-primary mb-3 font-semibold">+15 años de trayectoria</span>
                    <h1 class="text-2xl md:text-4xl font-bold text-white leading-tight uppercase">
                        El talento que tu evento merece
                    </h1>
                    <p class="py-4 text-sm md:text-base text-white/90">
                        En {{ config('app.name') }} conectamos a las marcas y familias más exigentes con el talento
                        artístico más solicitado de México: comediantes, cantantes, bandas, actores y conferencistas
                        listos para hacer inolvidable tu próximo evento.
                    </p>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline btn-primary rounded-none bg-transparent">
                        Contrata a tu artista favorito
                    </a>
                </div>
            </div>
        </div>
    @endforelse

    @if ($homeSlides->count() > 1)
        <button type="button" id="hero-prev" class="btn btn-circle btn-sm absolute left-4 top-1/2 -translate-y-1/2 z-20" aria-label="Anterior">❮</button>
        <button type="button" id="hero-next" class="btn btn-circle btn-sm absolute right-4 top-1/2 -translate-y-1/2 z-20" aria-label="Siguiente">❯</button>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            @foreach ($homeSlides as $index => $slide)
                <button type="button" class="hero-bullet w-2.5 h-2.5 rounded-full transition-colors {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                        data-index="{{ $index }}" aria-label="Ir al slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</div>

<style>
.hero-anim {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.hero-slide.opacity-100 .hero-anim {
    opacity: 1;
    transform: translateY(0);
}
.hero-kenburns {
    animation: kenburns 12s ease-in-out infinite alternate;
    will-change: transform, opacity;
}

@keyframes kenburns {
    0%   { transform: scale(1) translate(0, 0); opacity: 0; }
    50%  { opacity: 1; }
    100% { transform: scale(1.20) translate(-2%, -2%); opacity: 0.7; }
}
</style>

@if ($homeSlides->count() > 1)
<script>
(function () {
    const root = document.getElementById('hero-slider');
    if (!root) return;

    const slides = Array.from(root.querySelectorAll('.hero-slide'));
    const bullets = Array.from(root.querySelectorAll('.hero-bullet'));
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    let current = 0;
    let paused = false;
    const intervalMs = 6000;

    function goTo(index) {
        slides[current].classList.remove('opacity-100', 'z-10');
        slides[current].classList.add('opacity-0', 'z-0');
        bullets[current]?.classList.remove('bg-white');
        bullets[current]?.classList.add('bg-white/50');

        current = (index + slides.length) % slides.length;

        slides[current].classList.remove('opacity-0', 'z-0');
        slides[current].classList.add('opacity-100', 'z-10');
        bullets[current]?.classList.remove('bg-white/50');
        bullets[current]?.classList.add('bg-white');
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    prevBtn?.addEventListener('click', prev);
    nextBtn?.addEventListener('click', next);

    bullets.forEach(bullet => {
        bullet.addEventListener('click', () => goTo(parseInt(bullet.dataset.index, 10)));
    });

    root.addEventListener('mouseenter', () => paused = true);
    root.addEventListener('mouseleave', () => paused = false);

    setInterval(() => {
        if (!paused) next();
    }, intervalMs);
})();
</script>
@endif

<div class="stats stats-vertical md:stats-horizontal shadow w-full my-10 bg-base-100">
    <div class="stat place-items-center">
        <div class="stat-title">Talentos disponibles</div>
        <div class="stat-value text-primary">+300</div>
        <div class="stat-desc">En 13 categorías distintas</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Eventos realizados</div>
        <div class="stat-value text-primary">+1,200</div>
        <div class="stat-desc">En todo México</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Años de experiencia</div>
        <div class="stat-value text-primary">15+</div>
        <div class="stat-desc">Conectando talento y eventos</div>
    </div>
    <div class="stat place-items-center">
        <div class="stat-title">Satisfacción</div>
        <div class="stat-value text-primary">98%</div>
        <div class="stat-desc">Clientes que repiten</div>
    </div>
</div>

<div class="my-14">
    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.png') }}');"></div>
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-2">Algunos de nuestros talentos</h2>
    <p class="text-center opacity-70 mb-8">Solo una muestra de lo que puedes contratar hoy mismo</p>

    <div id="talent-carousel" class="flex gap-4 overflow-x-auto p-4 bg-base-100" style="scrollbar-width: none;">
        @foreach ($featuredTalents->concat($featuredTalents) as $talent)
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

<script>
(function () {
    const track = document.getElementById('talent-carousel');
    if (!track) return;

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
})();
</script>

<div class="hero bg-base-200/50 backdrop-lg p-10 my-10">
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