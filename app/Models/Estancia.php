<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mascota;
use App\Models\Cuidado;

class Estancia extends Model
{
    protected $fillable = [
        'mascota_id',
        'estado',
        'fecha_entrada',
        'fecha_salida',
        'precio_dia',
        'precio_total',
        'cancelada_por',
    ];

    protected $dates = [
        'fecha_entrada',
        'fecha_salida',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function cuidados()
    {
        return $this->hasMany(Cuidado::class);
    }

    //estancias activas (confirmadas o en curso)
    public function scopeEstanciasActivas($query)
    {
        return $query->whereIn('estado', ['confirmada', 'activa']);
    }

    //estancias pendientes
    public function scopeEstanciasPendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    //FUNCIONES AUXILIARES

    //calcular el precio total automaticamente segun precio_dia y dias de estancia
    public function calcularPrecioTotal()
    {
        $entrada = new \DateTime($this->fecha_entrada);
        $salida = new \DateTime($this->fecha_salida);

        //calcular diferencia de dias entre enrrada y salida
        //la fecha de salida NO cuenta como dia estancia
        $dias = $entrada->diff($salida)->days;
        
        //el precio total es el precio por dia multiplicado por los dias reales de estancia
        $this->precio_total = round($this->precio_dia * $dias, 2);

        return $this->precio_total;
    }

    //validar si la fecha de entrada es superior o igual a mañana (T+1)
    public static function fechaValida($fecha)
    {
        return strtotime($fecha) >= strtotime('tomorrow');
    }

    //comprueba si hay disponibilidad para una estancia entre dos fechas
    //la residencia alojara 20 perros como max a la vez
    //fecha de salida NO ocupa plaza
    public static function hayDisponibilidad($entrada, $salida, $maxPerros = 20, $ignorarEstanciaId = null)
    {
        $entrada = new \DateTime($entrada);
        $salida = new \DateTime($salida);

        //recorrer cada dia del rango solicitado (desde fecha_entrada hasta el día ANTERIOR a fecha_salida)
        $fecha = clone $entrada;

        while ($fecha < $salida) {

            //contamos cuantas estancias hay ese dia concreto
            $ocupadas = self::whereIn('estado', ['confirmada', 'activa'])
                ->when($ignorarEstanciaId, function ($query) use ($ignorarEstanciaId) {
                    $query->where('id', '!=', $ignorarEstanciaId);
                })
                ->where('fecha_entrada', '<=', $fecha->format('Y-m-d'))
                ->where('fecha_salida', '>', $fecha->format('Y-m-d'))
                ->count();

            //si esta lleno, no hay disponibilidad
            if ($ocupadas >= $maxPerros) {
                return false;
            }

            //pasar al siguiente dia
            $fecha->modify('+1 day');
        }

        //si ningun dia supera el limite, hay disponibilidad
        return true;
    }

    //indica si una estancia puede ampliarse hasta una nueva fecha de salida
    //si la nueva fecha es anterior o igual, se esta acortando = siempre permitido
    //si es posterior, se comprueba disponibilidad
    public function puedeAmpliarse($nuevaSalida)
    {
        //convertir a fechas
        $salidaActual = strtotime($this->fecha_salida);
        $nuevaSalida = strtotime($nuevaSalida);

        //acortar estancia siempre es posible
        if ($nuevaSalida <= $salidaActual) {
            return true;
        }

        //comprobar disponibilidad solo en los dias extra
        return self::hayDisponibilidad(
            $this->fecha_salida,
            date('Y-m-d', $nuevaSalida),
            20,
            $this->id
        );
    }

    //confirma la estancia (si hay disponibilidad)
    public function confirmar()
    {
        if (!self::hayDisponibilidad($this->fecha_entrada, $this->fecha_salida)) {
            return false;
        }

        $this->estado = 'confirmada';
        $this->save();

        return true;
    }

    //inicia la estancia (el perro entra en la residencia)
    public function iniciar()
    {
        if ($this->estado !== 'confirmada') {
            return false;
        }

        $this->estado = 'activa';
        $this->save();

        return true;
    }

    //finaliza la estancia
    public function finalizar()
    {
        if ($this->estado != 'activa') {
            return false;
        }

        $this->estado = 'finalizada';
        $this->save();

        return true;
    }

    //cancela la estancia
    public function cancelar($quien = 'usuario')
    {
        $this->estado = 'cancelada';
        $this->cancelada_por = $quien;
        $this->save();
    }

}

