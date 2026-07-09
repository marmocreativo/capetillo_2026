<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function build()
    {
        $link = route('contact-messages.public.show', $this->contactMessage->public_token);

        return $this->subject('Recibimos tu solicitud - Capetillo Producciones')
            ->view('emails.contact-confirmation')
            ->with(['contactMessage' => $this->contactMessage, 'link' => $link]);
    }
}