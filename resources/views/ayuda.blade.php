@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] text-white rounded-2xl overflow-hidden mb-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>

            <div class="px-7 py-9 md:px-10 md:py-10">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-3">
                    Residencia Huellas Felices
                </p>
                <h1 class="font-serif text-3xl md:text-4xl font-medium text-[#f0ede6] mb-3">
                    Centro de ayuda
                </h1>
                <p class="text-[#9fcf8e] max-w-2xl leading-relaxed text-sm">
                    Aquí encontrarás una guía clara para usar la plataforma: registrar mascotas, reservar estancias,
                    consultar cuidados, revisar avisos y entender facturas o cancelaciones.
                </p>
            </div>
        </div>

        <!-- resumen rapido -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                <div
                    class="w-10 h-10 rounded-xl bg-[#eef5e8] border border-[#c8d9be] flex items-center justify-center mb-3">
                    🐶
                </div>
                <h2 class="font-medium text-[#1e2e1a] mb-1">Usuarios</h2>
                <p class="text-sm text-[#8a8e84] leading-relaxed">
                    Registran mascotas, reservan estancias y consultan historial, avisos y facturas.
                </p>
            </div>

            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                <div
                    class="w-10 h-10 rounded-xl bg-[#e6f0fb] border border-[#b0cef0] flex items-center justify-center mb-3">
                    🦴
                </div>
                <h2 class="font-medium text-[#1e2e1a] mb-1">Cuidadores</h2>
                <p class="text-sm text-[#8a8e84] leading-relaxed">
                    Gestionan tareas diarias, registran cuidados realizados, extras y avisos.
                </p>
            </div>

            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                <div
                    class="w-10 h-10 rounded-xl bg-[#fef8ec] border border-[#e4c57a] flex items-center justify-center mb-3">
                    ⚙️
                </div>
                <h2 class="font-medium text-[#1e2e1a] mb-1">Administración</h2>
                <p class="text-sm text-[#8a8e84] leading-relaxed">
                    Supervisa mascotas, estancias, usuarios, cuidados, cancelaciones y facturas.
                </p>
            </div>
        </div>

        <!-- como funciona -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Cómo funciona la plataforma</h2>

            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                <div class="h-[3px] bg-[#5a9e47]"></div>

                <div class="p-5 md:p-6">
                    <p class="text-sm text-[#3d5c38] leading-relaxed mb-4">
                        Huellas Felices permite gestionar una residencia canina de forma sencilla. El usuario registra
                        sus mascotas y solicita estancias, el equipo revisa, confirma y gestiona la estancia, y durante
                        el alojamiento se registran cuidados, posibles extras y avisos.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-xs text-[#8a8e84] mb-1">Paso 1</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">Registrar mascota</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-xs text-[#8a8e84] mb-1">Paso 2</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">Revisión del equipo</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-xs text-[#8a8e84] mb-1">Paso 3</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">Solicitud de estancia</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-xs text-[#8a8e84] mb-1">Paso 4</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">Seguimiento diario</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- usuario -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Uso como usuario</h2>

            <div class="space-y-4">

                <!-- mascotas -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <div class="h-[3px] bg-[#5a9e47]"></div>
                    <div class="p-5">
                        <h3 class="font-medium text-[#1e2e1a] mb-2">Mis mascotas</h3>
                        <p class="text-sm text-[#3d5c38] leading-relaxed mb-3">
                            Desde esta sección puedes registrar nuevas mascotas, consultar su ficha, editar sus datos
                            y revisar su estado de aprobación.
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <span
                                class="text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#c8d9be]">
                                Aprobada
                            </span>
                            <span
                                class="text-xs px-2.5 py-1 rounded-full bg-[#fef8ec] text-[#7a4e10] border border-[#e4c57a]">
                                Pendiente
                            </span>
                            <span
                                class="text-xs px-2.5 py-1 rounded-full bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]">
                                No aprobada
                            </span>
                        </div>
                    </div>
                </div>

                <!-- estancias -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <div class="h-[3px] bg-[#3a7abf]"></div>
                    <div class="p-5">
                        <h3 class="font-medium text-[#1e2e1a] mb-2">Mis estancias</h3>
                        <p class="text-sm text-[#3d5c38] leading-relaxed mb-3">
                            Aquí puedes reservar una estancia, ver si está pendiente, confirmada, activa, finalizada
                            o cancelada, y acceder al historial, avisos y factura cuando corresponda.
                        </p>

                        <ul class="text-sm text-[#3d5c38] list-disc list-inside space-y-1">
                            <li>Pendiente: la solicitud está esperando revisión.</li>
                            <li>Confirmada: la estancia tiene plaza reservada.</li>
                            <li>Sin disponibilidad: no hay plazas para esas fechas.</li>
                            <li>Activa: la mascota ya está en la residencia.</li>
                            <li>Finalizada: la estancia ha terminado y puede consultarse la factura.</li>
                        </ul>
                    </div>
                </div>

                <!-- cancelaciones -->
                <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-2xl p-5">
                    <h3 class="font-medium text-[#7a4e10] mb-2">Cancelaciones y cobros</h3>
                    <p class="text-sm text-[#7a4e10] leading-relaxed mb-3">
                        Si cancelas una estancia pendiente o confirmada antes del día de entrada, no se aplica cobro.
                        Si cancelas el mismo día de entrada, se cobra 1 día de estancia.
                    </p>
                    <p class="text-xs text-[#8a8e84]">
                        Una vez la estancia está activa, el usuario debe contactar con administración para cualquier cambio.
                    </p>
                </div>

                <!-- facturas -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <div class="h-[3px] bg-[#5a9e47]"></div>
                    <div class="p-5">
                        <h3 class="font-medium text-[#1e2e1a] mb-2">Facturas</h3>
                        <p class="text-sm text-[#3d5c38] leading-relaxed mb-3">
                            La factura muestra los días facturados, el precio por día y los extras añadidos durante
                            la estancia. Si hubo una cancelación con cobro, la factura seguirá disponible.
                        </p>

                        <p class="text-xs text-[#8a8e84]">
                            La factura será enviada al correo electrónico del dueño una vez finalizada la estancia.
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <!-- cuidador -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Uso como cuidador</h2>

            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                <div class="h-[3px] bg-[#3a7abf]"></div>
                <div class="p-5 md:p-6">
                    <p class="text-sm text-[#3d5c38] leading-relaxed mb-4">
                        El panel de cuidados muestra las estancias confirmadas o activas y permite organizar las tareas
                        del día a día.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-sm font-medium text-[#1e2e1a] mb-1">Tareas pendientes</p>
                            <p class="text-xs text-[#8a8e84]">Cuidados programados que aún no se han completado.</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-sm font-medium text-[#1e2e1a] mb-1">Tareas atrasadas</p>
                            <p class="text-xs text-[#8a8e84]">Cuidados que deberían haberse completado antes.</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-sm font-medium text-[#1e2e1a] mb-1">Extras</p>
                            <p class="text-xs text-[#8a8e84]">Los extras son servicios añadidos durante la estancia, como
                                baño, cepillado, veterinario u otros cuidados.
                                Se registran como realizados y se suman al total de la factura.</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-4">
                            <p class="text-sm font-medium text-[#1e2e1a] mb-1">Avisos</p>
                            <p class="text-xs text-[#8a8e84]">Notas informativas o importantes para el dueño.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- admin -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Uso como administrador</h2>

            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                <div class="h-[3px] bg-[#c9821a]"></div>
                <div class="p-5 md:p-6">
                    <p class="text-sm text-[#3d5c38] leading-relaxed mb-4">
                        La administración gestiona el funcionamiento general de la residencia: mascotas, usuarios,
                        estancias, facturas, cancelaciones y seguimiento de cuidados.
                    </p>

                    <ul class="text-sm text-[#3d5c38] list-disc list-inside space-y-1">
                        <li>Aprobar o rechazar mascotas.</li>
                        <li>Confirmar estancias pendientes.</li>
                        <li>Iniciar estancias cuando la mascota entra en la residencia.</li>
                        <li>Finalizar estancias cuando la mascota sale.</li>
                        <li>Cancelar estancias pendientes, confirmadas o activas cuando sea necesario.</li>
                        <li>Consultar facturas de estancias activas, finalizadas o canceladas con cobro.</li>
                        <li>Gestionar usuarios y roles.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- normas -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Normas importantes</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- fechas -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Fechas de estancia</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        La fecha de entrada cuenta como primer día de estancia. La fecha de salida corresponde
                        al día en que la mascota abandona la residencia.
                    </p>
                </div>

                <!-- domingos -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Entradas y salidas</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        No se permiten entradas ni salidas en domingo. Te recomendamos planificar la estancia
                        teniendo en cuenta esta limitación.
                    </p>
                </div>

                <!-- disponibilidad -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Disponibilidad</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        La residencia tiene un límite de plazas (20). Si una estancia coincide con días en los que ya se
                        alcanza
                        el máximo permitido, no podrá confirmarse para esas fechas.
                    </p>
                    <p class="text-xs text-[#8a8e84] mt-2">
                        Solo las estancias confirmadas y activas ocupan plaza.
                    </p>
                </div>

                <!-- sin disponibilidad -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Estancia sin disponibilidad</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        Si no hay plazas para las fechas elegidas, la estancia aparecerá como
                        <strong>Sin disponibilidad</strong>. En ese caso, puedes modificar la fecha de salida o cancelar
                        la solicitud sin coste.
                    </p>
                    <p class="text-xs text-[#8a8e84] mt-2">
                        Si al modificar las fechas vuelve a haber plaza, la estancia podrá quedar confirmada.
                    </p>
                </div>

                <!-- mascotas pendientes -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Mascotas en estado pendiente</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed mb-2">
                        Cuando registras una mascota, pasa por un proceso de revisión antes de ser aprobada.
                    </p>

                    <ul class="text-sm text-[#3d5c38] list-disc list-inside space-y-1">
                        <li>Puedes solicitar estancias aunque esté pendiente.</li>
                        <li>La estancia no se confirmará hasta que la mascota sea aprobada.</li>
                        <li>El equipo puede rechazar la mascota si no cumple los requisitos.</li>
                    </ul>

                    <p class="text-xs text-[#8a8e84] mt-2">
                        Importante: una mascota no aprobada no podrá alojarse en la residencia bajo ninguna circunstancia.
                    </p>
                </div>

                <!-- cancelaciones -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Cancelaciones</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed mb-2">
                        Puedes cancelar una estancia mientras esté pendiente o confirmada.
                    </p>

                    <ul class="text-sm text-[#3d5c38] list-disc list-inside space-y-1">
                        <li>Si cancelas antes del día de entrada → no hay coste.</li>
                        <li>Si cancelas el mismo día → se cobra 1 día de estancia.</li>
                    </ul>

                    <p class="text-xs text-[#8a8e84] mt-2">
                        Una vez iniciada la estancia, cualquier cambio debe gestionarse con el equipo.
                    </p>
                </div>

                <!-- pago -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Pago</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        El pago de la estancia se realiza al entregar la mascota. Los extras se añaden al total de la
                        factura y deberán abonarse al recoger al perro.
                    </p>
                </div>

                <!-- cuidados -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5">
                    <h3 class="font-medium text-[#1e2e1a] mb-2">Registro de cuidados</h3>
                    <p class="text-sm text-[#3d5c38] leading-relaxed">
                        Los cuidadores registran los cuidados durante la estancia. Las tareas solo pueden
                        marcarse como realizadas en su horario correspondiente para asegurar un seguimiento correcto.
                    </p>
                </div>

            </div>
        </section>

        <!-- problemas frecuentes -->
        <section class="mb-10">
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">Problemas frecuentes</h2>

            <div class="space-y-3">
                <details class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <summary class="cursor-pointer px-5 py-4 text-sm font-medium text-[#1e2e1a]">
                        No puedo borrar una mascota.
                    </summary>
                    <div class="border-t border-[#f0ede6] px-5 py-4">
                        <p class="text-sm text-[#3d5c38] leading-relaxed">
                            No se puede borrar una mascota si tiene una estancia pendiente, confirmada o activa.
                            Primero debe cancelarse la estancia o esperar a que finalice.
                        </p>
                    </div>
                </details>

                <details class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <summary class="cursor-pointer px-5 py-4 text-sm font-medium text-[#1e2e1a]">
                        No puedo completar un cuidado.
                    </summary>
                    <div class="border-t border-[#f0ede6] px-5 py-4">
                        <p class="text-sm text-[#3d5c38] leading-relaxed">
                            Las tareas solo pueden completarse cuando llega su franja horaria (15 minutos antes de la
                            hora programada). Si todavía no está
                            disponible, el sistema mostrará cuándo podrá marcarse como realizada.
                        </p>
                    </div>
                </details>

                <details class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <summary class="cursor-pointer px-5 py-4 text-sm font-medium text-[#1e2e1a]">
                        Mi estancia no se confirma automáticamente.
                    </summary>
                    <div class="border-t border-[#f0ede6] px-5 py-4">
                        <p class="text-sm text-[#3d5c38] leading-relaxed">
                            Puede ocurrir si la mascota todavía está pendiente de aprobación o si la estancia aparece como
                            sin disponibilidad porque no quedan plazas para esas fechas.
                        </p>
                    </div>
                </details>

                <details class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <summary class="cursor-pointer px-5 py-4 text-sm font-medium text-[#1e2e1a]">
                        ¿Por qué aparece una factura en una estancia cancelada?
                    </summary>
                    <div class="border-t border-[#f0ede6] px-5 py-4">
                        <p class="text-sm text-[#3d5c38] leading-relaxed">
                            Porque algunas cancelaciones pueden tener cobro aplicado, por ejemplo si se cancela el mismo
                            día de entrada o si la mascota ya estaba en la residencia.
                        </p>
                    </div>
                </details>

                <details class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <summary class="cursor-pointer px-5 py-4 text-sm font-medium text-[#1e2e1a]">
                        ¿Qué puedo hacer si mi estancia aparece <strong>Sin disponibilidad</strong>?
                    </summary>
                    <div class="border-t border-[#f0ede6] px-5 py-4">
                        <p class="text-sm text-[#3d5c38] leading-relaxed">
                            Puedes modificar la fecha de salida para buscar una franja con plazas disponibles o cancelar la
                            solicitud
                            sin coste. Si al cambiar las fechas hay disponibilidad, la estancia podrá confirmarse.
                        </p>
                    </div>
                </details>
            </div>
        </section>

        <!-- contacto -->
        <section>
            <h2 class="font-serif text-2xl font-medium text-[#1e2e1a] mb-4">¿Necesitas ayuda?</h2>

            <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-2xl p-5">
                <p class="text-sm text-[#2d5a27] leading-relaxed mb-3">
                    Si no encuentras la solución aquí, puedes contactar con el equipo de Huellas Felices.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white/60 rounded-xl p-4">
                        <p class="text-xs text-[#8a8e84] mb-1">Email</p>
                        <p class="text-sm font-medium text-[#1e2e1a] break-all">residenciahuellasfelices@gmail.com</p>
                    </div>

                    <div class="bg-white/60 rounded-xl p-4">
                        <p class="text-xs text-[#8a8e84] mb-1">Teléfono</p>
                        <p class="text-sm font-medium text-[#1e2e1a]">722 72 72 72</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection