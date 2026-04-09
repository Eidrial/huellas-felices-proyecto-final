<?php

namespace App\Mail;

use App\Models\Estancia;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class FacturaDisponibleMail extends Mailable
{
    public Estancia $estancia;

    public function __construct(Estancia $estancia)
    {
        $this->estancia = $estancia;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura disponible - Residencia Huellas Felices',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.factura-disponible',
            with: [
                'estancia' => $this->estancia,
            ],
        );
    }
}