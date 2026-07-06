<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $roster->name }} · Capetillo Producciones</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { height: 100%; background: #1A1A1A; overflow: hidden; }
        .roster-bg-cover { background: linear-gradient(180deg, rgba(0,0,0,.15), rgba(0,0,0,.75)), url('{{ asset('images/fondo_presentaciones.jpg') }}') center/cover no-repeat; }
        .roster-bg-content { background: url('{{ asset('images/fondo_contenido.jpg') }}') center/cover no-repeat; }
        .roster-slide { position: absolute; inset: 0; }
        .roster-safe-bottom { padding-bottom: 96px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-white font-sans h-screen w-screen">
    <div
        x-data="rosterViewer(@js($payload))"
        x-init="init()"
        @keydown.window.arrow-right="next()"
        @keydown.window.arrow-left="prev()"
        @touchstart="touchStart($event)"
        @touchend="touchEnd($event)"
        class="relative h-screen w-screen select-none"
    >
        {{-- Slides --}}
        <template x-for="(slide, idx) in slides" :key="idx">
            <div x-show="idx === current" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="roster-slide">

                {{-- PORTADA --}}
                <template x-if="slide.type === 'cover'">
                    <div class="roster-bg-cover roster-safe-bottom h-full w-full flex flex-col items-center justify-center gap-6 px-10 text-center">
                        <div class="flex items-center gap-8 flex-wrap justify-center" x-show="logos.length">
                            <template x-for="logo in logos" :key="logo">
                                <img :src="logo" class="h-16 object-contain">
                            </template>
                        </div>
                        <img src="{{ asset('images/separador.png') }}" class="h-2 opacity-80" alt="">
                        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-wide" x-text="rosterName"></h1>
                        <p class="max-w-2xl text-sm md:text-base text-white/80" x-text="introText"></p>
                    </div>
                </template>

                {{-- SEPARADOR DE CATEGORÍA --}}
                <template x-if="slide.type === 'section'">
                    <div class="roster-bg-cover roster-safe-bottom h-full w-full flex flex-col items-center justify-center gap-6 px-10 text-center">
                        <img src="{{ asset('images/separador.png') }}" class="h-2 opacity-80" alt="">
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-wide" x-text="slide.title"></h2>
                    </div>
                </template>

                {{-- 1 POR VISTA --}}
                <template x-if="slide.type === 'talents' && mode === 1">
                    <div class="h-full w-full relative">
                        <div class="absolute inset-0 bg-black bg-cover bg-center bg-no-repeat md:bg-contain" :style="`background-image:url(${slide.items[0].imagen})`"></div>
                        <div class="absolute bottom-0 inset-x-0 bg-black/80 backdrop-blur-sm py-6 px-8 pb-16 text-center">
                            <h3 class="text-2xl font-bold" x-text="slide.items[0].nombre"></h3>
                            <p class="text-sm text-white/80 mt-2 max-w-xl mx-auto" x-text="slide.items[0].resumen"></p>
                            <p class="text-[#DCA54A] font-bold mt-2" x-show="mostrarHonorarios" x-text="slide.items[0].honorarios_formatted"></p>
                        </div>
                    </div>
                </template>

                {{-- 2 POR VISTA --}}
                <template x-if="slide.type === 'talents' && mode === 2">
                    <div class="roster-bg-content roster-safe-bottom h-full w-full grid grid-cols-1 md:grid-cols-2 gap-4 p-8 overflow-y-auto">
                        <template x-for="item in slide.items" :key="item.nombre">
                            <div class="flex flex-row md:flex-col bg-black/40 rounded-lg overflow-hidden">
                                <img :src="item.imagen" class="w-1/3 md:w-full h-auto md:h-2/3 object-cover object-top" x-show="item.imagen">
                                <div class="p-4 flex-1 flex flex-col">
                                    <h3 class="text-[#DCA54A] font-bold text-lg" x-text="item.nombre"></h3>
                                    <p class="text-xs text-white/80 mt-1 flex-1" x-text="item.resumen"></p>
                                    <p class="text-[#DCA54A] text-sm font-bold mt-2" x-show="mostrarHonorarios" x-text="item.honorarios_formatted"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- 4 POR VISTA --}}
                <template x-if="slide.type === 'talents' && mode === 4">
                    <div class="roster-bg-content roster-safe-bottom h-full w-full grid grid-cols-1 md:grid-cols-2 md:grid-rows-2 gap-3 p-6 overflow-y-auto">
                        <template x-for="item in slide.items" :key="item.nombre">
                            <div class="flex flex-row bg-black/40 rounded-lg overflow-hidden">
                                <img :src="item.imagen" class="w-1/3 object-cover object-top" x-show="item.imagen">
                                <div class="p-3 flex-1 flex flex-col">
                                    <h3 class="text-[#DCA54A] font-bold text-sm" x-text="item.nombre"></h3>
                                    <p class="text-[11px] text-white/80 mt-1 flex-1 line-clamp-4" x-text="item.resumen"></p>
                                    <p class="text-[#DCA54A] text-xs font-bold mt-1" x-show="mostrarHonorarios" x-text="item.honorarios_formatted"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- LISTA --}}
                <template x-if="slide.type === 'list'">
                    <div class="roster-bg-content roster-safe-bottom h-full w-full overflow-y-auto p-8">
                        <table class="table w-full bg-black/50 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="text-[#DCA54A]">
                                    <th>Nombre</th>
                                    <th x-show="mostrarHonorarios">Honorarios</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in slide.items" :key="item.nombre">
                                    <tr class="border-b border-white/10">
                                        <td>
                                            <button
                                                type="button"
                                                @click="openImageModal(item)"
                                                class="text-white font-bold hover:text-[#DCA54A] text-left block"
                                                x-text="item.nombre"
                                            ></button>
                                            <p class="text-white/70 text-xs mt-1" x-text="item.resumen"></p>
                                        </td>
                                        <td class="text-[#DCA54A] font-bold text-sm align-top" x-show="mostrarHonorarios" x-text="item.honorarios_formatted"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                {{-- CIERRE --}}
                <template x-if="slide.type === 'closing'">
                    <div class="roster-bg-cover roster-safe-bottom h-full w-full overflow-y-auto flex flex-col items-center justify-center gap-4 px-10 py-10 text-center">
                        <div class="flex items-center gap-6 flex-wrap justify-center" x-show="logos.length">
                            <template x-for="logo in logos" :key="logo">
                                <img :src="logo" class="h-10 object-contain">
                            </template>
                        </div>
                        <img src="{{ asset('images/separador.png') }}" class="h-2 opacity-80" alt="">
                        <p class="text-[#DCA54A] font-bold text-sm" x-show="outroText" x-text="outroText"></p>
                        <div class="text-xs text-white/80 space-y-2 max-w-xl">
                            <p>Talento sujeto a disponibilidad y se cotizará caso por caso con previo conocimiento del proyecto solicitado.</p>
                            <p>Más requerimientos según el lugar de presentación y el aforo</p>
                            <p>*Transporte *Hospedaje * Viáticos</p>
                            <p>*Equipo de audio e iluminación de acuerdo al Rider.</p>
                            <p>Depósito para bloquear la fecha del 50% a la firma del contrato y 50% restante 10 días antes de la presentación.</p>
                            <p>Cualquier otro espectáculo no incluido, podrá ser cotizado a petición del cliente.</p>
                        </div>
                        <div class="text-xs space-y-1" x-show="contacto.length">
                            <template x-for="item in contacto" :key="item.tipo + item.valor">
                                <p><span class="font-bold" x-text="item.tipo.toUpperCase() + ':'"></span> <span x-text="item.valor"></span></p>
                            </template>
                        </div>
                        <p class="text-[#DCA54A] font-black text-xl mt-2">www.capetilloproducciones.mx</p>
                    </div>
                </template>
            </div>
        </template>

        {{-- Controles inferiores: vista, navegación y contador --}}
        <div class="absolute bottom-4 inset-x-0 z-20 flex items-center justify-center gap-4 px-4">
            <div class="dropdown dropdown-top">
                <label tabindex="0" class="btn btn-sm bg-[#262626] border-[#DCA54A] text-[#DCA54A] hover:bg-[#333]">
                    Vista <span x-text="modeLabel()"></span>
                </label>
                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-[#262626] rounded-box w-44 border border-[#DCA54A]/40 mb-2">
                    <li><a @click="setMode(1)" :class="mode===1 && 'text-[#DCA54A] font-bold'">1 por vista</a></li>
                    <li><a @click="setMode(2)" :class="mode===2 && 'text-[#DCA54A] font-bold'">2 por vista</a></li>
                    <li><a @click="setMode(4)" :class="mode===4 && 'text-[#DCA54A] font-bold'">4 por vista</a></li>
                    <li><a @click="setMode(0)" :class="mode===0 && 'text-[#DCA54A] font-bold'">Lista</a></li>
                </ul>
            </div>

            <button @click="prev()" class="btn btn-circle btn-sm bg-[#262626] border-[#DCA54A] text-[#DCA54A]">❮</button>

            <span class="text-xs text-white/60 whitespace-nowrap">
                <span x-text="current + 1"></span> / <span x-text="slides.length"></span>
            </span>

            <button @click="next()" class="btn btn-circle btn-sm bg-[#262626] border-[#DCA54A] text-[#DCA54A]">❯</button>
        </div>

        {{-- Diálogo de imagen (modo lista) --}}
        <div
            x-show="imageModalOpen"
            x-cloak
            @click.self="closeImageModal()"
            @keydown.window.escape="closeImageModal()"
            class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="relative max-w-lg w-full text-center">
                <button @click="closeImageModal()" class="absolute -top-10 right-0 text-white/70 hover:text-[#DCA54A] text-2xl">✕</button>
                <img :src="imageModalItem?.imagen" class="w-full max-h-[70vh] object-contain rounded-lg mx-auto" x-show="imageModalItem?.imagen">
                <h3 class="text-[#DCA54A] font-bold text-xl mt-4" x-text="imageModalItem?.nombre"></h3>
                <p class="text-white/80 text-sm mt-2" x-text="imageModalItem?.resumen"></p>
            </div>
        </div>
    </div>

    <script>
        function rosterViewer(data) {
            return {
                rosterName: data.name,
                introText: data.intro_text,
                outroText: data.outro_text,
                mostrarHonorarios: data.mostrar_honorarios,
                separar: data.separar_por_categoria,
                logos: data.logos,
                contacto: data.contacto,
                talents: data.talents,
                mode: [1, 2, 4].includes(data.default_mode) ? data.default_mode : (data.default_mode === 0 ? 0 : 1),
                current: 0,
                slides: [],

                imageModalOpen: false,
                imageModalItem: null,

                openImageModal(item) {
                    this.imageModalItem = item;
                    this.imageModalOpen = true;
                },

                closeImageModal() {
                    this.imageModalOpen = false;
                },

                touchStartX: 0,
                touchStartY: 0,

                init() {
                    this.buildSlides();
                },

                modeLabel() {
                    if (this.mode === 0) return 'Lista';
                    return this.mode + ' por vista';
                },

                setMode(mode) {
                    this.mode = mode;
                    this.buildSlides();
                },

                groupByCategoria(items) {
                    const groups = {};
                    items.forEach(item => {
                        const key = item.categoria || 'Sin categoría';
                        if (!groups[key]) groups[key] = [];
                        groups[key].push(item);
                    });
                    return groups;
                },

                buildSlides() {
                    const slides = [{ type: 'cover' }];
                    const groups = this.separar ? this.groupByCategoria(this.talents) : { '': this.talents };

                    for (const [categoria, items] of Object.entries(groups)) {
                        if (this.separar) {
                            slides.push({ type: 'section', title: categoria });
                        }

                        if (this.mode === 0) {
                            slides.push({ type: 'list', items });
                        } else {
                            for (let i = 0; i < items.length; i += this.mode) {
                                slides.push({ type: 'talents', items: items.slice(i, i + this.mode) });
                            }
                        }
                    }

                    slides.push({ type: 'closing' });
                    this.slides = slides;
                    this.current = 0;
                },

                next() {
                    if (this.current < this.slides.length - 1) this.current++;
                },

                prev() {
                    if (this.current > 0) this.current--;
                },

                touchStart(e) {
                    this.touchStartX = e.changedTouches[0].clientX;
                    this.touchStartY = e.changedTouches[0].clientY;
                },

                touchEnd(e) {
                    const deltaX = e.changedTouches[0].clientX - this.touchStartX;
                    const deltaY = e.changedTouches[0].clientY - this.touchStartY;

                    // Ignora swipes muy verticales (para no interferir con scroll en modo lista).
                    if (Math.abs(deltaX) < 50 || Math.abs(deltaX) < Math.abs(deltaY)) {
                        return;
                    }

                    if (deltaX < 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                },
            };
        }
    </script>
</body>
</html>