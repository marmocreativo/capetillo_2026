@extends('layouts.public')

@section('title', ($talent->meta_title ?: $talent->name) . ' | ' . config('app.name'))
@section('meta_description', $talent->meta_description ?: 'Contrata a ' . $talent->name . ' con Capetillo Producciones.')
@section('meta_keywords', $talent->meta_keywords ?: '')

@section('public-content')

<div class="max-w-7xl mx-auto p-2">

    <div class="fixed inset-0 -z-10 bg-cover bg-center"
         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/fondo_concierto.webp') }}');"></div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-24">
                <div class="relative overflow-hidden shadow-lg aspect-[4/5]">
                    @if ($talent->cover_image)
                        <img src="{{ Storage::url($talent->cover_image) }}" alt="{{ $talent->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl opacity-30">🎤</div>
                    @endif
                    <button type="button" class="open-contact-modal btn btn-primary btn-lg absolute bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)]">
                        Contrata a {{ $talent->name }}
                    </button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="breadcrumbs text-sm mb-6">
                <ul>
                    <li><a href="{{ route('categories.index') }}">Talento</a></li>
                    <li><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
                    <li>{{ $talent->name }}</li>
                </ul>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $talent->name }}</h1>

            @if ($talent->categories->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach ($talent->categories as $cat)
                        <a href="{{ route('categories.show', $cat) }}" class="badge badge-outline badge-primary badge-sm">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($talent->summary)
                <p class="text-lg opacity-80 mb-5">{{ $talent->summary }}</p>
            @endif

            @if (!empty($talent->highlights))
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-6">
                    @foreach ($talent->highlights as $highlight)
                        <li class="flex items-start gap-2 text-sm">
                            <span class="text-primary">★</span> {{ $highlight }}
                        </li>
                    @endforeach
                </ul>
            @endif

            

            <div class="card">
                <h2 class="card-title mb-2">Sobre {{ $talent->name }}</h2>
                @if ($talent->content)
                    <div class="leading-relaxed space-y-4 text-base-content/90 [&_p]:leading-relaxed">
                        {!! $talent->content !!}
                    </div>
                @else
                    <p class="opacity-60">Estamos preparando la información de este talento. Contáctanos para más detalles.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- FILA 2: Galería (solo si el talento tiene imágenes reales) --}}
    @if ($talent->images->isNotEmpty())
    <div class="mt-10">
        <h2 class="text-xl font-bold mb-4">Galería</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ($talent->images as $image)
                <div class="aspect-square rounded-box overflow-hidden bg-base-200">
                    <img src="{{ Storage::url($image->path) }}" alt="{{ $talent->name }}" class="w-full h-full object-cover">
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FILA 3: Spotify + grid de videos de YouTube (solo si hay datos reales) --}}
    @if ($talent->spotify_url || $talent->videos->isNotEmpty())
    <div class="mt-10 grid grid-cols-1 lg:grid-cols-4 gap-6">
        @if ($talent->spotify_url)
        @php
            preg_match('/open\.spotify\.com\/(playlist|show|album|artist|episode)\/([a-zA-Z0-9]+)/', $talent->spotify_url, $spotifyMatch);
            $spotifyType = $spotifyMatch[1] ?? null;
            $spotifyId = $spotifyMatch[2] ?? null;
        @endphp
        @if ($spotifyType && $spotifyId)
            <div class="lg:col-span-1">
                <h2 class="text-xl font-bold mb-4">Escúchalo en Spotify</h2>
                <iframe
                    style="border-radius:12px"
                    src="https://open.spotify.com/embed/{{ $spotifyType }}/{{ $spotifyId }}?utm_source=generator"
                    width="100%" height="380" frameBorder="0"
                    allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy">
                    </iframe>
                </div>
            @endif
        @endif

        @if ($talent->videos->isNotEmpty())
            <div class="{{ $talent->spotify_url ? 'lg:col-span-3' : 'lg:col-span-4' }}">
                <h2 class="text-xl font-bold mb-4">Videos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($talent->videos as $video)
                        <div class="aspect-video rounded-box overflow-hidden">
                            <iframe class="w-full h-full"
                                src="https://www.youtube.com/embed/{{ $video->youtube_id }}"
                                title="Video de {{ $talent->name }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif
