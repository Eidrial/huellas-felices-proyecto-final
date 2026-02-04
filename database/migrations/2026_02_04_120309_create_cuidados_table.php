<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('cuidados', function (Blueprint $table) {
            $table->id();

            //relacion con estancia
            $table->foreignId('estancia_id')->constrained()->onDelete('cascade');

            //tipo de cuidado: paseo, medicacion, alimentacion, juego o extra
            $table->enum('tipo', ['paseo', 'medicacion', 'alimentacion', 'juego', 'extra']);

            $table->text('descripcion')->nullable();
            $table->time('hora')->nullable();

            //marcar si esta completado
            $table->boolean('completado')->default(false);

            //quien lo envia, cuidador/admin
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            //precio extra (solo si tipo == extra)
            $table->decimal('precio_extra', 6, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuidados');
    }
};
