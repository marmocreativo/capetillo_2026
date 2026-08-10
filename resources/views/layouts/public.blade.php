@extends('layouts.app')

@section('content')

<div class="drawer overflow-x-hidden">
    <input id="mobile-drawer" type="checkbox" class="drawer-toggle">

    <div class="drawer-content overflow-x-hidden">

        <header id="site-header" class="sticky top-0 z-50 transition-all duration-300 bg-transparent">
            <div class="navbar px-4 md:px-6">
                <div class="navbar-start gap-3 lg:gap-6 min-w-0">
                    <a href="{{ url('/') }}" class="shrink-0">
                        <img src="{{ asset('images/logo_capetillo_blanco.svg') }}" alt="{{ config('app.name') }}" class="h-[40px] w-auto">
                    </a>

                    <form action="{{ route('search') }}" method="GET" class="hidden md:block w-full max-w-[10rem] lg:max-w-xs">
                        <label class="input input-bordered input-sm flex items-center gap-2 bg-base-100/90">
                            <svg class="h-4 w-4 opacity-60 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar talento..." class="grow" autocomplete="off">
                        </label>
                    </form>
                </div>

                <div class="navbar-end gap-1 lg:gap-2">
                    <div class="hidden xl:flex items-center gap-0.5">
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-ghost px-2">Talento</a>
                        <a href="{{ route('about') }}" class="btn btn-sm btn-ghost px-2">Quiénes Somos</a>

                        <div class="dropdown dropdown-hover">
                            <div tabindex="0" role="button" class="btn btn-sm btn-ghost px-2 gap-1">
                                Golden Party
                                <svg class="h-3 w-3 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[70] w-64 p-2 shadow-lg">
                                <li><a href="{{ route('golden-party') }}" class="font-semibold">¿Qué es Golden Party?</a></li>
                                @if ($headerEvents->isNotEmpty())
                                    <div class="divider my-1"></div>
                                    @foreach ($headerEvents as $headerEvent)
                                        <li><a href="{{ route('events.show', $headerEvent->slug) }}">{{ $headerEvent->title }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <a href="{{ route('live-media') }}" class="btn btn-sm btn-ghost px-2">Live Media</a>
                        <a href="{{ route('network') }}" class="btn btn-sm btn-ghost px-2">Capetillo Network</a>
                        <a href="{{ route('contact.page') }}" class="btn btn-sm btn-ghost px-2">Contacto</a>
                    </div>

                    @auth
                        <a href="{{ url('/admin') }}" class="btn btn-sm btn-primary shrink-0">Administración</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary shrink-0">Iniciar sesión</a>
                    @endauth

                    <label for="mobile-drawer" class="btn btn-ghost btn-circle xl:hidden" aria-label="Abrir menú">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>
                </div>
            </div>
        </header>

        <main class="p-6">
            @yield('public-content')
        </main>

        <footer class="bg-base-100 border-t border-base-300 mt-20">
            <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">

                <div>
                    <img src="{{ asset('images/logo_capetillo_blanco.svg') }}" alt="{{ config('app.name') }}" class="h-12 w-auto mb-4 p-2 rounded">
                    <p class="text-sm opacity-70 leading-relaxed">
                        Agencia de contratación de talento artístico con más de 15 años conectando eventos
                        con los comediantes, cantantes, bandas y conferencistas más solicitados de México.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold mb-3">Enlaces</h3>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li><a href="{{ route('categories.index') }}">Talento</a></li>
                        <li><a href="{{ route('about') }}">Quiénes Somos</a></li>
                        <li><a href="{{ route('golden-party') }}">Golden Party</a></li>
                        <li><a href="{{ route('live-media') }}">Live Media</a></li>
                        <li><a href="{{ route('network') }}">Capetillo Network</a></li>
                        <li><a href="{{ route('contact.page') }}">Contacto</a></li>
                        <li><a href="{{ route('privacy') }}" class="link link-hover">Aviso de privacidad</a></li>
                    </ul>
                </div>

                <div>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li>
                            <a href="tel:+525521179110" class="link link-hover flex items-center gap-2">
                                <hero-icon-outline name="phone" class="h-4 w-4 shrink-0"></hero-icon-outline>
                                55 2117 9110
                            </a>
                        </li>
                        <li>
                            <a href="tel:+525519530300" class="link link-hover flex items-center gap-2">
                                <hero-icon-outline name="phone" class="h-4 w-4 shrink-0"></hero-icon-outline>
                                55 1953 0300
                            </a>
                        </li>
                        <li>
                            <a href="mailto:ventas@capetilloproducciones.mx" class="link link-hover flex items-center gap-2">
                                <hero-icon-outline name="envelope" class="h-4 w-4 shrink-0"></hero-icon-outline>
                                ventas@capetilloproducciones.mx
                            </a>
                        </li>
                    </ul>

                    <div class="flex gap-3 mt-4">
                        <a href="https://www.facebook.com/capetilloproducciones" target="_blank" rel="noopener" class="btn btn-circle btn-sm btn-ghost" aria-label="Facebook">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12"/></svg>
                        </a>
                        <a href="https://www.instagram.com/capetilloproducciones/" target="_blank" rel="noopener" class="btn btn-circle btn-sm btn-ghost" aria-label="Instagram">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c2.7 0 3.1 0 4.1.1 1 .1 1.7.2 2.3.5.6.2 1.1.6 1.6 1.1.5.5.8 1 1.1 1.6.2.6.4 1.3.5 2.3.1 1 .1 1.4.1 4.1s0 3.1-.1 4.1c-.1 1-.2 1.7-.5 2.3-.2.6-.6 1.1-1.1 1.6-.5.5-1 .8-1.6 1.1-.6.2-1.3.4-2.3.5-1 .1-1.4.1-4.1.1s-3.1 0-4.1-.1c-1-.1-1.7-.2-2.3-.5-.6-.2-1.1-.6-1.6-1.1-.5-.5-.8-1-1.1-1.6-.2-.6-.4-1.3-.5-2.3C2 15.1 2 14.7 2 12s0-3.1.1-4.1c.1-1 .2-1.7.5-2.3.2-.6.6-1.1 1.1-1.6.5-.5 1-.8 1.6-1.1.6-.2 1.3-.4 2.3-.5C8.9 2 9.3 2 12 2m0 1.8c-2.7 0-3 0-4 .1-.9 0-1.4.2-1.7.3-.4.2-.7.3-1 .6-.3.3-.5.6-.6 1-.1.3-.3.8-.3 1.7-.1 1-.1 1.3-.1 4s0 3 .1 4c0 .9.2 1.4.3 1.7.2.4.3.7.6 1 .3.3.6.5 1 .6.3.1.8.3 1.7.3 1 .1 1.3.1 4 .1s3 0 4-.1c.9 0 1.4-.2 1.7-.3.4-.2.7-.3 1-.6.3-.3.5-.6.6-1 .1-.3.3-.8.3-1.7.1-1 .1-1.3.1-4s0-3-.1-4c0-.9-.2-1.4-.3-1.7-.2-.4-.3-.7-.6-1-.3-.3-.6-.5-1-.6-.3-.1-.8-.3-1.7-.3-1-.1-1.3-.1-4-.1M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4M17.5 6.5a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0"/></svg>
                        </a>
                        <a href="https://www.tiktok.com/@capetilloproducciones" target="_blank" rel="noopener" class="btn btn-circle btn-sm btn-ghost" aria-label="TikTok">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16.6 5.8a4.3 4.3 0 0 1-3.1-1.3V15a5.2 5.2 0 1 1-4.5-5.2v2.6a2.6 2.6 0 1 0 1.9 2.5V2h2.6a4.3 4.3 0 0 0 3.1 3.8z"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            <div class="border-t border-base-300 py-4 text-center text-xs opacity-60">
                Copyright © 2026 Capetillo Producciones
            </div>
        </footer>

    </div>

    <div class="drawer-side z-[60]">
        <label for="mobile-drawer" aria-label="Cerrar menú" class="drawer-overlay"></label>

        <div class="menu bg-base-100 min-h-full w-80 max-w-[85vw] p-6 gap-2">
            <a href="{{ url('/') }}" class="mb-4">
                <img src="{{ asset('images/logo_capetillo_blanco.svg') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
            </a>

            <form action="{{ route('search') }}" method="GET" class="mb-4">
                <label class="input input-bordered flex items-center gap-2">
                    <svg class="h-4 w-4 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar talento..." class="grow" autocomplete="off">
                </label>
            </form>

            <ul class="space-y-1">
                <li><a href="{{ route('categories.index') }}">Talento</a></li>
                <li><a href="{{ route('about') }}">Quiénes Somos</a></li>
                <li>
                    <details>
                        <summary>Golden Party</summary>
                        <ul>
                            <li><a href="{{ route('golden-party') }}" class="font-semibold">¿Qué es Golden Party?</a></li>
                            @foreach ($headerEvents as $headerEvent)
                                <li><a href="{{ route('events.show', $headerEvent->slug) }}">{{ $headerEvent->title }}</a></li>
                            @endforeach
                        </ul>
                    </details>
                </li>
                <li><a href="{{ route('live-media') }}">Live Media</a></li>
                <li><a href="{{ route('network') }}">Capetillo Network</a></li>
                <li><a href="{{ route('contact.page') }}">Contacto</a></li>
            </ul>

            <div class="mt-6 pt-4 border-t border-base-300">
                @auth
                    <a href="{{ url('/admin') }}" class="btn btn-primary btn-block">Administración</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    const header = document.getElementById('site-header');
    if (!header) return;

    function updateHeader() {
        if (window.scrollY > 20) {
            header.classList.add('bg-base-100/95', 'backdrop-blur', 'shadow-md');
            header.classList.remove('bg-transparent');
        } else {
            header.classList.remove('bg-base-100/95', 'backdrop-blur', 'shadow-md');
            header.classList.add('bg-transparent');
        }
    }

    window.addEventListener('scroll', updateHeader);
    updateHeader();
})();
</script>

@endsection