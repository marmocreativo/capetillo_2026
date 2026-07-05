@component('mail::message')
# Nueva solicitud de contratación

**Talento solicitado:** {{ $data['talent_name'] }}

**Nombre:** {{ $data['name'] }}
**Correo:** {{ $data['email'] }}
**Teléfono:** {{ $data['phone'] ?: 'No proporcionado' }}

**Mensaje:**

{{ $data['message'] }}

Gracias,<br>
{{ config('app.name') }}
@endcomponent