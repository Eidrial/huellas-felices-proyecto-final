<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\AdminController;

// name('home') para poder referirnos a ella con facilidad  desde vistas o controladores
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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


});

require __DIR__ . '/auth.php';
