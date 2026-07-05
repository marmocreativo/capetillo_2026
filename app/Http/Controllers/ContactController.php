<?php

namespace App\Http\Controllers;

use App\Mail\ContactRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'talent_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $data['talent_name'] = $data['talent_name'] ?: 'Consulta general (formulario de contacto)';

        Mail::to(config('services.contact.email'))->send(new ContactRequestMail($data));

        return response()->json(['message' => 'Mensaje enviado correctamente.']);
    }
}