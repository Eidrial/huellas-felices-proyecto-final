<?php

namespace App\Http\Controllers;

use App\Models\Estancia;
use App\Models\Mascota;
use App\Models\Cuidado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstanciaConfirmadaMail;

class EstanciaController extends Controller
{
    //listado de estancias del usuario logueado
    public function index(Request $request)
    {
        //cancelar automaticamente pendientes y sin disponibilidad caducadas sin cobrar
        Estancia::cancelarCaducadasSinCobro();

        $vista = $request->get('vista', 'abiertas');

        //totales para las pestañas
        $totalAbiertas = Auth::user()->estancias()
            ->whereIn('estado', [
                'pendiente',
                'confirmada',
                'activa',
                'sin_disponibilidad'
            ])
            ->count();

        $totalHistorial = Auth::user()->estancias()
            ->whereIn('estado', [
                'finalizada',
                'cancelada'
            ])
            ->count();

        //consulta base con mascota
        $consulta = Auth::user()->estancias()->with('mascota');

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

        //ordenar y paginar solo las estancias de esa pestaña (usuario)
        $estancias = $consulta
            ->orderBy('fecha_entrada', 'desc')
            ->paginate(6)
            ->appends(['vista' => $vista]);

        $pendientesHoy = [];

        $hoy = date('Y-m-d');

        //filtrar solo estancias activas para calcular cuidados pendientes de hoy
        $estanciasActivas = $estancias->getCollection()->where('estado', 'activa');

        foreach ($estanciasActivas as $estancia) {

            $pendientesHoy[$estancia->id] = Cuidado::where('estancia_id', $estancia->id)
                ->where('completado', false)
                ->where('fecha', $hoy)
                ->where('tipo', '!=', 'extra')
                ->count();
        }

        return view('estancias.index', compact(
            'estancias',
            'pendientesHoy',
            'vista',
            'totalAbiertas',
            'totalHistorial'
        ));
    }

    //formulario para crear nueva estancia
    public function create()
    {
        //incluye pendientes
        $mascotas = Auth::user()->mascotas()->get();

        return view('estancias.create', compact('mascotas'));
    }

