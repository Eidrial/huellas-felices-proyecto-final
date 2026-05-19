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
    
    //datos visuales segun el tipo de aviso
    public function getAvisoVisual()
    {
        if ($this->tipo == 'importante') {
            return [
                'texto' => 'Importante',
                'barra' => 'bg-[#c9342e]',
                'punto' => 'bg-[#c9342e]',
                'etiqueta' => 'text-[#9b2a2a]',
            ];
        }

        return [
            'texto' => 'Info',
            'barra' => 'bg-[#3a7abf]',
            'punto' => 'bg-[#3a7abf]',
            'etiqueta' => 'text-[#1a4f8a]',
        ];
    }
}
