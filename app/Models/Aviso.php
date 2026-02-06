<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mascota;
use App\Models\User;

class Aviso extends Model
{
    protected $fillable = [
        'mascota_id',
        'user_id',
        'mensaje',
    ];

    //relacion con la mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    //relacion con el usuario que envia el aviso (admin o cuidador)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
