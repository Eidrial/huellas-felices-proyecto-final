<?php

namespace App\Mail;

use App\Models\Estancia;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EstanciaConfirmadaMail extends Mailable
{
    public Estancia $estancia;

    public function __construct(Estancia $estancia)
    {
        $this->estancia = $estancia;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva confirmada - Residencia Huellas Felices',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.estancia-confirmada',
            with: [
                'estancia' => $this->estancia,
            ],
        );
    }
}