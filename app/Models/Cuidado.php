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
    public function scopePendientes($consulta)
    {
        return $consulta->where('completado', false);
    }

    //solo cuidados realizados
    public function scopeRealizados($consulta)
    {
        return $consulta->where('completado', true);
    }

    //cuidados base, es decir, todos menos los extras
    public function scopeBase($consulta)
    {
        return $consulta->where('tipo', '!=', 'extra');
    }

    //solo cuidados extra
    public function scopeExtras($consulta)
    {
        return $consulta->where('tipo', 'extra');
    }

    //cuidados pendientes y que no sean extras
    public function scopePendientesBase($consulta)
    {
        return $consulta->pendientes()->base();
    }

    //CUIDADOS ESTANCIAS ADMIN
    //saber si un cuidado esta atrasado
    public function esAtrasado($hoy, $ahoraHora)
    {
        if ($this->fecha < $hoy) {
            return true;
        }

        if ($this->fecha == $hoy && $this->hora && $this->hora < $ahoraHora) {
            return true;
        }

        return false;
    }

    //saber si un cuidado de hoy esta atrasado
    public function esAtrasadoHoy($hoy, $ahoraHora)
    {
        return $this->fecha == $hoy && $this->hora && $this->hora < $ahoraHora;
    }

    //saber si un cuidado puede marcarse como hecho 
    public function sePuedeMarcar($hoy, $ahoraMas15)
    {
        if ($this->fecha > $hoy) {
            return false;
        }

        if ($this->fecha == $hoy && $this->hora && $this->hora > $ahoraMas15) {
            return false;
        }

        return true;
    }

    //saber si todavia no se puede marcar
    public function noSePuedeMarcar($hoy, $ahoraMas15)
    {
        return !$this->sePuedeMarcar($hoy, $ahoraMas15);
    }

    //mostrar hora corta
    public function horaCorta()
    {
        return $this->hora ? substr($this->hora, 0, 5) : null;
    }

    //hora desde la que estara disponible
    public function disponibleDesde()
    {
        if (!$this->hora) {
            return null;
        }

        return date('H:i', strtotime($this->hora . ' -15 minutes'));
    }

    //filtros para la vista de cuidados
    public static function getFiltrosVisuales()
    {
        return [
            'hoy' => [
                'texto' => 'Hoy',
                'activo' => 'bg-[#3a7abf] text-white border-[#3a7abf]',
                'inactivo' => 'border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a]',
            ],
            'atrasadas' => [
                'texto' => 'Atrasadas',
                'activo' => 'bg-[#c9342e] text-white border-[#c9342e]',
                'inactivo' => 'border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9342e] hover:text-[#9b2a2a]',
            ],
            'realizados' => [
                'texto' => 'Realizados',
                'activo' => 'bg-[#3a7a2e] text-white border-[#3a7a2e]',
                'inactivo' => 'border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7a2e] hover:text-[#2d5a27]',
            ],
        ];
    }

}