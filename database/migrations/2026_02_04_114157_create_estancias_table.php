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
        Schema::create('estancias', function (Blueprint $table) {
            $table->id();

            //relacion con mascota
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');

            //estado de la estancia
            $table->enum('estado', [
                'pendiente', //solicitada pero no aprobada (por valicdacion de mascota o por comprobar disponibilidad)
                'confirmada', //aprobada y pendiente de inicio
                'activa', //estancia en curso
                'finalizada', //estancia terminada
                'cancelada' // ancelada por usuario o admin
            ])->default('pendiente');

            //fechas de estancia
            $table->date('fecha_entrada');
            $table->date('fecha_salida');

            //precios
            $table->decimal('precio_dia', 6, 2); //precio por dia, máximo 9999.99€/dia
            $table->decimal('precio_total', 8, 2)->nullable(); //total estimado o final

            //saber quien ha cancelado la estancia
            $table->enum('cancelada_por', ['usuario', 'admin'])->nullable();

            $table->timestamps();

            //para buscar mas rapido
            $table->index(['fecha_entrada', 'fecha_salida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estancias');
    }
};