</div>
{{-- FILA 4: CTA --}}
<div class="hero bg-base-200/50 backdrop-lg rounded-box p-10 my-12">
    <div class="hero-content w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">

            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">¡Estamos a un mensaje de distancia!</h2>
                <p class="opacity-80">
                    Completa el formulario o llámanos directamente. Nuestro equipo está listo para resolver
                    tus dudas y personalizar cada detalle según tus necesidades. ¡Hablemos y hagamos magia juntos!
                </p>
            </div>

            <div class="flex items-center justify-center">
                <button type="button" class="open-contact-modal btn btn-primary btn-lg">
                    Contrata ahora
                </button>
            </div>

        </div>
    </div>
</div>
{{-- FILA 5: Otros talentos de la misma categoría (carrusel aleatorio con autoplay) --}}
@if ($otherTalents->isNotEmpty())
<div class="my-14">
    <h2 class="text-2xl font-bold text-center mb-2">Otros talentos en {{ $category->name }}</h2>
    <p class="text-center opacity-70 mb-6">Descubre más opciones para tu evento</p>

    <div id="related-carousel" class="flex gap-4 overflow-x-auto p-4 bg-base-100/50 backdrop-lg" style="scrollbar-width: none;">
        @foreach ($otherTalents as $related)
            <a href="{{ route('talents.show', [$category, $related]) }}"
               class="relative shrink-0 w-44 aspect-[4/5] rounded-box overflow-hidden shadow hover:shadow-lg transition-shadow group">
                @if ($related->cover_image)
                    <img src="{{ Storage::url($related->cover_image) }}" alt="{{ $related->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-base-300 flex items-center justify-center text-4xl opacity-30">🎤</div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
                <div class="absolute inset-0 flex items-end justify-center pb-3 text-center px-2">
                    <h3 class="text-primary font-semibold text-sm">{{ $related->name }}</h3>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif



