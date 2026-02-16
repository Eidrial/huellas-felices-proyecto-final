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

        //si la salida es igual o anterior a la entrada, la estancia es invalida y el precio total se pone a 0
        if ($salida <= $entrada) {
            $this->precio_total = 0;
            return 0;
        }

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
    //la residencia alojara 20 perros como max a la vez (segun config)
    //fecha de salida NO ocupa plaza
    public static function hayDisponibilidad($entrada, $salida, $ignorarEstanciaId = null)
    {

        //obtener maximo de perros desde config
        $maxPerros = config('residencia.max_perros');

        $entrada = new \DateTime($entrada);
        $salida = new \DateTime($salida);

        //para saber que salida es posterior a entrada
        if ($salida <= $entrada) {
            return false;
        }

        //recorrer cada dia del rango solicitado (desde fecha_entrada hasta el día ANTERIOR a fecha_salida)
        //importante! clone evita que al modificar la fecha del bucle se modifique tambien la fecha original
        $fecha = clone $entrada;

        while ($fecha < $salida) {

            //solo cuentan las reservas que esten confirmadas y activas, no canceladas o pendientes
            $query = self::estanciasActivas();

            //si se esta editando una estancia existente (ej: ampliando fechas), ignorar esa misma estancia para no contarla dos veces y sea erroneo
            //!== para que no de fallos
            if ($ignorarEstanciaId !== null) {
                $query->where('id', '!=', $ignorarEstanciaId);
            }

            $ocupadas = $query
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
        //convertir las fechas para comparar
        $salidaActual = new \DateTime($this->fecha_salida);
        $nuevaSalida = new \DateTime($nuevaSalida);

        //acortar estancia siempre es posible
        if ($nuevaSalida <= $salidaActual) {
            return true;
        }

        //comprobar disponibilidad solo en los dias extra
        return self::hayDisponibilidad(
            $salidaActual->format('Y-m-d'),
            $nuevaSalida->format('Y-m-d'),
            $this->id
        );
    }

    //confirma la estancia (si hay disponibilidad)
    public function confirmar()
    {

        $entrada = new \DateTime($this->fecha_entrada);
        $salida = new \DateTime($this->fecha_salida);

        //para saber que salida es posterior a entrada
        if ($salida <= $entrada) {
            return false;
        }

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
        if ($this->estado != 'confirmada') {
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

