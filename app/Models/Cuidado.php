<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estancia;
use App\Models\User;

class Cuidado extends Model
{
    protected $fillable = [
        'estancia_id',
        'tipo',
        'descripcion',
        'fecha',
        'hora',
        'precio_extra', //si es extra
        'user_id',  //quien lo crea o realiza
        'completado',   //true/false
    ];

    public function estancia()
    {
        return $this->belongsTo(Estancia::class);
    }

    //relacion con el usuario que realiza el cuidado
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //solo cuidados pendientes
    public function scopePendiente($query)
    {
        return $query->where('completado', false);
    }
}
