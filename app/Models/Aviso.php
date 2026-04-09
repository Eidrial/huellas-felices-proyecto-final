<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estancia;
use App\Models\User;

class Aviso extends Model
{
    protected $fillable = [
        'estancia_id',
        'user_id',
        'tipo',
        'mensaje',
    ];

    public function estancia()
    {
        return $this->belongsTo(Estancia::class);
    }
    //relacion con el usuario que envia el aviso (admin o cuidador)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
