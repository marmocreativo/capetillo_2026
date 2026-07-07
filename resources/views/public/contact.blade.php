@extends('layouts.public')

@section('title', 'Contacto | ' . config('app.name'))
@section('meta_description', 'Contáctanos: 55 2117 9110, 55 1953 0300 o ventas@capetilloproducciones.mx. Estaremos en contacto contigo en menos de 24 horas.')

@section('public-content')

<h1 class="text-3xl md:text-4xl font-bold text-center mb-2">Contacto</h1>
<p class="text-center opacity-70 mb-10">Estaremos en contacto contigo en menos de 24 horas.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10 max-w-5xl mx-auto">

    <div>
        <div class="card bg-base-100 shadow-lg mb-6">
            <div class="card-body">
                <h2 class="card-title text-primary">Teléfonos</h2>
                <p>55 2117 9110</p>
                <p>55 1953 0300</p>

                <div class="divider"></div>

                <h2 class="card-title text-primary">Correo electrónico</h2>
                <a href="mailto:ventas@capetilloproducciones.mx" class="link link-hover">
                    ventas@capetilloproducciones.mx
                </a>

                <div class="divider"></div>

                <h2 class="card-title text-primary">Síguenos</h2>
                <div class="flex gap-3">
                    <a href="https://www.facebook.com/capetilloproducciones" target="_blank" rel="noopener" class="btn btn-outline btn-primary btn-sm">Facebook</a>
                    <a href="https://www.instagram.com/capetilloproducciones/" target="_blank" rel="noopener" class="btn btn-outline btn-primary btn-sm">Instagram</a>
                    <a href="https://www.tiktok.com/@capetilloproducciones" target="_blank" rel="noopener" class="btn btn-outline btn-primary btn-sm">TikTok</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-2">Envíanos un mensaje</h2>

            <div id="contact-page-alert" class="hidden alert mb-4 text-sm"></div>

            <form id="contact-page-form" class="flex flex-col gap-3">
                @csrf
                <input type="hidden" name="type" value="general">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre</span></label>
                        <input type="text" name="first_name" class="input input-bordered w-full" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Apellidos</span></label>
                        <input type="text" name="last_name" class="input input-bordered w-full">
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Email</span></label>
                    <input type="email" name="email" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Teléfono</span></label>
                    <input type="tel" name="phone" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">¿A cuál de nuestros talentos deseas contratar?</span></label>
                    <input type="text" name="talent_name" class="input input-bordered w-full" placeholder="Opcional">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Mensaje</span></label>
                    <textarea name="message" class="textarea textarea-bordered w-full" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-2">Enviar mensaje</button>
            </form>
        </div>
    </div>

</div>

<script>
(function () {
    const form = document.getElementById('contact-page-form');
    const alertBox = document.getElementById('contact-page-alert');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const firstName = form.querySelector('[name="first_name"]').value;
        const lastName = form.querySelector('[name="last_name"]').value;

        const payload = new FormData();
        payload.append('_token', form.querySelector('input[name="_token"]').value);
        payload.append('type', 'general');
        payload.append('name', `${firstName} ${lastName}`.trim());
        payload.append('email', form.querySelector('[name="email"]').value);
        payload.append('phone', form.querySelector('[name="phone"]').value);
        payload.append('talent_name', form.querySelector('[name="talent_name"]').value);
        payload.append('message', form.querySelector('[name="message"]').value);

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';

        try {
            const response = await fetch('{{ route('contact.send') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: payload,
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error al enviar.');

            alertBox.textContent = '¡Mensaje enviado! Estaremos en contacto contigo en menos de 24 horas.';
            alertBox.classList.remove('hidden', 'alert-error');
            alertBox.classList.add('alert-success');
            form.reset();
        } catch (err) {
            alertBox.textContent = 'Hubo un error al enviar tu mensaje. Intenta llamarnos directamente.';
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