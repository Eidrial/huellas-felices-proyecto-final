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
        Schema::table('avisos', function (Blueprint $table) {
            //quitar mascota_id (clave foranea y columna)
            $table->dropConstrainedForeignId('mascota_id');
            //añadir estancia_id
            $table->foreignId('estancia_id')->after('id')->constrained()->onDelete('cascade');
            //añadir tipo
            $table->string('tipo')->default('info')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avisos', function (Blueprint $table) {
            //volver a mascota_id
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');

            //quitar lo nuevo
            $table->dropConstrainedForeignId('estancia_id');
            $table->dropColumn('tipo');
        });
    }
};
