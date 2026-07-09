<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContactExtraInfoRequest;
use App\Models\ContactMessage;

class PublicContactMessageController extends Controller
{
    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->load('cotizacionTalents');

        return view('public.contact-messages.show', compact('contactMessage'));
    }

    public function update(UpdateContactExtraInfoRequest $request, ContactMessage $contactMessage)
    {
        abort_if($contactMessage->extra_info_completed_at !== null, 403, 'Esta información ya fue registrada y no puede modificarse.');

        $data = $request->validated();
        $data['extra_info_completed_at'] = now();

        $contactMessage->update($data);

        return redirect()
            ->route('contact-messages.public.show', $contactMessage->public_token)
            ->with('status', 'Gracias, tu información fue registrada correctamente.');
    }
}