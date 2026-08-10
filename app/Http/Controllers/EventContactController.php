<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventContactRequest;
use App\Models\EventContact;

class EventContactController extends Controller
{
    public function store(StoreEventContactRequest $request)
    {
        EventContact::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Mensaje enviado correctamente.']);
        }

        return back()->with('status', 'Gracias, tu mensaje fue enviado correctamente. Te contactaremos pronto.');
    }
}