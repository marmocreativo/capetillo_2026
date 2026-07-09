<?php

namespace App\Http\Controllers;

use App\Mail\ContactRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(\App\Http\Requests\StoreContactMessageRequest $request)
    {
        \Illuminate\Support\Facades\Log::info('Contacto: request recibido', $request->all());

        $data = $request->validated();

        \Illuminate\Support\Facades\Log::info('Contacto: datos validados', $data);

        if ($data['type'] === 'general' && empty($data['talent_name'])) {
            $data['talent_name'] = 'Consulta general (formulario de contacto)';
        }

        try {
            $contactMessage = \App\Models\ContactMessage::create($data);
            \Illuminate\Support\Facades\Log::info('Contacto: mensaje guardado en BD', ['id' => $contactMessage->id]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contacto: error al guardar en BD', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'No se pudo guardar tu mensaje. Intenta de nuevo.'], 500);
        }

        $destino = config('services.contact.email');
        \Illuminate\Support\Facades\Log::info('Contacto: intentando enviar correo', ['destino' => $destino]);

        $publicLink = route('contact-messages.public.show', $contactMessage->public_token);

        try {
            Mail::to($destino)->send(new ContactRequestMail($data, $publicLink));
            \Illuminate\Support\Facades\Log::info('Contacto: correo enviado sin excepciones');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contacto: error al enviar correo', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // El mensaje ya quedó guardado en BD aunque falle el correo, así que no regresamos error aquí.
        }

        try {
            \Illuminate\Support\Facades\Mail::to($data['email'])->send(new \App\Mail\ContactConfirmationMail($contactMessage));
            \Illuminate\Support\Facades\Log::info('Contacto: correo de confirmación enviado al cliente');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contacto: error al enviar confirmación al cliente', [
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json(['message' => 'Mensaje enviado correctamente.']);
    }
}