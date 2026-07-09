<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data, public ?string $publicLink = null)
    {
    }

    public function build()
    {
        return $this->subject('Nueva solicitud de contratación: ' . $this->data['talent_name'])
            ->view('emails.contact-request')
            ->with(['data' => $this->data, 'publicLink' => $this->publicLink]);
    }
}