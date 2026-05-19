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

    //PARA USAR EN VISTAS
    //colores y texto segun el estado de aprobacion de la mascota
    public function getEstadoVisual()
    {
        if ($this->aprobado === 1) {
            return [
                'texto' => 'Aprobada',
                'insignia' => 'bg-[#eef5e8] text-[#2d5a27] border-[#b0cc9e]',
                'barra' => 'bg-[#5a9e47]',
                'punto' => 'bg-[#5a9e47]',
                'etiqueta' => 'text-[#2d5a27]',
            ];
        }

        if ($this->aprobado === 0) {
            return [
                'texto' => 'No aprobada',
                'insignia' => 'bg-[#fceaea] text-[#9b2a2a] border-[#e8b4b4]',
                'barra' => 'bg-[#c9342e]',
                'punto' => 'bg-[#c9342e]',
                'etiqueta' => 'text-[#9b2a2a]',
            ];
        }

        return [
            'texto' => 'Pendiente de revisión',
            'insignia' => 'bg-[#fef8ec] text-[#7a4e10] border-[#e4c57a]',
            'barra' => 'bg-[#c9821a]',
            'punto' => 'bg-[#c9821a]',
            'etiqueta' => 'text-[#7a4e10]',
        ];
    }

    //texto corto del estado para javascript
    public function estadoAprobacionTexto()
    {
        if ($this->aprobado === 1) {
            return 'aprobada';
        }

        if ($this->aprobado === 0) {
            return 'no-aprobada';
        }

        return 'pendiente';
    }
}
