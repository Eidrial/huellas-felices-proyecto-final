<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EstanciaController;
use App\Http\Controllers\EstanciaAdminController;
use App\Http\Controllers\CuidadosController;
use App\Http\Controllers\AvisoController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.estancias.index');
    }

    if ($user->role === 'cuidador') {
        return redirect()->route('cuidador.estancias');
    }

    return redirect()->route('mascotas.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//ayuda
Route::view('/ayuda', 'ayuda')->name('ayuda');

Route::middleware(['auth', 'role:usuario'])->group(function () {
    //ruta para que un usuario vea sus mascotas, necesario estar logueado
    Route::get('/mascotas', [MascotaController::class, 'index'])->name('mascotas.index');
    //ruta para que un usuario cree sus mascotas, necesario estar logueado
    Route::get('/mascotas/crear', [MascotaController::class, 'create'])->name('mascotas.create');
    ///ruta para guardar una nueva mascota enviada desde el formulario, necesario estar logueado
    Route::post('/mascotas', [MascotaController::class, 'store'])->name('mascotas.store');
    //mostrar detalles de una mascota seleccionada
    Route::get('/mascotas/{mascota}', [MascotaController::class, 'show'])->name('mascotas.show');
    //editar una mascota seleccionada
    Route::get('/mascotas/{mascota}/editar', [MascotaController::class, 'edit'])->name('mascotas.edit');
    //guardar cambios de la mascota seleccionada
    Route::put('/mascotas/{mascota}', [MascotaController::class, 'update'])->name('mascotas.update');
    //borrar una mascota del usuario
    Route::delete('/mascotas/{mascota}', [MascotaController::class, 'destroy'])->name('mascotas.destroy');

    //estancias
    //listado de estancias
    Route::get('/estancias', [EstanciaController::class, 'index'])->name('estancias.index');
    //formulario crear estancia
    Route::get('/estancias/crear', [EstanciaController::class, 'create'])->name('estancias.create');
    //guardar estancia
    Route::post('/estancias', [EstanciaController::class, 'store'])->name('estancias.store');
    //editar estancia
    Route::get('/estancias/{estancia}/editar', [EstanciaController::class, 'edit'])->name('estancias.edit');
    //guardar cambios estancia
    Route::put('/estancias/{estancia}', [EstanciaController::class, 'update'])->name('estancias.update');
    //cancelar estancia
    Route::put('/estancias/{estancia}/cancelar', [EstanciaController::class, 'cancelar'])->name('estancias.cancelar');
});

//necesario estar logueado como admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    //mascotas
    //listado de mascotas
    Route::get('/mascotas', [AdminController::class, 'index'])->name('admin.mascotas.index');
    //editar
    Route::get('/mascotas/{mascota}/editar', [AdminController::class, 'editarMascota'])->name('admin.mascotas.editar');
    //actualizar
    Route::put('/mascotas/{mascota}', [AdminController::class, 'actualizarMascota'])->name('admin.mascotas.actualizar');
    //aprobar o no aprobar
    Route::put('/mascotas/{mascota}/aprobar', [AdminController::class, 'aprobar'])->name('admin.mascotas.aprobar');
    //eliminar
    Route::delete('/mascotas/{mascota}', [AdminController::class, 'eliminarMascota'])->name('admin.mascotas.destroy');

    //usuarios
    //mostrar el listado de usuarios registrados
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
    //cambiar el rol de un usuario concreto
    Route::put('/usuarios/{user}/rol', [AdminController::class, 'cambiarRol'])->name('admin.usuarios.rol');
    //borrar usuario
    Route::delete('/usuarios/{user}', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.destroy');
    //crear usuario
    Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
    //guardar usuario
    Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('admin.usuarios.guardar');
    //editar usuario
    Route::get('/usuarios/{user}/editar', [AdminController::class, 'editarUsuario'])->name('admin.usuarios.editar');
    //guardar cambios usuario
    Route::put('/usuarios/{user}', [AdminController::class, 'actualizarUsuario'])->name('admin.usuarios.actualizar');

    //estancias
    //listado de estancias
    Route::get('/estancias', [EstanciaAdminController::class, 'index'])->name('admin.estancias.index');
    //confirmar estancia
    Route::put('/estancias/{estancia}/confirmar', [EstanciaAdminController::class, 'confirmar'])->name('admin.estancias.confirmar');
    //iniciar estancia
    Route::put('/estancias/{estancia}/iniciar', [EstanciaAdminController::class, 'iniciar'])->name('admin.estancias.iniciar');
    //finalizar estancia
    Route::put('/estancias/{estancia}/finalizar', [EstanciaAdminController::class, 'finalizar'])->name('admin.estancias.finalizar');
    //cancelar estancia
    Route::put('/estancias/{estancia}/cancelar', [EstanciaAdminController::class, 'cancelar'])->name('admin.estancias.cancelar');

    //cuidados
    //borrar un extra
    Route::delete('/cuidados/{cuidado}/extra', [CuidadosController::class, 'borrarExtra'])->name('cuidados.borrarExtra');
    //borrar aviso
    Route::delete('/avisos/{aviso}', [AvisoController::class, 'borrarAviso'])->name('avisos.borrarAviso');
});

//necesario estar logueado como admin o cuidador
Route::middleware(['auth', 'role:admin,cuidador'])->group(function () {
    //panel principal cuidados
    Route::get('/cuidados', [CuidadosController::class, 'index'])->name('cuidados.index');
    //detalle de una estancia
    Route::get('/cuidados/estancia/{estancia}', [CuidadosController::class, 'show'])->name('cuidados.show');
    //completar cuidado
    Route::put('/cuidados/{cuidado}/completar', [CuidadosController::class, 'completar'])->name('cuidados.completar');
    //añadir extra
    Route::post('/cuidados', [CuidadosController::class, 'store'])->name('cuidados.store');
    //crear aviso
    Route::post('/avisos', [AvisoController::class, 'store'])->name('avisos.store');
});

//necesario estar logueado como cuidador
Route::middleware(['auth', 'role:cuidador'])->prefix('cuidador')->group(function () {
    //listado de estancias para cuidador
    Route::get('/estancias', [CuidadosController::class, 'estancias'])->name('cuidador.estancias');
    //ver mascotas
    Route::get('/cuidador/mascotas', [CuidadosController::class, 'mascotas'])->name('cuidador.mascotas');
    //ver ficha
    Route::get('/mascotas/{mascota}', [CuidadosController::class, 'showMascota'])->name('cuidador.mascotas.show');
});

//rutas compartidas entre usuario, admin y cuidador
Route::middleware(['auth', 'role:usuario,admin,cuidador'])->group(function () {
    //factura
    Route::get('/estancias/{estancia}/factura', [EstanciaController::class, 'factura'])->name('estancias.factura');
    //historial
    Route::get('/estancias/{estancia}/historial', [EstanciaController::class, 'historial'])->name('estancias.historial');
    //avisos
    Route::get('/estancias/{estancia}/avisos', [EstanciaController::class, 'avisos'])->name('estancias.avisos');
});


require __DIR__ . '/auth.php';
