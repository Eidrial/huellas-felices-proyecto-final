<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('estancias', function (Blueprint $table) {
            //descripcion de la medicacion
            $table->string('medicacion_descripcion')->nullable()->after('precio_total');

            //horas separadas por coma (ej: 09:00,21:00)
            $table->string('medicacion_horas')->nullable()->after('medicacion_descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('estancias', function (Blueprint $table) {
            $table->dropColumn('medicacion_descripcion');
            $table->dropColumn('medicacion_horas');
        });
    }
};