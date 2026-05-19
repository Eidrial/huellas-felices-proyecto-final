<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionRestablecerContra extends Notification
{
    use Queueable;

    //token para generar el enlace de recuperación
    protected string $token;

    //guardar el token
    public function __construct($token)
    {
        $this->token = $token;
    }

    //canal de envio
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    //correo
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de contraseña - Huellas Felices')
            ->view('emails.auth.recuperar-contra', [
                'url' => $url,
                'usuario' => $notifiable,
            ]);
    }

}
