<?php

namespace App\Http\Controllers;

use App\Models\Cuidado;
use App\Models\Estancia;
use App\Models\Aviso;
use App\Models\Mascota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CuidadosController extends Controller
{

    //panel principal del cuidados
    public function index()
    {
        //cancelar automaticamente pendientes y sin disponibilidad caducadas sin cobrar
        Estancia::cancelarCaducadasSinCobro();

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
            ->pendientes()
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

                $esAtrasada = $tarea->esAtrasado($hoy, $ahoraHora);

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

    //listado de estancias para cuidador
    public function estancias(Request $request)
    {
        //cancelar automaticamente pendientes y sin disponibilidad caducadas sin cobrar
        Estancia::cancelarCaducadasSinCobro();

        $vista = $request->get('vista', 'abiertas');

        //totales para las pestañas
        $totalAbiertas = Estancia::whereIn('estado', [
            'pendiente',
            'confirmada',
            'activa',
            'sin_disponibilidad'
        ])->count();

        $totalHistorial = Estancia::whereIn('estado', [
            'finalizada',
            'cancelada'
        ])->count();

        //consulta base con mascota y dueño
        $consulta = Estancia::with('mascota.dueno');

        //filtrar segun la pestaña
        if ($vista === 'historial') {
            $consulta->whereIn('estado', ['finalizada', 'cancelada']);
        } else {
            $consulta->whereIn('estado', [
                'pendiente',
                'confirmada',
                'activa',
                'sin_disponibilidad'
            ]);
        }

        //ordenar y paginar solo las estancias de esa pestaña (cuidadores)
        $estancias = $consulta
            ->orderByRaw("FIELD(estado, 'activa', 'confirmada', 'pendiente', 'sin_disponibilidad', 'finalizada', 'cancelada')")
            ->orderBy('fecha_entrada', 'asc')
            ->paginate(6)
            ->appends(['vista' => $vista]);

        return view('cuidados.estancias', compact(
            'estancias',
            'vista',
            'totalAbiertas',
            'totalHistorial'
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
        if (!$estancia->esConfirmada() && !$estancia->esActiva()) {
            return redirect()->route('cuidados.index')->with('error', 'No puedes ver esta estancia.');
        }

        //cargar relaciones necesarias para mostrar datos en la vista
        $estancia->load('mascota.dueno');

        //fecha y hora actual
        $hoy = now()->toDateString();
        $ahoraHora = now()->format('H:i:s');
        //hora actual + 15 minutos
        $ahoraMas15 = now()->addMinutes(15)->format('H:i:s');

        //filtro: hoy - atrasadas - realizados (por defecto = hoy)
        $filtro = request('filtro', 'hoy');

        //evitar filtros que ya no existen
        if (!in_array($filtro, ['hoy', 'atrasadas', 'realizados'])) {
            $filtro = 'hoy';
        }

        //inicializar todo para no tener fallos
        $atrasadas = [];
        $pendientesHoy = [];

        //contador total de atrasadas
        $totalAtrasadas = 0;

        //solo para el filtro realizados (agrupado por dia)
        $realizados = null;
        $realizadosPorDia = [];
        $totalRealizados = 0;

        //AGRUPADOS POR DIA
        $atrasadasPorDia = [];
        $pendientesHoyPorDia = [];

        //cuidados pendientes hasta hoy para calcular atrasadas y pendientes de hoy
        $cuidadosPendientesHastaHoy = Cuidado::where('estancia_id', $estancia->id)
            ->pendientesBase()
            ->with('usuario')
            ->where('fecha', '<=', $hoy)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        //calcular contador total de atrasadas
        foreach ($cuidadosPendientesHastaHoy as $cuidado) {
            if ($cuidado->esAtrasado($hoy, $ahoraHora)) {
                $totalAtrasadas++;
            }
        }

        //REALIZADOS (completados)
        if ($filtro == 'realizados') {

            $realizados = Cuidado::where('estancia_id', $estancia->id)
                ->realizados()
                ->with('usuario')
                ->orderByDesc('fecha')
                ->orderByDesc('hora')
                ->paginate(4, pageName: 'realizados_page')
                ->withQueryString(); //mantiene los parametros actuales de la url al cambiar de pagg

            $totalRealizados = Cuidado::where('estancia_id', $estancia->id)
                ->realizados()
                ->count();

            $realizadosPorDia = $this->agruparPorDia($realizados->getCollection());

        } else {

            //PENDIENTES (hoy/atrasadas)
            $lista = $cuidadosPendientesHastaHoy;

            foreach ($lista as $cuidado) {

                //saber si la tarea esta atrasada
                $esAtrasada = $cuidado->esAtrasado($hoy, $ahoraHora);

                //si es atrasada, va en atrasadas
                if ($esAtrasada) {
                    $atrasadas[] = $cuidado;
                }

                //si es de hoy, va en hoy (aunque tambien este atrasada)
                if ($cuidado->fecha == $hoy) {
                    $pendientesHoy[] = $cuidado;
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
        }

        //extras siempre, independientemente del filtro
        $extras = Cuidado::where('estancia_id', $estancia->id)
            ->extras()
            ->realizados()
            ->with('usuario')
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(4, pageName: 'extras_page')
            ->withQueryString();

        //avisos de la estancia
        $avisos = Aviso::where('estancia_id', $estancia->id)
            ->with('usuario')
            ->orderByDesc('created_at')
            ->paginate(4, pageName: 'avisos_page')
            ->withQueryString();

        $filtrosVisuales = Cuidado::getFiltrosVisuales();

        return view('cuidados.show', compact(
            'estancia',
            'hoy',
            'filtro',
            'filtrosVisuales',

            //para los contadores de la vista
            'atrasadas',
            'pendientesHoy',
            'totalAtrasadas',

            //agrupados por dia
            'atrasadasPorDia',
            'pendientesHoyPorDia',

            'ahoraHora',
            'ahoraMas15',
            'realizados',
            'realizadosPorDia',
            'totalRealizados',

            'extras',
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
        if (!$estancia || !$estancia->esActiva()) {
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
        if (!$cuidado->estancia->esActiva()) {
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
        if (!$cuidado->sePuedeMarcar($hoy, $ahoraMas15)) {
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

        if (!$cuidado->estancia->esActiva()) {
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

    //listado de mascotas para cuidador
    public function mascotas()
    {
        $mascotas = Mascota::with(['dueno', 'estancias'])
            ->orderByDesc('created_at')
            ->paginate(6);

        return view('admin.mascotas', compact('mascotas'));
    }

    //ver ficha de mascota para cuidador
    public function showMascota(Mascota $mascota)
    {
        $mascota->load('dueno', 'estancias');

        return view('cuidados.show-mascota', compact('mascota'));
    }

}