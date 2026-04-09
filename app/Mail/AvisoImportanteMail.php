<?php

namespace App\Mail;

use App\Models\Aviso;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AvisoImportanteMail extends Mailable
{
    public Aviso $aviso;

    public function __construct(Aviso $aviso)
    {
        $this->aviso = $aviso;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aviso importante - Residencia Huellas Felices',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.aviso-importante',
            with: [
                'aviso' => $this->aviso,
            ],
        );
    }
}