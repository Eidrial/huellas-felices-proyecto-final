<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'edad',
        'peso',
        'foto',
        'dueno_id', //clave foranea
        'aprobado'  //aprobado = false o aprobado = true 
    ];

    //relación con el dueño (usuario)
    public function dueno()
    {
        return $this->belongsTo(User::class, 'dueno_id');
    }
}
