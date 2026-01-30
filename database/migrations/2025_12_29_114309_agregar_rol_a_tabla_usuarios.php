<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //modificar la tabla users para añadir un campo llamado role
        Schema::table('users', function (Blueprint $table) {
            //se crea un tipo ENUM con los posibles roles
            //las opciones seran: admin, cuidador, usuario
            //pr defecto se asigna como usuario usuario
            $table->enum('role', ['admin', 'cuidador', 'usuario'])->default('usuario')
                ->after('password')
                ->comment('Rol del usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //para revertir la migración y eliminar role
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
