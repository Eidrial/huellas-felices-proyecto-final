<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Estancia;

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

    //una mascota puede tener muchas estancias
    public function estancias()
    {
        return $this->hasMany(Estancia::class);
    }
}
