<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id(); //autoincremental

            $table->string('nombre');
            $table->string('especie');
            $table->string('raza');
            $table->integer('edad');
            $table->decimal('peso');
            $table->string('foto')->nullable(); //foto, opcional
            //id del usuario (dueño) es clave foranea que apunta a la tabla users (y si se borra el usuario, también se borran sus mascotas)
            $table->foreignId('dueno_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('aprobado')->nullable()->default(null); // null = pendiente, 1 = aprobada, 0 = no aprobada
            $table->timestamps(); //saber creacion y actualizacion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
