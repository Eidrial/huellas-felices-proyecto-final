<?php

namespace App\Http\Controllers;

use App\Models\Cuidado;
use App\Models\Estancia;
use App\Models\Aviso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CuidadosController extends Controller
{

    //tarea atrasada si su fecha es anterior a hoy o es hoy pero su hora programada ha pasado
    private function esAtrasada($fecha, $hora, $hoy, $ahoraHora)
    {
        //si es de un dia anterior a hoy = tarea atrasada
        if ($fecha < $hoy) {
            return true;
        }

        //si es hoy con horAa y esa hora es menor que la hora actual = tarea atrasada
        if ($fecha == $hoy && $hora && $hora < $ahoraHora) {
            return true;
        }

        //en cualquier otro caso = tarea no atrasada
        return false;
    }

    //se puede marcar si NO es futura (con margen de 15 min para tareas de hoy con hora)
    private function sePuedeMarcar($fecha, $hora, $hoy, $ahoraMas15)
    {
        //si es de un dia posterior a hoy = tarea futura (NO se puede marcar)
        if ($fecha > $hoy)
            return false;

        //si es hoy con hora, solo se puede marcar a partir de 15 min antes
        if ($fecha == $hoy && $hora && $hora > $ahoraMas15) {
            return false;
        }

        //en cualquier otro caso = se puede marcar
        return true;
    }

    //panel principal del cuidados
    public function index()
    {
        //dia actual
        $hoy = now()->toDateString();
        //hora actual
        $ahoraHora = now()->format('H:i:s');
        $manana = now()->addDay()->toDateString();

        //entradas de mascotas mañana (verificando que el estado sea confirmada)
        $entradasManana = Estancia::where('estado', 'confirmada')
            ->where('fecha_entrada', $manana)
            ->with('mascota.dueno')
            ->orderBy('fecha_entrada')
            ->get();

        //salidas de mascotas mañana (verificando que el estado sea activa)
        $salidasManana = Estancia::where('estado', 'activa')
            ->where('fecha_salida', $manana)
            ->with('mascota.dueno')
            ->orderBy('fecha_salida')
            ->get();

        //contadores de entradas y salidas
        $totalEntradasManana = $entradasManana->count();
        $totalSalidasManana = $salidasManana->count();

        //estancias confirmadas o activas
        $estancias = Estancia::estanciasActivas()
            ->with('mascota.dueno')
            ->orderBy('fecha_entrada')
            ->get();

        //si no hay estancias registradas, devolver la vista con datos vacios
        if ($estancias->isEmpty()) {
            $resumen = [];
            return view('cuidados.index', compact(
                'estancias',
                'resumen',
                'hoy',
                'entradasManana',
                'salidasManana',
                'totalEntradasManana',
                'totalSalidasManana'
            ));
        }

        //obtener los ID de las estancias para usarlos en la consulta de cuidados
        $idsEstancias = $estancias->pluck('id');

        $cuidados = Cuidado::whereIn('estancia_id', $idsEstancias)
            ->pendiente()
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get()
            ->groupBy('estancia_id'); //organizar los cuidados por estancia

        $resumen = [];

        foreach ($estancias as $estancia) {

            //lista de cuidados pendientes de ESTA estancia
            $lista = $cuidados->get($estancia->id, collect());

            //tareas basicas (NO extras)
            $tareas = $lista->where('tipo', '!=', 'extra');

            $atrasadas = 0;
            $hoyCount = 0;
            $proximas = 0;

            //proxima tarea pendiente (la primera NO atrasada)
            $proxima = null;

            foreach ($tareas as $tarea) {

                $esAtrasada = $this->esAtrasada($tarea->fecha, $tarea->hora, $hoy, $ahoraHora);

                //contadores
                if ($esAtrasada) {
                    $atrasadas++;
                } elseif ($tarea->fecha == $hoy) {
                    $hoyCount++;
                } else {
                    $proximas++;
                }

                //si todavia no se ha guardado ninguna tarea como proxima y la tarea NO esta atrasada, sera la prox
                if ($proxima === null && !$esAtrasada) {
                    $proxima = $tarea;
                }
            }

            //extras solo de hoy
            $extrasHoy = $lista->where('tipo', 'extra')->where('fecha', $hoy)->count();

            //guardar el resumen de esta estancia
            $resumen[$estancia->id] = [
                'pendientesAtrasadas' => $atrasadas,
                'pendientesHoy' => $hoyCount,
                'pendientesProximas' => $proximas,
                'extrasHoy' => $extrasHoy,
                'proxima' => $proxima,
            ];
        }

        return view('cuidados.index', compact(
            'estancias',
            'resumen',
            'hoy',
            'entradasManana',
            'salidasManana',
            'totalEntradasManana',
            'totalSalidasManana'
        ));
    }

    //agrupar lista por fecha
    private function agruparPorDia($lista)
    {
        return collect($lista)->groupBy('fecha');
    }

    //detalle de estancia
    public function show(Estancia $estancia)
    {
        //solo se ve en el panel si esta confirmada o activa
        if ($estancia->estado != 'confirmada' && $estancia->estado != 'activa') {
            return redirect()->route('cuidados.index')->with('error', 'No puedes ver esta estancia.');
        }

        //cargar relaciones necesarias para mostrar datos en la vista
        $estancia->load('mascota.dueno');

        //fecha y hora actual
        $hoy = now()->toDateString();
        $ahoraHora = now()->format('H:i:s');
        //hora actual + 15 minutos
        $ahoraMas15 = now()->addMinutes(15)->format('H:i:s');

        //filtro: hoy - atrasadas - todos - realizados (por defecto = hoy)
        $filtro = request('filtro', 'hoy');

        //inicializar todo para no tener fallos
        $atrasadas = [];
        $pendientesHoy = [];

        //solo para el filtro todos
        $todasAtrasadas = [];
        $todasHoy = [];
        $todasFuturas = [];

        //solo para el filtro realizados (agrupado por dia)
        $realizadosPorDia = [];
        $totalRealizados = 0;

        //AGRUPADOS POR DIA
        $atrasadasPorDia = [];
        $pendientesHoyPorDia = [];
        $todasAtrasadasPorDia = [];
        $todasHoyPorDia = [];
        $todasFuturasPorDia = [];

        //REALIZADOS (completados)
        if ($filtro == 'realizados') {

            //todos los completados de esta estancia
            $realizados = Cuidado::where('estancia_id', $estancia->id)
                ->where('completado', true)
                ->with('usuario')
                ->orderByDesc('fecha') //ordena por fecha mas reciente arriba
                ->orderBy('hora')
                ->get();

            //total realizados (para mostrar en la vista)
            $totalRealizados = $realizados->count();

            //agrupar por fecha
            $realizadosPorDia = $this->agruparPorDia($realizados);

        } else {

            //PENDIENTES (hoy/atrasadas/todos)

            //pendientes de esta estancia (no extras)
            $pendientes = Cuidado::where('estancia_id', $estancia->id)
                ->pendiente()
                ->where('tipo', '!=', 'extra')
                ->with('usuario')
                ->orderBy('fecha')
                ->orderBy('hora');

            //si NO es todos, solo mostrar hasta hoy (hoy + anteriores)
            if ($filtro != 'todos') {
                $pendientes->where('fecha', '<=', $hoy);
            }

            $lista = $pendientes->get();

            foreach ($lista as $cuidado) {

                //saber si la tarea esta atrasada
                $esAtrasada = $this->esAtrasada($cuidado->fecha, $cuidado->hora, $hoy, $ahoraHora);

                if ($filtro == 'todos') {
                    //si el filtro es todos, separar en atrasadas - hoy - futuras
                    if ($esAtrasada) {
                        $todasAtrasadas[] = $cuidado;
                    } elseif ($cuidado->fecha == $hoy) {
                        $todasHoy[] = $cuidado;
                    } else {
                        $todasFuturas[] = $cuidado;
                    }
                } else {
                    //si el filtro es hoy o atrasadas, separar en atrasadas - hoy
                    if ($esAtrasada) {
                        $atrasadas[] = $cuidado;
                    } elseif ($cuidado->fecha == $hoy) {
                        $pendientesHoy[] = $cuidado;
                    }
                }
            }

            //aplicar filtro
            if ($filtro == 'hoy') {
                $atrasadas = [];
            } elseif ($filtro == 'atrasadas') {
                $pendientesHoy = [];
            }

            //agrupar por fecha para mostrar por dias en la vista
            $atrasadasPorDia = $this->agruparPorDia($atrasadas);
            $pendientesHoyPorDia = $this->agruparPorDia($pendientesHoy);

            $todasAtrasadasPorDia = $this->agruparPorDia($todasAtrasadas);
            $todasHoyPorDia = $this->agruparPorDia($todasHoy);
            $todasFuturasPorDia = $this->agruparPorDia($todasFuturas);
        }

        //avisos de la estancia
        //ultimos 10 
        $avisos = Aviso::where('estancia_id', $estancia->id)
            ->with('usuario')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('cuidados.show', compact(
            'estancia',
            'hoy',
            'filtro',

            //para los contadores de la vista
            'atrasadas',
            'pendientesHoy',
            'todasAtrasadas',
            'todasHoy',
            'todasFuturas',

            //agrupados por dia
            'atrasadasPorDia',
            'pendientesHoyPorDia',
            'todasAtrasadasPorDia',
            'todasHoyPorDia',
            'todasFuturasPorDia',

            'ahoraHora',
            'ahoraMas15',
            'realizadosPorDia',
            'totalRealizados',

            //avisos
            'avisos'
        ));
    }

    //crear cuidado extra
    public function store(Request $request)
    {
        //SOLO extra
        $request->validate([
            'estancia_id' => 'required|exists:estancias,id',
            'tipo' => 'required|in:extra',
            'hora' => 'required|date_format:H:i',
            'descripcion' => 'required|string|max:255',
            'precio_extra' => 'required|numeric|min:0',
        ]);

        $estancia = Estancia::find($request->estancia_id);

        //solo si la estancia esta activa
        if (!$estancia || $estancia->estado != 'activa') {
            return back()->with('error', 'No puedes añadir extras a una estancia no activa.');
        }

        //si ya ha pasado la fecha de salida, no permitir añadir extras
        $hoy = now()->toDateString();

        if ($hoy > $estancia->fecha_salida) {
            return back()->with('error', 'No puedes añadir extras en una estancia cuya fecha de salida ya ha pasado.');
        }

        Cuidado::create([
            'estancia_id' => $request->estancia_id,
            'tipo' => 'extra',
            'fecha' => now()->toDateString(),
            'hora' => $request->hora,
            'descripcion' => $request->descripcion,
            'precio_extra' => $request->precio_extra,
            'user_id' => Auth::id(),
            'completado' => true,
        ]);

        return back()->with('success', 'Extra añadido correctamente.');
    }

    //marcar cuidado como completado
    public function completar(Cuidado $cuidado)
    {
        //solo si la estancia esta activa
        if ($cuidado->estancia->estado != 'activa') {
            return back()->with('error', 'No puedes completar cuidados de una estancia no activa.');
        }

        //evitar volver a completar lo mismo
        if ($cuidado->completado) {
            return back()->with('error', 'Este cuidado ya estaba marcado como realizado.');
        }

        $hoy = now()->toDateString();
        $ahoraMas15 = now()->addMinutes(15)->format('H:i:s');

        //si ya ha pasado la fecha de salida, no permitir marcar cuidados
        if ($hoy > $cuidado->estancia->fecha_salida) {
            return back()->with('error', 'No puedes marcar cuidados en una estancia cuya fecha de salida ya ha pasado.');
        }

        //si no se puede marcar aun, error
        if (!$this->sePuedeMarcar($cuidado->fecha, $cuidado->hora, $hoy, $ahoraMas15)) {
            return back()->with('error', 'No puedes marcar un cuidado antes de su hora/fecha (margen 15 min).');
        }

        //marcar como completado
        $cuidado->update([
            'completado' => true,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Cuidado marcado como realizado');
    }

    //borrar extras (por equivocacion o fallo)
    public function borrarExtra(Cuidado $cuidado)
    {
        if (Auth::user()->role != 'admin') {
            return back()->with('error', 'Solo un administrador puede borrar extras.');
        }

        if ($cuidado->estancia->estado != 'activa') {
            return back()->with('error', 'No puedes borrar extras de una estancia no activa.');
        }

        //si ya ha pasado la fecha de salida, no permitir borrar extras
        $hoy = now()->toDateString();

        if ($hoy > $cuidado->estancia->fecha_salida) {
            return back()->with('error', 'No puedes borrar extras en una estancia cuya fecha de salida ya ha pasado.');
        }

        if ($cuidado->tipo != 'extra') {
            return back()->with('error', 'Solo se pueden borrar cuidados de tipo extra.');
        }

        $cuidado->delete();

        return back()->with('success', 'Extra eliminado correctamente.');
    }

}
