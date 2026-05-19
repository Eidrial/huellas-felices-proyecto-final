<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mascota;
use App\Models\Cuidado;
use App\Models\Aviso;

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
        'medicacion_descripcion',
        'medicacion_horas',
    ];

    protected $dates = [
        'fecha_entrada',
        'fecha_salida',
    ];

    //relacion con avisos de la estancia
    public function avisos()
    {
        return $this->hasMany(Aviso::class);
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function cuidados()
    {
        return $this->hasMany(Cuidado::class);
    }

    //estancias activas (confirmadas o en curso)
    public function scopeEstanciasActivas($consulta)
    {
        return $consulta->whereIn('estado', ['confirmada', 'activa']);
    }

    //estancias pendientes
    public function scopeEstanciasPendientes($consulta)
    {
        return $consulta->where('estado', 'pendiente');
    }

    //si estan los 20 cupos ocupados
    public function esSinDisponibilidad()
    {
        return $this->estado === 'sin_disponibilidad';
    }

    //FUNCIONES AUXILIARES

    //PRECIOS

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

    //calcular total hasta hoy (para cancelaciones)
    public function calcularTotalHastaHoy()
    {
        $entrada = new \DateTime($this->fecha_entrada);
        $manana = new \DateTime(date('Y-m-d', strtotime('+1 day')));

        $dias = $entrada->diff($manana)->days;

        return round($dias * $this->precio_dia, 2);
    }

    //aplicar cancelacion sin cobro
    public function aplicarCancelacionSinCobro()
    {
        $this->precio_total = 0;
        $this->save();
    }

    //cancela la estancia sin cobrar nada (para usar en estancias no confirmadas o en sin disponibilidad)
    public function cancelarSinCobro($quien = 'admin')
    {
        $this->precio_total = 0;
        $this->estado = 'cancelada';
        $this->cancelada_por = $quien;
        $this->save();
    }

    //cancelar automaticamente estancias caducadas sin cobrar
    public static function cancelarCaducadasSinCobro()
    {
        $estancias = self::whereIn('estado', ['pendiente', 'sin_disponibilidad'])
            ->whereDate('fecha_entrada', '<=', date('Y-m-d'))
            ->get();

        foreach ($estancias as $estancia) {
            $estancia->cancelarSinCobro('admin');
        }
    }

    //aplicar cancelacion cobrando 1 dia
    public function aplicarCancelacionUnDia()
    {
        $manana = date('Y-m-d', strtotime('+1 day'));

        $this->fecha_salida = $manana;
        $this->precio_total = $this->precio_dia;
        $this->save();
    }

    //aplicar cancelacion de estancia activa cobrando hasta hoy incluido
    public function aplicarCancelacionActiva()
    {
        $manana = date('Y-m-d', strtotime('+1 day'));

        $this->fecha_salida = $manana;
        $this->calcularPrecioTotal();
        $this->save();
    }

    //calcular total incluyendo extras
    public function totalConExtras()
    {
        $extras = $this->cuidados()->where('tipo', 'extra')->sum('precio_extra');

        return ($this->precio_total ?? 0) + $extras;
    }

    //validar si la fecha de entrada es superior o igual a mañana (T+1)
    public static function fechaValida($fecha)
    {
        return strtotime($fecha) >= strtotime('tomorrow');
    }

    //FECHAS

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
            $consulta = self::estanciasActivas();

            //si se esta editando una estancia existente (ej: ampliando fechas), ignorar esa misma estancia para no contarla dos veces y sea erroneo
            //!== para que no de fallos
            if ($ignorarEstanciaId !== null) {
                $consulta->where('id', '!=', $ignorarEstanciaId);
            }

            $ocupadas = $consulta
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


    //OPCIONES ESTANCIA

    //inicia la estancia (el perro entra en la residencia)
    public function iniciar()
    {
        if ($this->estado != 'confirmada') {
            return false;
        }

        $this->estado = 'activa';
        $this->save();

        $this->generarCuidadosBase();

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

    //FACTURA ESTANCIA

    //dias de estancia reales
    public function diasFacturados()
    {
        //si se ha cancelado el mismo dia de entrada, se devuelve un dia
        if ($this->esCancelacionUnDia()) {
            return 1;
        }

        $entrada = new \DateTime($this->fecha_entrada);
        $salida = new \DateTime($this->fecha_salida);

        //calcular los dias reales entre entrada y salida
        //la fecha de salida no cuenta como dia de estancia
        return $entrada->diff($salida)->days;
    }

    //saber si se ha cancelado el mismo dia de entrada
    public function esCancelacionUnDia()
    {
        return $this->estado == 'cancelada' && $this->precio_total == $this->precio_dia;
    }


    //CUIDADOS BASE
    public function generarCuidadosBase()
    {
        //evitar duplicados: si ya existen cuidados BASE (no extras), no generar otra vez
        $primerDia = (new \DateTime($this->fecha_entrada))->format('Y-m-d');

        if ($this->cuidados()->where('tipo', '!=', 'extra')->where('fecha', $primerDia)->exists()) {
            return;
        }

        $entrada = new \DateTime($this->fecha_entrada);
        $salida = new \DateTime($this->fecha_salida);

        //recorrer cada dia desde entrada hasta el dia anterior a salida
        while ($entrada < $salida) {

            $fechaActual = $entrada->format('Y-m-d');

            //paseo mañana
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'paseo',
                'fecha' => $fechaActual,
                'hora' => '09:00',
                'descripcion' => 'Paseo de la mañana',
                'completado' => false,
            ]);

            //desayuno
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'alimentacion',
                'fecha' => $fechaActual,
                'hora' => '10:00',
                'descripcion' => 'Desayuno',
                'completado' => false,
            ]);

            //paseo mediodia
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'paseo',
                'fecha' => $fechaActual,
                'hora' => '14:00',
                'descripcion' => 'Paseo del mediodía',
                'completado' => false,
            ]);

            //juego / socializacion
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'juego',
                'fecha' => $fechaActual,
                'hora' => '17:00',
                'descripcion' => 'Juego y socialización supervisada',
                'completado' => false,
            ]);

            //paseo tarde
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'paseo',
                'fecha' => $fechaActual,
                'hora' => '19:00',
                'descripcion' => 'Paseo de la tarde',
                'completado' => false,
            ]);

            //cena
            Cuidado::create([
                'estancia_id' => $this->id,
                'tipo' => 'alimentacion',
                'fecha' => $fechaActual,
                'hora' => '20:00',
                'descripcion' => 'Cena',
                'completado' => false,
            ]);

            //medicacion (si existe)
            if ($this->medicacion_descripcion && $this->medicacion_horas) {

                $horas = explode(',', $this->medicacion_horas);

                foreach ($horas as $hora) {
                    $hora = trim($hora);

                    if ($hora !== '') {
                        $horaNorm = date('H:i', strtotime($hora));

                        Cuidado::create([
                            'estancia_id' => $this->id,
                            'tipo' => 'medicacion',
                            'fecha' => $fechaActual,
                            'hora' => $horaNorm,
                            'descripcion' => $this->medicacion_descripcion,
                            'completado' => false,
                        ]);
                    }
                }
            }

            //pasar al siguiente dia
            $entrada->modify('+1 day');
        }
    }

    //PARA USAR EN VISTAS
    //ESTANCIAS USUARIO
    //colores y texto segun el estado de la estancia
    public function getEstadoVisual()
    {
        $config = [
            'pendiente' => [
                'punto' => 'bg-[#c9821a]',
                'etiqueta' => 'text-[#7a4e10]',
                'texto' => 'Pendiente',
                'barra' => 'bg-[#c9821a]',
            ],
            'confirmada' => [
                'punto' => 'bg-[#5a9e47]',
                'etiqueta' => 'text-[#2d5a27]',
                'texto' => 'Confirmada',
                'barra' => 'bg-[#5a9e47]',
            ],
            'activa' => [
                'punto' => 'bg-[#3a7abf]',
                'etiqueta' => 'text-[#1a4f8a]',
                'texto' => 'Activa',
                'barra' => 'bg-[#3a7abf]',
            ],
            'finalizada' => [
                'punto' => 'bg-[#8a8e84]',
                'etiqueta' => 'text-[#8a8e84]',
                'texto' => 'Finalizada',
                'barra' => 'bg-[#d9ddd0]',
            ],
            'cancelada' => [
                'punto' => 'bg-[#c9342e]',
                'etiqueta' => 'text-[#9b2a2a]',
                'texto' => 'Cancelada',
                'barra' => 'bg-[#c9342e]',
            ],
            'sin_disponibilidad' => [
                'punto' => 'bg-[#c9342e]',
                'etiqueta' => 'text-[#9b2a2a]',
                'texto' => 'Sin disponibilidad',
                'barra' => 'bg-[#c9342e]',
            ],
        ];

        return $config[$this->estado] ?? [
            'punto' => 'bg-[#8a8e84]',
            'etiqueta' => 'text-[#8a8e84]',
            'texto' => ucfirst($this->estado),
            'barra' => 'bg-[#d9ddd0]',
        ];
    }

    //comprobar estados
    public function esPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function esConfirmada()
    {
        return $this->estado === 'confirmada';
    }

    public function esActiva()
    {
        return $this->estado === 'activa';
    }

    public function esFinalizada()
    {
        return $this->estado === 'finalizada';
    }

    public function esCancelada()
    {
        return $this->estado === 'cancelada';
    }

    //calcular dias
    //dias que faltan para la entrada
    public function diasParaEntrada()
    {
        $hoy = strtotime(date('Y-m-d'));
        $entrada = strtotime($this->fecha_entrada);

        return floor(($entrada - $hoy) / 86400);
    }

    //dias que lleva activa desde la entrada
    public function diasActiva()
    {
        $entrada = strtotime($this->fecha_entrada);
        $hoy = strtotime(date('Y-m-d'));

        return floor(($hoy - $entrada) / 86400);
    }

    //saber si la estancia empieza hoy
    public function entraHoy()
    {
        return $this->fecha_entrada == date('Y-m-d');
    }

    public function fechaSalidaVisible()
    {
        $hoy = date('Y-m-d');

        // si la salida es posterior a hoy, significa que se ha forzado (+1 día)
        if (($this->esCancelada() || $this->esFinalizada()) && $this->fecha_salida > $hoy) {
            return date('Y-m-d', strtotime($this->fecha_salida . ' -1 day'));
        }

        return $this->fecha_salida;
    }

    //mensaje modal
    public function mensajeCancelacion()
    {
        if ($this->esPendiente() || $this->esSinDisponibilidad()) {
            return 'Esta estancia se cancelará sin ningún coste. ¿Continuar?';
        }

        if ($this->esActiva()) {

            $total = $this->calcularTotalHastaHoy();
            $totalFormateado = number_format($total, 2);

            return "Vas a cancelar una estancia activa. Se cobrará solo el tiempo que el perro ha estado en la residencia ({$totalFormateado} €). ¿Continuar?";
        }

        if ($this->esConfirmada() && $this->entraHoy()) {
            $precioDia = number_format($this->precio_dia, 2);
            return "Vas a cancelar el mismo día de entrada. Se cobrará 1 día igualmente ({$precioDia} €). ¿Continuar?";
        }

        return '¿Seguro que quieres cancelar esta estancia?';
    }

    //ESTANCIAS ADMIN
    //avisos para el panel de administracion
    public function getAvisosAdmin()
    {
        $avisos = [];

        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));

        $entrada = date('Y-m-d', strtotime($this->fecha_entrada));
        $salida = date('Y-m-d', strtotime($this->fecha_salida));

        //PENDIENTE
        if ($this->mascota && $this->mascota->aprobado === null) {
            $avisos[] = [
                'texto' => 'Pendiente aprobación',
                'clase' => 'bg-[#fef8ec] text-[#7a4e10] border border-[#e4c57a]',
            ];
        }

        //PENDIENTE URGENTE
        if ($this->estado === 'pendiente' && $entrada === $manana) {
            $avisos[] = [
                'texto' => 'PENDIENTE URGENTE',
                'clase' => 'bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]',
            ];
        }

        //HOY ENTRA
        if ($this->estado === 'confirmada' && $entrada === $hoy) {
            $avisos[] = [
                'texto' => 'Hoy entra',
                'clase' => 'bg-[#e6f0fb] text-[#1a4f8a] border border-[#b0cef0]',
            ];
        }

        //MAÑANA ENTRA
        if ($this->estado === 'confirmada' && $entrada === $manana) {
            $avisos[] = [
                'texto' => 'Mañana entra',
                'clase' => 'bg-[#fef8ec] text-[#7a4e10] border border-[#e4c57a]',
            ];
        }

        //YA DENTRO
        if ($this->estado === 'activa' && $hoy < $salida) {
            $avisos[] = [
                'texto' => 'Ya dentro',
                'clase' => 'bg-[#e6f0fb] text-[#1a4f8a] border border-[#b0cef0]',
            ];
        }

        //HOY SALE
        if ($this->estado === 'activa' && $salida === $hoy) {
            $avisos[] = [
                'texto' => 'Hoy sale',
                'clase' => 'bg-[#fef3e6] text-[#7a3a10] border border-[#e4b47a]',
            ];
        }

        //MAÑANA SALE
        if ($this->estado === 'activa' && $salida === $manana) {
            $avisos[] = [
                'texto' => 'Mañana sale',
                'clase' => 'bg-[#eef5e8] text-[#2d5a27] border border-[#c8d9be]',
            ];
        }

        //SALIDA PENDIENTE
        if ($this->estado === 'activa' && $hoy > $salida) {
            $avisos[] = [
                'texto' => 'Salida pendiente',
                'clase' => 'bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]',
            ];
        }

        return $avisos;
    }

    //saber si una estancia confirmada ya se puede iniciar
    public function pendienteIniciar()
    {
        return $this->estado == 'confirmada' && date('Y-m-d') >= $this->fecha_entrada;
    }

    //saber si una estancia activa ya se puede finalizar
    public function pendienteFinalizar()
    {
        return $this->estado == 'activa' && date('Y-m-d') >= $this->fecha_salida;
    }

    //mensaje del modal para confirmar una estancia
    public function mensajeConfirmacionAdmin()
    {
        $hoy = date('Y-m-d');

        if ($hoy > $this->fecha_entrada && $this->mascota && $this->mascota->aprobado === null) {
            return 'La fecha de entrada ya ha pasado y esta mascota todavía está pendiente de aprobación. Si confirmas, la mascota se aprobará automáticamente. ¿Continuar?';
        }

        if ($hoy > $this->fecha_entrada) {
            return 'La fecha de entrada ya ha pasado. Si el animal ya ha llegado, recuerda iniciar la estancia después de confirmarla.';
        }

        if ($this->mascota && $this->mascota->aprobado === null) {
            return 'Esta mascota todavía está pendiente de aprobación. Si confirmas, se aprobará automáticamente. ¿Continuar?';
        }

        return '¿Seguro que quieres confirmar esta estancia?';
    }

    //mensaje del modal para finalizar una estancia
    public function mensajeFinalizacionAdmin()
    {
        if ($this->estado == 'activa' && date('Y-m-d') < $this->fecha_salida) {
            $total = $this->calcularTotalHastaHoy();
            $totalFormateado = number_format($total, 2);

            return "Vas a finalizar la estancia antes de la fecha prevista. 
        Se ajustará la fecha de salida real a hoy y el precio final será de {$totalFormateado} €. ¿Continuar?";
        }

        return '¿Seguro que quieres finalizar esta estancia? (El perro sale de la residencia)';
    }

    //CUIDADOS ESTANCIAS ADMIN
    //resumen para el panel de cuidados
    public function getResumenCuidados($resumen)
    {
        $r = $resumen[$this->id] ?? [
            'pendientesAtrasadas' => 0,
            'pendientesHoy' => 0,
            'pendientesProximas' => 0,
            'extrasHoy' => 0,
            'proxima' => null,
        ];

        $sinTareas = $r['pendientesHoy'] == 0 && $r['pendientesAtrasadas'] == 0 && $r['pendientesProximas'] == 0;

        //color barra superior segun prioridad
        //rojo = hay atrasadas, azul = hay tareas hoy, verde = no hay urgentes
        if ($r['pendientesAtrasadas'] > 0) {
            $barra = 'bg-[#c9342e]';
        } elseif ($r['pendientesHoy'] > 0) {
            $barra = 'bg-[#3a7abf]';
        } else {
            $barra = 'bg-[#5a9e47]';
        }

        return [
            'data' => $r,
            'sinTareas' => $sinTareas,
            'barra' => $barra,
        ];
    }
}

