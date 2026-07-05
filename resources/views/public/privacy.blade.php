@extends('layouts.public')

@section('title', 'Aviso de Privacidad | ' . config('app.name'))
@section('meta_description', 'Aviso de privacidad de Capetillo Producciones. Tus datos personales no se almacenan ni se venden a terceros.')

@section('public-content')

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold mb-2">Aviso de Privacidad</h1>
    <p class="opacity-60 text-sm mb-8">Última actualización: {{ now()->format('d/m/Y') }}</p>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body prose max-w-none">
            <p>
                En <strong>{{ config('app.name') }}</strong> valoramos y respetamos tu privacidad. Este aviso
                explica de forma clara y sencilla cómo tratamos la información que nos compartes.
            </p>

            <h2>¿Qué datos recopilamos?</h2>
            <p>
                Cuando completas alguno de nuestros formularios de contacto (por ejemplo, para solicitar
                información sobre un talento o cotizar un evento), recopilamos únicamente los datos que
                tú decides proporcionarnos: nombre, correo electrónico, teléfono y el mensaje que nos
                escribas.
            </p>

            <h2>¿Cómo usamos tu información?</h2>
            <p>
                Usamos estos datos exclusivamente para responder a tu solicitud y ponernos en contacto
                contigo respecto al talento o servicio que nos consultaste. No usamos tu información para
                ningún otro fin.
            </p>

            <h2>Almacenamiento y venta de datos</h2>
            <p>
                <strong>Tus datos personales no se almacenan de forma permanente ni se venden, rentan o
                comparten con terceros bajo ninguna circunstancia.</strong> La información que envías a
                través de nuestros formularios se utiliza únicamente para darte seguimiento a tu solicitud
                y no es utilizada con fines distintos ni compartida con empresas externas.
            </p>

            <h2>Contacto</h2>
            <p>
                Si tienes alguna duda sobre este aviso de privacidad, puedes escribirnos a
                <a href="mailto:ventas@capetilloproducciones.mx">ventas@capetilloproducciones.mx</a>
                o llamarnos al 55 2117 9110 / 55 1953 0300.
            </p>
        </div>
    </div>
</div>

@endsection