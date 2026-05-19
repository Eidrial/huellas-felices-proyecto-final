<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Mascota;
use App\Models\Estancia;
use App\Notifications\NotificacionRestablecerContra;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellidos',
        'dni',
        'telefono',
        'direccion',
        'email',
        'password',
        'role',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //un usuario puede tener varias mascotas
    //acceder y mostrar las mascotas de x usuario
    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'dueno_id');
    }

    //un usuario puede tener muchas estancias a traves de sus mascotas
    //primero se obtienen las mascotas del usuario y luego las estancias de esas mascotas
    public function estancias()
    {
        return $this->hasManyThrough(
            Estancia::class,
            Mascota::class,
            'dueno_id', //clave foranea en mascotas que apunta al usuario
            'mascota_id' //clave foranea en estancias que apunta a la mascota
        );
    }

    //enviar notificación personalizada para restablecer contraseña
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new NotificacionRestablecerContra($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            public function toMail($notifiable)
            {
                $url = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject('Verifica tu correo · Huellas Felices')
                    ->view('emails.verificar-email', [
                        'url' => $url,
                        'user' => $notifiable,
                    ]);
            }
        });
    }
}
