@extends('layouts.app')

@section('title', 'Carlos Jaime — Ventas y Administración | Capetillo Producciones')
@section('meta_description', 'Carlos Jaime, Ventas y Administración de Capetillo Producciones. Representación artística y organización de eventos.')
@section('og_image', asset('images/tarjeta-carlos-og.jpg'))

@section('content')
<div class="min-h-screen flex items-start sm:items-center justify-center bg-base-300 px-4 py-8">
    <div class="w-full max-w-sm bg-base-100 rounded-3xl shadow-2xl overflow-hidden">

        {{-- Banner --}}
        <div class="relative h-40 bg-cover bg-center" style="background-image: url('{{ asset('images/tarjeta-banner.jpg') }}');">
            <div class="absolute inset-0 bg-black/50"></div>

            {{-- Acciones superiores --}}
            <div class="relative flex items-center justify-between px-4 pt-4 text-white text-xs">
                <button type="button" onclick="compartirTarjeta()" class="flex flex-col items-center gap-1 opacity-90 hover:opacity-100">
                    <hero-icon-outline name="share" class="w-5 h-5"></hero-icon-outline>
                    <span>Comparte</span>
                </button>

                <a href="{{ route('tarjeta.carlos.vcard') }}" class="flex flex-col items-center gap-1 opacity-90 hover:opacity-100">
                    <hero-icon-outline name="download" class="w-5 h-5"></hero-icon-outline>
                    <span>Guardar</span>
                </a>

                <button type="button" onclick="document.getElementById('qr_modal').showModal()" class="flex flex-col items-center gap-1 opacity-90 hover:opacity-100">
                    <hero-icon-outline name="qrcode" class="w-5 h-5"></hero-icon-outline>
                    <span>Código Qr</span>
                </button>
            </div>
        </div>

        {{-- Avatar sobrepuesto --}}
        <div class="relative flex justify-center">
            <div class="absolute -top-14 w-28 h-28 rounded-full ring-4 ring-base-100 overflow-hidden bg-base-200">
                <img src="{{ asset('images/avatar-carlos.png') }}" alt="Carlos Jaime" class="w-full h-full object-cover">
            </div>
        </div>

        {{-- Datos --}}
        <div class="pt-20 pb-6 px-6 text-center">
            <h1 class="text-xl font-bold text-base-content">Carlos Jaime</h1>
            <p class="text-sm font-semibold text-primary mt-1">Ventas y Administración</p>
            <p class="text-xs italic text-base-content/60 mt-1">Capetillo Producciones</p>

            {{-- Botones de contacto circulares --}}
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="tel:+525518365242" class="btn btn-circle btn-primary" aria-label="Llamar">
                    <hero-icon-outline name="phone" class="w-5 h-5"></hero-icon-outline>
                </a>
                <a href="https://wa.me/525518365242" target="_blank" class="btn btn-circle btn-primary" aria-label="WhatsApp">
                    <hero-icon-outline name="chat-alt-2" class="w-5 h-5"></hero-icon-outline>
                </a>
                <a href="mailto:administracion@capetilloproducciones.mx" class="btn btn-circle btn-primary" aria-label="Correo">
                    <hero-icon-outline name="mail" class="w-5 h-5"></hero-icon-outline>
                </a>
            </div>

            {{-- Botón al sitio web --}}
            <a href="{{ route('home') }}"
               class="mt-6 flex items-center justify-center gap-2 w-full py-3 rounded-full bg-gradient-to-r from-[#DCA54A] to-[#F2C572] text-neutral font-bold text-sm shadow-lg hover:opacity-90 transition">
                <hero-icon-outline name="globe-alt" class="w-5 h-5"></hero-icon-outline>
                Visitar sitio web
            </a>

            {{-- Separador con escudo --}}
            <div class="flex items-center gap-3 my-6">
                <div class="h-px flex-1 bg-base-300"></div>
                <img src="{{ asset('images/escudo-capetillo.png') }}" alt="" class="w-6 h-6 opacity-80">
                <div class="h-px flex-1 bg-base-300"></div>
            </div>

            {{-- Teléfono y correo --}}
            <div class="space-y-2 text-left text-sm">
                <a href="tel:+525518365242" class="flex items-center gap-3 text-base-content/80 hover:text-primary transition">
                    <hero-icon-outline name="phone" class="w-4 h-4 text-primary shrink-0"></hero-icon-outline>
                    <span>(55) 1836 5242</span>
                </a>
                <a href="mailto:administracion@capetilloproducciones.mx" class="flex items-center gap-3 text-base-content/80 hover:text-primary transition break-all">
                    <hero-icon-outline name="mail" class="w-4 h-4 text-primary shrink-0"></hero-icon-outline>
                    <span class="text-xs">administracion@capetilloproducciones.mx</span>
                </a>
            </div>
        </div>

        {{-- Franja inferior --}}
        <div class="bg-neutral text-neutral-content text-center text-xs font-semibold tracking-wide py-3 border-t-2 border-primary">
            REPRESENTACIÓN ARTÍSTICA / ORGANIZACIÓN DE EVENTOS
        </div>
    </div>
</div>

{{-- Modal QR --}}
<dialog id="qr_modal" class="modal">
    <div class="modal-box max-w-xs text-center">
        <h3 class="font-bold text-lg mb-4">Escanea para compartir</h3>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&ecc=M&data={{ urlencode(url()->current()) }}"
             alt="Código QR"
             class="mx-auto rounded-lg">
        <form method="dialog" class="mt-4">
            <button class="btn btn-primary btn-sm">Cerrar</button>
        </form>
    </div>
</dialog>

<script>
    function compartirTarjeta() {
        if (navigator.share) {
            navigator.share({
                title: 'Carlos Jaime — Ventas y Administración',
                text: 'Contacta a Carlos Jaime, Ventas y Administración de Capetillo Producciones.',
                url: window.location.href,
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert('Enlace copiado al portapapeles.');
        }
    }
</script>
@endsection