    //guardar estancia
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'fecha_entrada' => 'required|date',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'medicacion_descripcion' => 'nullable|string|max:255',
            'medicacion_horas' => 'nullable|string|max:255',
        ]);

        //si hay medicacion, obligatorio poner horas
        if ($request->medicacion_descripcion && (!$request->medicacion_horas || trim($request->medicacion_horas) == '')) {
            return back()->withErrors([
                'medicacion_horas' => 'Si añades medicación, debes indicar las horas de las tomas.'
            ])->withInput(); //devolver al formulario manteniendo los datos que el usuario ya habia escrito
        }

        //comprobar que el usuario tiene sus datos personales completos antes de reservar
        $user = Auth::user();

        if (!$user->apellidos || !$user->dni || !$user->telefono || !$user->direccion) {
            return redirect()->route('profile.edit')
                ->with('error', 'Debes completar tus datos personales antes de reservar una estancia.');
        }

        $mascota = Mascota::find($request->mascota_id);

        if (!$mascota) {
            return back()->with('error', 'Mascota no encontrada.');
        }

        //asegurar que la mascota es del usuario
        if ($mascota->dueno_id != Auth::id()) {
            return back()->with('error', 'No puedes reservar para una mascota que no es tuya.');
        }

        //limite de estancias por mascota
        $maxPendientes = config('residencia.max_estancias_por_mascota');

        //solo contaran para el maximo las pendientes y las confirmadas, las activas no
        $abiertas = Estancia::where('mascota_id', $mascota->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->count();

        if ($abiertas >= $maxPendientes) {
            return back()->with('error', 'Esta mascota ya tiene el máximo de estancias pendientes/confirmadas.');
        }

        //validar T+1
        if (!Estancia::fechaValida($request->fecha_entrada)) {
            return back()->with('error', 'La fecha de entrada debe ser al menos mañana.');
        }

        $entrada = new \DateTime($request->fecha_entrada);
        $salida = new \DateTime($request->fecha_salida);
        $dias = $entrada->diff($salida)->days;

        //no permitir entradas en domingo
        if ($entrada->format('w') == 0) {
            return back()->with('error', 'No se permiten entradas en domingo.');
        }

        //no permitir salidas en domingo
        if ($salida->format('w') == 0) {
            return back()->with('error', 'No se permiten salidas en domingo.');
        }

        //minimo de dias
        if ($dias < config('residencia.min_dias_estancia')) {
            return back()->with('error', 'La estancia mínima es de ' . config('residencia.min_dias_estancia') . ' días.');
        }

        //maximo de dias
        if ($dias > config('residencia.max_dias_estancia')) {
            return back()->with('error', 'La estancia no puede superar los ' . config('residencia.max_dias_estancia') . ' días.');
        }

        //comprobar si coincide con otra estancia de la misma mascota
        $otrasEstancias = Estancia::where('mascota_id', $mascota->id)
            ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
            ->get();

        $hayConflicto = false;

        foreach ($otrasEstancias as $e) {
            $hayConflicto = $hayConflicto || (
                $request->fecha_entrada < $e->fecha_salida &&
                $request->fecha_salida > $e->fecha_entrada
            );
        }

        if ($hayConflicto) {
            return back()->with('error', 'Esta mascota ya tiene otra estancia entre estas fechas.');
        }

        //estado segun aprobacion de mascota y disponibilidad
        if ($mascota->aprobado != 1) {
            $estado = 'pendiente';
        } elseif (!Estancia::hayDisponibilidad($request->fecha_entrada, $request->fecha_salida)) {
            $estado = 'sin_disponibilidad';
        } else {
            $estado = 'confirmada';
        }

        $medicacionDescripcion = trim($request->medicacion_descripcion);
        $medicacionHoras = trim($request->medicacion_horas);

        $estancia = Estancia::create([
            'mascota_id' => $mascota->id,
            'estado' => $estado,
            'fecha_entrada' => $request->fecha_entrada,
            'fecha_salida' => $request->fecha_salida,
            'precio_dia' => config('residencia.precio_dia'),
            'medicacion_descripcion' => $medicacionDescripcion ? $medicacionDescripcion : null,
            'medicacion_horas' => $medicacionHoras ? $medicacionHoras : null,
        ]);

        $estancia->calcularPrecioTotal();
        $estancia->save();

        //si la estancia ha quedado confirmada automaticamente, mandar email
        if ($estancia->estado == 'confirmada') {
            $estancia->load('mascota.dueno');

            $emailDueno = $estancia->mascota->dueno->email ?? null;
            //si hay override en .env, mandar ahi (pruebass)
            $destinatario = config('mail.to_override') ? config('mail.to_override') : $emailDueno;

            if ($destinatario) {
                Mail::to($destinatario)->send(new EstanciaConfirmadaMail($estancia));
            }
        }

        if ($estancia->estado == 'sin_disponibilidad') {
            return redirect()->route('estancias.index')->with('warning', 'Estancia creada, pero no hay plazas disponibles para esas fechas.');
        }

        return redirect()->route('estancias.index')->with('success', 'Estancia creada correctamente. Recuerda que se paga el primer día.');
    }

    //mostrar factura de estancia
    public function factura(Estancia $estancia)
    {
        if (!$this->puedeVerEstancia($estancia)) {
            return redirect()->route('home')->with('error', 'No puedes ver esta estancia.');
        }

        return view('estancias.factura', compact('estancia'));
    }

    //formulario para editar estancia
    public function edit(Estancia $estancia)
    {
        //saber que la estancia es del usuario
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes editar esta estancia.');
        }

        return view('estancias.edit', compact('estancia'));
    }

    //actualizar estancia (acortar o ampliar si hay espacio)
    public function update(Request $request, Estancia $estancia)
    {
        //saber que la estancia es del usuario
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes actualizar esta estancia.');
        }

        //no permitir editar si esta finalizada o cancelada
        if ($estancia->estado == 'finalizada' || $estancia->estado == 'cancelada') {
            return redirect()->route('estancias.index')->with('error', 'No puedes editar una estancia finalizada o cancelada.');
        }

        $hoy = date('Y-m-d');

        //no permitir modificar el mismo dia de salida
        if ($hoy >= $estancia->fecha_salida) {
            return redirect()->route('estancias.index')->with('error', 'No se puede modificar la estancia el día de salida.');
        }

        $request->validate([
            'fecha_salida' => 'required|date|after:' . $estancia->fecha_entrada,
        ]);

        $nuevaSalida = new \DateTime($request->fecha_salida);

        //no permitir salidas en domingo
        if ($nuevaSalida->format('w') == 0) {
            return back()->with('error', 'No se permiten salidas en domingo.');
        }

        //si estaba sin disponibilidad, al cambiar fecha se vuelve a comprobar disponibilidad
        if ($estancia->esSinDisponibilidad()) {

            $estancia->fecha_salida = $request->fecha_salida;

            if (Estancia::hayDisponibilidad($estancia->fecha_entrada, $request->fecha_salida, $estancia->id)) {
                $estancia->estado = 'confirmada';
            } else {
                $estancia->estado = 'sin_disponibilidad';
            }

            $estancia->calcularPrecioTotal();
            $estancia->save();

            if ($estancia->estado == 'confirmada') {
                return redirect()->route('estancias.index')->with('success', 'Estancia actualizada correctamente. Ahora hay disponibilidad y queda confirmada.');
            }

            return redirect()->route('estancias.index')->with('error', 'Estancia actualizada, pero sigue sin haber plazas disponibles para esas fechas.');
        }

        //al ampliar debe haber disponibilidad
        if (!$estancia->puedeAmpliarse($request->fecha_salida)) {
            return back()->with('error', 'No se puede ampliar la estancia, no hay disponibilidad.');
        }

        $estancia->fecha_salida = $request->fecha_salida;
        $estancia->calcularPrecioTotal();
        $estancia->save();

        return redirect()->route('estancias.index')->with('success', 'Estancia actualizada correctamente.');
    }

    //cancelar estancia
    public function cancelar(Estancia $estancia)
    {
        //solo el dueño
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes cancelar esta estancia.');
        }

        $hoy = date('Y-m-d');

        $entrada = date('Y-m-d', strtotime($estancia->fecha_entrada));

        //sin disponibilidad = cancelable y sin cobrar
        if ($estancia->estado == 'sin_disponibilidad') {
            $estancia->cancelarSinCobro('usuario');

            return redirect()->route('estancias.index')->with('success', 'Estancia cancelada correctamente.');
        }

        //pendiente = siempre cancelable y sin penalizar
        if ($estancia->estado == 'pendiente') {
            $estancia->cancelarSinCobro('usuario');

            return redirect()->route('estancias.index')->with('success', 'Estancia cancelada correctamente.');
        }

        //confirmada = cancelable
        if ($estancia->estado == 'confirmada') {

            //si cancela el mismo dia de entrada, se cobra 1 dia
            if ($hoy == $entrada) {
                $estancia->aplicarCancelacionUnDia();

                $estancia->cancelar('usuario');

                return redirect()->route('estancias.index')->with('success', 'Estancia cancelada. Al ser el mismo día de entrada, se cobra 1 día.');
            }

            //si es antes del dia de entrada, se cancela normal, sin penalizar
            if ($hoy < $entrada) {
                $estancia->cancelarSinCobro('usuario');

                return redirect()->route('estancias.index')->with('success', 'Estancia cancelada correctamente.');
            }

            //si ha pasado dia de entrada entrada, no se puede cancelar normalmente
            return redirect()->route('estancias.index')->with('error', 'Ya ha pasado el día de entrada. Contacta con administración.');
        }

        //si no es pendiente, confirmada ni sin disponibilidad, no se puede cancelar
        return redirect()->route('estancias.index')->with('error', 'No puedes cancelar esta estancia. Contacta con administración.');
    }

    //historial de cuidados para dueño, admin o cuidador
    public function historial(Estancia $estancia)
    {
        if (!$this->puedeVerEstancia($estancia)) {
            return redirect()->route('home')->with('error', 'No puedes ver el historial de esta estancia.');
        }

        $hoy = now()->toDateString();
        $ahoraHora = now()->format('H:i:s');

        //REALIZADOS (historial)
        $realizados = $estancia->cuidados()
            ->with('usuario')
            ->where('completado', true)
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->get()
            ->groupBy('fecha');

        $totalRealizados = $estancia->cuidados()->where('completado', true)->count();

        //PENDIENTES (para atrasadas y hoy)
        $listaPendientes = $estancia->cuidados()
            ->where('completado', false)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $atrasadas = [];
        $pendientesHoy = [];

        foreach ($listaPendientes as $cuidado) {
            //atrasada si: fecha menor que hoy o fecha == hoy y hora existe y hora menor que ahora
            $esAtrasada = false;

            if ($cuidado->fecha < $hoy) {
                $esAtrasada = true;
            } elseif ($cuidado->fecha == $hoy && $cuidado->hora && $cuidado->hora < $ahoraHora) {
                $esAtrasada = true;
            }

            if ($esAtrasada) {
                $atrasadas[] = $cuidado;
            } elseif ($cuidado->fecha == $hoy) {
                $pendientesHoy[] = $cuidado;
            }
        }

        $totalAtrasadas = count($atrasadas);
        $totalPendientesHoy = count($pendientesHoy);

        $atrasadas = collect($atrasadas)->groupBy('fecha');
        $pendientesHoy = collect($pendientesHoy)->groupBy('fecha');

        return view('estancias.historial', compact(
            'estancia',
            'hoy',
            'realizados',
            'totalRealizados',
            'atrasadas',
            'totalAtrasadas',
            'pendientesHoy',
            'totalPendientesHoy'
        ));
    }

    //avisos para dueño, admin o cuidador
    public function avisos(Estancia $estancia)
    {
        if (!$this->puedeVerEstancia($estancia)) {
            return redirect()->route('home')->with('error', 'No puedes ver los avisos de esta estancia.');
        }

        $avisos = $estancia->avisos()
            ->with('usuario')
            ->orderByDesc('created_at')
            ->get();

        return view('estancias.avisos', compact('estancia', 'avisos'));
    }

    //comprobar si el usuario puede ver la estancia
    private function puedeVerEstancia(Estancia $estancia)
    {
        $user = auth()->user();

        //admin y cuidador pueden ver cualquier estancia
        if (in_array($user->role, ['admin', 'cuidador'])) {
            return true;
        }

        //el dueño solo puede ver sus propias estancias
        return $estancia->mascota && $estancia->mascota->dueno_id == $user->id;
    }
}