<dialog id="contact-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-lg p-0 overflow-hidden flex flex-col max-h-[90vh]">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10">✕</button>
        </form>

        <div class="flex items-center gap-3 sm:gap-4 p-4 sm:p-6 pb-3 sm:pb-4 shrink-0 border-b border-base-content/10">
            <div class="w-14 h-14 sm:w-20 sm:h-20 shrink-0 rounded-box overflow-hidden bg-black">
                @if ($talent->cover_image)
                    <img src="{{ Storage::url($talent->cover_image) }}" alt="{{ $talent->name }}" class="w-full h-full object-cover object-top">
                @else
                    <div class="w-full h-full flex items-center justify-center text-2xl sm:text-3xl opacity-30">🎤</div>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-base sm:text-lg mb-0.5 sm:mb-1 leading-snug">Contrata a {{ $talent->name }}</h3>
                <p class="text-xs sm:text-sm opacity-70 leading-snug">Llena tus datos y nos comunicaremos contigo lo antes posible.</p>
            </div>
        </div>

        <div class="overflow-y-auto px-4 sm:px-6 py-4 sm:py-5">
            <div id="contact-form-alert" class="hidden alert mb-4 text-sm"></div>

            <form id="contact-form" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="type" value="contratacion">
                    <input type="hidden" name="talent_id" value="{{ $talent->id }}">
                    <input type="hidden" name="talent_name" value="{{ $talent->name }}">

                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre</span></label>
                        <input type="text" name="name" class="input input-bordered w-full" required>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="email" class="input input-bordered w-full" required>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="tel" name="phone" class="input input-bordered w-full">
                    </div>

                    <div class="divider my-1"></div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Tipo de evento</span></label>
                        <select name="tipo_evento" class="select select-bordered w-full">
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="privado">Privado</option>
                            <option value="corporativo">Corporativo</option>
                            <option value="publico_masivo">Público / Masivo</option>
                            <option value="social">Social (boda / XV)</option>
                            <option value="gubernamental">Gubernamental</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Fecha del evento (tentativa)</span></label>
                        <input type="date" name="fecha_evento" class="input input-bordered w-full" min="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Estado de la rep.</span></label>
                            <select name="estado_republica" class="select select-bordered w-full" required>
                                <option value="" disabled selected>Selecciona tu estado</option>
                                @foreach (config('estados_mexico') as $estado)
                                    <option value="{{ $estado }}">{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Ciudad</span></label>
                            <input type="text" name="ciudad" class="input input-bordered w-full" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Aforo esperado</span></label>
                            <input type="number" min="1" name="aforo_esperado" class="input input-bordered w-full" placeholder="Opcional">
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Venue / Lugar</span></label>
                            <input type="text" name="venue" class="input input-bordered w-full" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="con_venta_boletos" value="1" class="checkbox checkbox-sm">
                            <span class="label-text">¿Venderás boletos?</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">¿Tienes presupuesto asignado?</span></label>
                        <div class="flex gap-4 text-sm">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="radio" name="tiene_presupuesto" value="1" class="radio radio-sm" id="presupuesto-si">
                                <span class="label-text">Sí</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="radio" name="tiene_presupuesto" value="0" class="radio radio-sm" id="presupuesto-no" checked>
                                <span class="label-text">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control" id="presupuesto-aproximado-wrapper" style="display:none;">
                        <label class="label"><span class="label-text">Presupuesto aproximado (MXN)</span></label>
                        <input type="text" inputmode="numeric" name="presupuesto_aproximado_display" id="presupuesto_aproximado_display" class="input input-bordered w-full" placeholder="Opcional">
                        <input type="hidden" name="presupuesto_aproximado" id="presupuesto_aproximado">
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Mensaje</span></label>
                        <textarea name="message" class="textarea textarea-bordered w-full" rows="3">Me interesa contratar a {{ $talent->name }}.</textarea>
                    </div>

                    <div class="modal-action flex-col sm:flex-row gap-2">
                        <button type="submit" class="btn btn-primary flex-1 p-4">Enviar mensaje</button>
                    </div>
                </form>
            </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
(function () {
    // Carrusel de "otros talentos" con autoplay
    const track = document.getElementById('related-carousel');
    if (track) {
        let paused = false;
        track.addEventListener('mouseenter', () => paused = true);
        track.addEventListener('mouseleave', () => paused = false);
        function step() {
            if (!paused) {
                track.scrollLeft += 0.6;
                if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 1) {
                    track.scrollLeft = 0;
                }
            }
            requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // Modal de contacto
    const modal = document.getElementById('contact-modal');
    document.querySelectorAll('.open-contact-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            contactForm.classList.remove('hidden');
            alertBox.classList.add('hidden');
            modal.showModal();
        });
    });

    const contactForm = document.getElementById('contact-form');
    const alertBox = document.getElementById('contact-form-alert');

    const presupuestoSi = document.getElementById('presupuesto-si');
    const presupuestoNo = document.getElementById('presupuesto-no');
    const presupuestoWrapper = document.getElementById('presupuesto-aproximado-wrapper');

    function togglePresupuestoWrapper() {
        presupuestoWrapper.style.display = presupuestoSi.checked ? 'block' : 'none';
    }
    presupuestoSi.addEventListener('change', togglePresupuestoWrapper);
    presupuestoNo.addEventListener('change', togglePresupuestoWrapper);

    // Formateo con comas de millares para presupuesto aproximado
    const presupuestoDisplay = document.getElementById('presupuesto_aproximado_display');
    const presupuestoHidden = document.getElementById('presupuesto_aproximado');

    presupuestoDisplay.addEventListener('input', () => {
        let raw = presupuestoDisplay.value.replace(/[^0-9.]/g, '');
        const parts = raw.split('.');
        if (parts.length > 2) raw = parts[0] + '.' + parts.slice(1).join('');

        presupuestoHidden.value = raw;

        const [intPart, decPart] = raw.split('.');
        const formattedInt = intPart ? Number(intPart).toLocaleString('en-US') : '';
        presupuestoDisplay.value = decPart !== undefined ? `${formattedInt}.${decPart}` : formattedInt;
    });

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(contactForm);
        formData.delete('presupuesto_aproximado_display');
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';

        try {
            const response = await fetch('{{ route('contact.send') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': contactForm.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error al enviar.');

            alertBox.textContent = '¡Mensaje enviado! Nos comunicaremos contigo pronto.';
            alertBox.classList.remove('hidden', 'alert-error');
            alertBox.classList.add('alert-success');
            contactForm.reset();
            contactForm.classList.add('hidden');
        } catch (err) {
            alertBox.textContent = 'Hubo un error al enviar tu mensaje. Intenta por WhatsApp.';
            alertBox.classList.remove('hidden', 'alert-success');
            alertBox.classList.add('alert-error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Enviar mensaje';
        }
    });
})();
</script>

@endsection